<?php
namespace App\Traits;

use App\Jobs\AddLogToProductBinCard;
use App\Jobs\PushDataServer;
use App\Models\Invoice;
use App\Models\Stock;
use App\Models\Stockbatch;
use App\Models\Stocktransfer;
use Arr;
use Illuminate\Support\Facades\Auth;

trait StockModelTrait
{

    public function activeBatches()
    {
        $departments = departments(true)->filter(function($item){
            return $item->quantity_column !== NULL;
        });

        return $this->stockbatches()->where(function($query) use ($departments){
            foreach ($departments as $department)
            {
                if($department->quantity_column == NULL) continue;
                $query->orWhere($department->quantity_column,">", 0);
            }
        })->orderBy("expiry_date", "ASC");

    }

    public function minimumBatches()
    {
        return $this->stockbatches()->orderBy('received_date', 'DESC')->limit(3);
    }

    public static function removeSaleableBatches(Invoice $invoice ,$batches, $columns = []){

        Stockbatch::upsert($batches, ['id'], $columns);

        Stock::whereIn('id', Arr::pluck($invoice->invoiceitems->toArray(), 'stock_id'))->get()->each->updateQuantity();

        $bincards = [];

        $invoice->invoiceitems->each(function($stock) use(&$bincards, $invoice){
            $bincards[] =  [
                'bin_card_type'=>"APP//SOLD",
                'bin_card_date'=>todaysDate(),
                'user_id'=>Auth::id(),
                'sold_qty'=>$stock->quantity,
                'stock_id'=>$stock->stock_id,
                'stockbatch_id'=>NULL,
                'from_department'=>$invoice->department,
                'invoice_id'=>$invoice->id,
                'comment'=>"Stock Sold Invoice Number :".$invoice->invoice_number." by ".Auth::user()->name,
                'balance'=>$stock->stock->totalBalance(),
                'department_balance'=>$stock->stock->getCurrentlevel($invoice->department)
            ];
        });

        dispatch(new AddLogToProductBinCard($bincards));

        return true;
    }


    public static function returnStocks(Invoice $invoice, $batches, $columns = [])
    {
        Stockbatch::upsert($batches, ['id'], $columns);

        Stock::whereIn('id', Arr::pluck($invoice->invoiceitems->toArray(), 'stock_id'))->get()->each->updateQuantity();


        if($invoice->department !=="bulksales") {
            $cards = $invoice->invoiceitembatches->map(function ($item) {
                return [
                    'bin_card_type' => "APP//RETURN",
                    'bin_card_date' => todaysDate(),
                    'user_id' => auth()->id(),
                    'stock_id' => $item->stock_id,
                    'return_qty' => $item->quantity,
                    'stockbatch_id' => $item->stockbatch_id,
                    'to_department' => $item->department,
                    'comment' => "Stock Returned Invoice : by " . Auth::user()->name,
                    'balance' => $item->stock->totalBalance(),
                    'department_balance' => $item->stock->{$item->department}
                ];
            })->toArray();

            dispatch(new AddLogToProductBinCard($cards));
        }

    }


    public function pingStockLocation($online_quantity, $activeBatches = false, $departments = []) : array|bool
    {
        if($activeBatches === false){

            $activeBatches = $this->activeBatches;
        }

        $allbatches = [];

        foreach ($departments as $department){

            $batches = $this->pingSaleableBatches($department, $online_quantity, $activeBatches);

            if($batches !== false) {

                $qty = 0;
                foreach ($batches as $batch){

                    $qty+=$batch['qty'];
                    $allbatches[] = $batch;
                }

                $online_quantity= $online_quantity-$qty;
            }

            if($online_quantity === 0) break;
        }

        if(count($allbatches) === 0) {
            return false;
        }

        if($online_quantity > 0)  {
            return false;
        }

        return $allbatches;
    }

    public function pingSaleableBatches($from, $qty, $activeBatches = false){

        if($activeBatches === false){

            $activeBatches = $this->activeBatches;
        }

        if($this->{$from} < $qty) return false;

        $neededBatches = [];
        if($from == 'retail') {
            $cost_price_column = cost_price_column(4);
        }else{
            $cost_price_column = cost_price_column();
        }

        foreach ($activeBatches as $batch)
        {
            if($batch->{$from} - $qty < 0){
                $qty = $qty - $batch->{$from};
                $neededBatches[] = [
                    'id' => $batch->id,
                    $from => 0,
                    'qty' => $batch->{$from},
                    'cost_price' => $batch->{$cost_price_column},
                    'department' => $from
                ];
            }else{
                $newqty = $batch->{$from} - $qty;
                $neededBatches[] = [
                    'id' => $batch->id,
                    $from => $newqty,
                    'cost_price' => $batch->{$cost_price_column},
                    'department' => $from,
                    'qty' => $qty
                ];
                $qty = 0;
            }

            if($qty === 0) return $neededBatches;
        }

        if(count($neededBatches) > 0 && $qty === 0) return  $neededBatches;

        return false;
    }


