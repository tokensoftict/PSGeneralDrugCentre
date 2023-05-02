<?php

namespace App\Console\Commands;

use App\Classes\Settings;
use App\Models\Invoiceitembatch;
use App\Models\Nearoutofstock;
use App\Models\Purchaseitem;
use App\Models\Stock;
use App\Models\Stockgroup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NearOSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nearos:compute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute Near Out of stock for PS GDC';

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
        $department = 'others';

        $departments = self::$department[$department];

        $store = $settings->store();

        if($store->nearos_status === "backgroundprocess"){
            $this->info("Near Os is already running");
            return "already running";
        }

        $settings->put('nearos_status', 'backgroundprocess');

        Nearoutofstock::truncate();

        $day = $store->threshold_days;
        $supply_days = $store->supply_days;
        $threshold_day = $store->qty_to_buy_threshold;
        $from =  date('Y-m-d', strtotime(' - '.$day.' days'));
        $to = todaysDate();


        Stock::where('status',"1")->where('reorder',1)->chunk(200,function($stocks) use(&$day,&$supply_days,&$threshold_day,&$from,&$to, &$departments, &$department){
            foreach ($stocks as $stock) {
                $qty = 0;
                $_product = Invoiceitembatch::select(
                    'stock_id',
                    DB::raw('SUM(quantity) as qty')
                )
                    ->whereIn('department',  $departments)
                    ->where('stock_id', $stock->id)
                    ->whereHas('invoice', function ($q) use (&$from, $to) {
                        $q->whereBetween('invoice_date', [$from, $to]);
                    })
                    ->groupBy('stock_id')
                    ->get();
                if ($_product->count() > 0) {
                    $p = $_product->first()->toArray();
                    $qty+=$p['qty'];
                }



                if($_product->count() > 0) {
                    $p = $_product->first()->toArray();
                    $qty+=round(abs($p['qty']/$_product->first()->stock->box));
                }
                $thresholad_score = round(abs(($qty/$day) * $supply_days));

                $now_qty = $stock->totalBalance();

                $po = Purchaseitem::where('stock_id',$stock->id)->whereHas('purchase',function($q){
                    $q->where('status_id','6');
                })
                    ->orderBy('id','DESC')
                    ->limit(1)->first();


                $last_supplier = $stock->stockBatches()->orderBy('id','DESC')->limit(1)->first();


                if($thresholad_score > $now_qty){
                    $qty_to_buy = $qty * $threshold_day;
                    //trigger insertion
                    $insert = [
                        'stock_id'=>$stock->id,
                        'threshold_type'=>"THRESHOLD",
                        'os_type'=>'SINGLE',
                        'last_qty_purchased'=>($po->qty ?? NULL),
                        'last_purchase_date'=>($po->po->date_completed ?? NULL),
                        'qty_to_buy'=> $qty_to_buy,
                        'current_sold'=>$qty,
                        'is_grouped'=>($stock->stockgroup_id ? 1 : 0),
                        'group_os_id'=>$stock->stockgroup_id,
                        'last_po_batch'=>($po->id ?? NULL),
                        'threshold_value'=> $thresholad_score,
                        'current_qty'=> $stock->totalBalance(),
                        'supplier_id'=> $last_supplier->supplier_id ?? NULL
                    ];
                    Nearoutofstock::create($insert);
                    continue;
                }else if($now_qty < 2){
                    $qty_to_buy = $qty * $threshold_day;
                    $insert = [
                        'stock_id'=>$stock->id,
                        'threshold_type'=>"NORMAL",
                        'os_type'=>'SINGLE',
                        'last_qty_purchased'=>($po->qty ?? NULL),
                        'last_purchase_date'=>($po->po->date_completed ?? NULL),
                        'qty_to_buy'=> $qty_to_buy,
                        'current_sold'=>$qty,
                        'last_po_batch'=>($po->id ?? NULL),
                        'is_grouped'=>($stock->stockgroup_id ? 1 : 0),
                        'group_os_id'=>$stock->stockgroup_id,
                        'threshold_value'=> $thresholad_score,
                        'current_qty'=> $stock->totalBalance(),
                        'supplier_id'=>  $last_supplier->supplier_id ?? NULL
                    ];

                    Nearoutofstock::create($insert);
                    continue;
                }else{
                    //remember you did not create migration for this
                    //threshold_type to be NOT-NORMAL
                    // You add it has a quick fix to your logic
                    $qty_to_buy = $qty * $threshold_day;
                    $insert = [
                        'stock_id'=>$stock->id,
                        'threshold_type'=>"NOT-NORMAL",
                        'os_type'=>'SINGLE',
                        'last_qty_purchased'=>($po->qty ?? NULL),
                        'last_purchase_date'=>($po->po->date_completed ?? NULL),
                        'qty_to_buy'=> $qty_to_buy,
                        'current_sold'=>$qty,
                        'last_po_batch'=>($po->id ?? NULL),
                        'is_grouped'=>($stock->stockgroup_id ? 1 : 0),
                        'group_os_id'=>$stock->stockgroup_id,
                        'threshold_value'=> $thresholad_score,
                        'current_qty'=> $stock->totalBalance(),
                        'supplier_id'=>  $last_supplier->supplier_id ?? NULL
                    ];
                   Nearoutofstock::create($insert);
                    continue;
                }
            }
        });



        $groups = Stockgroup::where('status',"1")->get();
        foreach($groups as $group) {
            //if (\App\RetailNearOs::where('group_os_id', $group->id)->get()->count() > 0) {
            $qty = 0;
            $now_qty = 0;
            $_product = DB::table('invoiceitembatches as batch_item')
                ->select(DB::raw('SUM(batch_item.quantity) as qty'))
                ->join('stocks as st', 'st.id', '=', 'batch_item.stock_id')
                ->join('stockgroups as group', 'group.id', '=', 'st.stockgroup_id')
                ->join('invoices as invoices', 'invoices.id', '=', 'batch_item.invoice_id')
                ->where('st.stockgroup_id', '=', $group->id)
                ->where('st.status', '=', 1)
                ->where('st.reorder', '=', 1)
                ->where('batch_item.department', "!=", "retail")
                ->whereBetween('invoices.invoice_date', [$from, $to])
                ->groupBy('st.stockgroup_id')
                ->get();
            if ($_product->count() > 0) {
                foreach ($_product as $_prod) {
                    $qty += $_prod->qty;
                    $now_qty += $group->totalBalance();
                }
            }


            $thresholad_score = round(abs(($qty / $day) * $supply_days));

            $poItem = $group->getlastpo_item();

            if ($thresholad_score > $now_qty) {
                $qty_to_buy = $qty * $threshold_day;
                $insert = [
                    'stock_id' => NULL,
                    'stockgroup_id' => $group->id,
                    'last_po_batch' =>($poItem->id ?? NULL),
                    'threshold_type' => "THRESHOLD",
                    'os_type' => 'GROUP',
                    'qty_to_buy' => $qty_to_buy,
                    'current_sold' => $qty,
                    'threshold_value' => $thresholad_score,
                    'current_qty' => $now_qty,
                ];
                Nearoutofstock::create($insert);
                continue;
            }

            if ($now_qty < 2) {
                $qty_to_buy = $qty * $threshold_day;
                $insert = [
                    'stock_id' => NULL,
                    'stockgroup_id' => $group->id,
                    'threshold_type' => "NORMAL",
                    'os_type' => 'GROUP',
                    'last_po_batch' =>($poItem->id ?? NULL),
                    'qty_to_buy' => $qty_to_buy,
                    'current_sold' => $qty,
                    'threshold_value' => $thresholad_score,
                    'current_qty' => $now_qty,
                ];
                Nearoutofstock::create($insert);
                continue;
            }
        }

        $settings->put('nearos_status', 'okay');

        return Command::SUCCESS;
    }
}
