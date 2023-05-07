<?php

namespace App\Console\Commands;

use App\Classes\Settings;
use App\Models\Invoiceitembatch;
use App\Models\Purchaseitem;
use App\Models\Retailnearoutofstock;
use App\Models\Stock;
use App\Models\Stockgroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RetailNearOSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retailnearos:compute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute Retail Near Out of stock for PS GDC';

    public static $department = [
        'retail' => ['retail'],
        'others' =>   [
            "bulksales",
            "quantity",
            "wholesales",
        ]
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Settings $settings)
    {

        $store = $settings->store();

        if($store->retail_nearos_status === "backgroundprocess"){
            $this->info("Retail Near Os is already running");
            return "already running";
        }

        $settings->put('retail_nearos_status', 'backgroundprocess');

        Retailnearoutofstock::truncate();

        $day = $store->retail_threshold_days;
        $supply_days = $store->retail_supply_days;
        $threshold_day = $store->retail_qty_to_buy_threshold;
        $from =  date('Y-m-d', strtotime(' - '.$day.' days'));
        $to = todaysDate();



        $stocks = Stock::where('status',"1")->where("retail_price",">",0)->where('reorder',1)->get();
        foreach($stocks as $stock){
            //for retail department
            $qty =0;
            $_product =   Invoiceitembatch::select(
                'stock_id',
                DB::raw( 'SUM(quantity) as qty')
            )
                ->where('department',"retail")
                ->where('stock_id',$stock->id)
                ->whereHas('invoice',function($q) use(&$from,$to){
                    $q->whereBetween('invoice_date',[$from,$to]);
                })
                ->groupBy('stock_id')
                ->get();
            if($_product->count() > 0) {
                $p = $_product->first()->toArray();
                $qty+=round(abs(
                    divide($p['qty'], $_product->first()->stock->box)
                ));
            }
            $thresholad_score = round(abs(($qty/$day) * $supply_days));

            $now_qty = $stock->retail;

            //for last qty purchased
            //for last qty purchased
            $po = Purchaseitem::where('stock_id',$stock->id)->whereHas('purchase',function($q){
                $q->where('status_id',status('Complete'));
            })
                ->orderBy('id','DESC')
                ->limit(1)->first();

            if($thresholad_score > $now_qty){
                $qty_to_buy = $qty * $threshold_day;
                $last_supplier = $stock->stockBatches()->orderBy('id','DESC')->get()->first();
                //trigger insertion
                $insert = [
                    'stock_id'=>$stock->id,
                    'threshold_type'=>"THRESHOLD",
                    'os_type'=>'SINGLE',
                    'last_qty_purchased'=>(isset($po->qty) ? $po->qty : NULL),
                    'last_purchase_date'=>(isset($po->purchase->date_completed) ? $po->purchase->date_completed : NULL),
                    'qty_to_buy'=> $qty_to_buy,
                    'current_sold'=>$qty,
                    'is_grouped'=>($stock->stockgroup_id ? 1 : 0),
                    'group_os_id'=>$stock->stockgroup_id,
                    'last_po_batch'=>(isset($po->id) ? $po->id : NULL),
                    'threshold_value'=> $thresholad_score,
                    'current_qty'=> $stock->retail,
                    'supplier_id'=> !empty($last_supplier->supplier_id) ? $last_supplier->supplier_id : NULL
                ];
               Retailnearoutofstock::create($insert);
                continue;
            }else if($now_qty < 2){
                $qty_to_buy = $qty * $threshold_day;
                $last_supplier = $stock->stockBatches()->orderBy('id','DESC')->get()->first();
                $insert = [
                    'stock_id'=>$stock->id,
                    'threshold_type'=>"NORMAL",
                    'os_type'=>'SINGLE',
                    'last_qty_purchased'=>(isset($po->qty) ? $po->qty : NULL),
                    'last_purchase_date'=>(isset($po->purchase->date_completed) ? $po->purchase->date_completed : NULL),
                    'qty_to_buy'=> $qty_to_buy,
                    'current_sold'=>$qty,
                    'is_grouped'=>($stock->stockgroup_id ? 1 : 0),
                    'group_os_id'=>$stock->stockgroup_id,
                    'last_po_batch'=>(isset($po->id) ? $po->id : NULL),
                    'threshold_value'=> $thresholad_score,
                    'current_qty'=> $stock->retail,
                    'supplier_id'=> !empty($last_supplier->supplier_id) ? $last_supplier->supplier_id : NULL
                ];
                Retailnearoutofstock::create($insert);
                continue;
            }else{
                $qty_to_buy = $qty * $threshold_day;
                $last_supplier = $stock->stockBatches()->orderBy('id','DESC')->get()->first();
                $insert = [
                    'stock_id'=>$stock->id,
                    'threshold_type'=>"NOT-NORMAL",
                    'os_type'=>'SINGLE',
                    'last_qty_purchased'=>(isset($po->qty) ? $po->qty : NULL),
                    'last_purchase_date'=>(isset($po->purchase->date_completed) ? $po->purchase->date_completed : NULL),
                    'qty_to_buy'=> $qty_to_buy,
                    'current_sold'=>$qty,
                    'is_grouped'=>($stock->stockgroup_id ? 1 : 0),
                    'group_os_id'=>$stock->stockgroup_id,
                    'last_po_batch'=>(isset($po->id) ? $po->id : NULL),
                    'threshold_value'=> $thresholad_score,
                    'current_qty'=> $stock->retail,
                    'supplier_id'=> !empty($last_supplier->supplier_id) ? $last_supplier->supplier_id : NULL
                ];
                Retailnearoutofstock::create($insert);
                continue;
            }
        }


        //query product by group
        $groups = Stockgroup::where('status',"1")->get();
        foreach($groups as $group){
            //if(\App\RetailNearOs::where('group_os_id',$group->id)->get()->count() > 0) {
            $qty = 0;
            $now_qty = 0;
            //for retail department
            $_product = DB::table('invoiceitembatches as batch_item')
                ->select(DB::raw('SUM(batch_item.quantity/st.box) as qty'))
                ->join('stocks as st', 'st.id', '=', 'batch_item.stock_id')
                ->where( 'st.retail_price', '>', 0)
                ->where('st.status', '=', 1)
                ->where('st.reorder', '=', 1)
                ->join('stockgroups as group', 'group.id', '=', 'st.stockgroup_id')
                ->join('invoices as invoices', 'invoices.id', '=', 'batch_item.invoice_id')
                ->where('st.stockgroup_id', '=', $group->id)
                ->where('batch_item.department', "retail")
                ->whereBetween('invoices.invoice_date', [$from, $to])
                ->groupBy('st.stockgroup_id')
                ->get();

            if ($_product->count() > 0) {
                foreach ($_product as $_prod) {
                    $qty += $_prod->qty;
                    $now_qty += $group->totalRetailBalance();
                }
            }

            $thresholad_score = round(abs(($qty / $day) * $supply_days));

            $poItem = $group->getlastpo_item();

            if ($thresholad_score > $now_qty) {
                $qty_to_buy = $qty * $threshold_day;
                $insert = [
                    'stockgroup_id' => $group->id,
                    'last_po_batch' =>(isset($poItem->id) ? $poItem->id : NULL),
                    'threshold_type' => "THRESHOLD",
                    'os_type' => 'GROUP',
                    'qty_to_buy' => $qty_to_buy,
                    'current_sold' => $qty,
                    'threshold_value' => $thresholad_score,
                    'current_qty' => $now_qty,
                ];
                Retailnearoutofstock::create($insert);
                continue;
            }

            if ($now_qty < 2) {
                $qty_to_buy = $qty * $threshold_day;
                $insert = [
                    'stockgroup_id' => $group->id,
                    'threshold_type' => "NORMAL",
                    'os_type' => 'GROUP',
                    'last_po_batch' =>(isset($poItem->id) ? $poItem->id : NULL),
                    'qty_to_buy' => $qty_to_buy,
                    'current_sold' => $qty,
                    'threshold_value' => $thresholad_score,
                    'current_qty' => $now_qty,
                ];
               Retailnearoutofstock::create($insert);
                continue;
            }
        }





        $settings->put('retail_nearos_status', 'okay');
        $settings->put('retail_nearos_last_run', Carbon::now()->toDateTimeLocalString());

        return Command::SUCCESS;
    }
}