    public function pingTransferStock($from, $to, $qty)
    {
        if($this->{$from} < $qty) return false;

        $cost_price = $to === 'retail' ? 'retail_cost_price' : 'cost_price';

        $neededBatches = [];

        foreach ($this->activeBatches as $batch)
        {
            if($batch->{$from} - $qty < 0){
                $qty = $qty - $batch->{$from};
                $neededBatches[] = [
                    'id' => $batch->id,
                    $from => 0,
                    $to =>  ($to !=='retail' ?  $batch->{$from} :  $batch->{$from} * $this->box),
                    $cost_price => ($to !== 'retail') ? $batch->{$cost_price} : round(abs($batch->cost_price / $this->box))
                ];
            }else{
                $newqty = $batch->{$from} - $qty;
                $neededBatches[] = [
                    'id' => $batch->id,
                    $from => $newqty ,
                    $to =>  ($to !=='retail' ? ($batch->{$to} + $qty) :  $batch->{$to} + ($qty * $this->box)),
                    $cost_price => ($to !== 'retail') ? $batch->{$cost_price} : round(abs($batch->cost_price / $this->box))
                ];
                $qty = 0;
            }

            if($qty === 0) return $neededBatches;
        }

        if(count($neededBatches) > 0 && $qty === 0) return  $neededBatches;

        return false;
    }


    public function updateQuantity()
    {
        $depts = departments(true)->filter(function($item){
            return $item->quantity_column !== NULL;
        });

        foreach ($depts as $dept) {
            $this->{$dept->quantity_column} =  $this->stockbatches->sum($dept->quantity_column);
            $this->update();
        }
    }


    public static function completeTransfer(array $batches, Stocktransfer $stocktransfer)
    {
        $cost_price =  $stocktransfer->to === 'retail' ? 'retail_cost_price' : 'cost_price';

        Stockbatch::upsert($batches, ['id'], [$stocktransfer->from, $stocktransfer->to, $cost_price]);

        $stocks = Stock::whereIn('id', Arr::pluck($stocktransfer->stocktransferitems->toArray(), 'stock_id'))->get()->each->updateQuantity();

        $bincards = [];

        $stocktransfer->stocktransferitems->each(function ($items) use(&$bincards, &$stocktransfer){
             $bincards[] = [
                 'bin_card_type'=>'APP//TRANSFER',
                 'bin_card_date'=>todaysDate(),
                 'user_id'=>Auth::id(),
                 'out_qty'=>$items->quantity,
                 'stock_id'=>$items->stock_id,
                 'stockbatch_id'=>NULL,
                 'from_department'=>$items->stocktransfer->from,
                 'to_department'=>$items->stocktransfer->to,
                 'stocktransfer_id'=>$stocktransfer->id,
                 'comment'=>"Stock Transfer Transfer ID : ".$items->stocktransfer_id." by ".Auth::user()->name,
                 'balance'=>$items->stock->totalBalance(),
                 'department_balance'=>$items->stock->getCurrentlevel($items->stocktransfer->from)
             ];

            $bincards[] = [
                'bin_card_type'=>'APP//RECEIVED',
                'bin_card_date'=>todaysDate(),
                'user_id'=>Auth::id(),
                'in_qty'=>$items->quantity,
                'stock_id'=>$items->stock_id,
                'stockbatch_id'=>NULL,
                'from_department'=>$items->stocktransfer->from,
                'to_department'=>$items->stocktransfer->to,
                'stocktransfer_id'=>$stocktransfer->id,
                'comment'=>"Stock Received Transfer ID : ".$items->stocktransfer_id." by ".Auth::user()->name,
                'balance'=>$items->stock->totalBalance(),
                'department_balance'=>$items->stock->getCurrentlevel($items->stocktransfer->to)
            ];

         });

        dispatch(new AddLogToProductBinCard($bincards));

        return true;
    }



    public function getBulkPushData() : array{
        $data =  [
            'local_stock_id'=>$this->id,
            'description'=>$this->description,
            'name'=>$this->name,
            'classification_id'=>$this->classification_id,
            'category_id'=>$this->category_id,
            'brand_id'=>$this->brand_id,
            'manufacturer_id'=>$this->manufacturer_id,
            'group_id'=>$this->stockgroup_id,
            'price'=>$this->bulk_price,
            'quantity'=>$this->getOnlineQuantity(),
            'box'=>$this->box,
            'is_wholesales'=>($this->bulk_price > 0 ? 1 : 0 ),
            'max'=>"0",
            'cartoon'=>$this->carton,
            'sachet'=>1,
            //'status'=>$this->status,
            //'retail_status'=>$this->status,
        ];

        $ex = $this->getOnlineExpiryDate();
        if( $ex ) {
            $data['expiry_date'] =  $ex;
        }

        // for OnlineSuperMarket Push
        if($this->retail_price > 0){

            $data['retail_price'] = $this->retail_price;

            $data['retail_quantity'] = $this->retail;
        }

        return $data;
    }


    public function getOnlineExpiryDate()
    {
        $batch = $this->activeBatches->filter(function($item, $index){
            return ($item->wholesales > 0 || $item->bulksales > 0 || $item->quantity > 0);
        })->first();

        if($batch) return $batch->expiry_date;

        return false;
    }

    public function getOnlineQuantity()
    {
        return $this->wholesales + $this->bulksales + $this->quantity;
    }

    public function getCurrentlevel($department)
    {
        return $this->{$department};
    }

    public function totalBalance()
    {
        return  $this->wholesales + $this->bulksales + $this->quantity + abs(round($this->retail/$this->box));
    }

    public function newonlinePush()
    {
        if($this->bulk_price > 0 || $this->retail_price > 0) {
            dispatch(new PushDataServer(['action' => 'new', 'table' => 'stock', 'data' => $this->getBulkPushData(), 'url'=>onlineBase()."dataupdate/add_or_update_stock"]));
        }

    }

    public function updateonlinePush()
    {
        if(($this->bulk_price > 0 || $this->retail_price > 0)  && !$this->isDirty('batched')) {
            dispatch(new PushDataServer(['action' => 'update', 'table' => 'stock', 'data' => $this->getBulkPushData(), 'url'=>onlineBase()."dataupdate/add_or_update_stock"]));
        }
    }




}
