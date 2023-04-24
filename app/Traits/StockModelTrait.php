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
        $departments = departments(true)->filter(function($item){
            return $item->quantity_column !== NULL;
        });

        return $this->stockbatches()->orderBy('received_date', 'DESC')
            ->where(function($query) use($departments){
                foreach ($departments as $department)
                {
                    if($department->quantity_column == NULL) continue;
                    $query->orWhere($department->quantity_column,">", 0);
                }
            });
    }

    public static function removeSaleableBatches(Invoice $invoice ,$batches, $columns = []){

        //Stockbatch::upsert($batches, ['id'], $columns);

        $bincards = [];

        foreach ($batches as $batch)
        {
            $b =  Stockbatch::with(['stock'])->find($batch['id']);
            $b->{$batch['department']} =  $batch[$batch['department']];
            $b->update();
            $b->stock->updateQuantity();
        }

        //Stock::whereIn('id', Arr::pluck($invoice->invoiceitems->toArray(), 'stock_id'))->get()->each->updateQuantity();

        if($invoice->department !=="bulksales") {
            $comment = "Stock Sold from online - Invoice Number :".$invoice->invoice_number." by ". auth()->user()->name;
        }else{
            $comment =  "Stock Sold Invoice Number :".$invoice->invoice_number." by ". auth()->user()->name;
        }

        $invoice->invoiceitembatches()->get()->each(function($stock) use(&$bincards, $invoice, &$comment){
            $bincards[] =  [
                'bin_card_type'=>"APP//SOLD",
                'bin_card_date'=>todaysDate(),
                'user_id'=>Auth::id(),
                'sold_qty'=>$stock->quantity,
                'stock_id'=>$stock->stock_id,
                'stockbatch_id'=>$stock->stockbatch_id,
                'from_department'=>$stock->department,
                'invoice_id'=>$invoice->id,
                'comment'=>$comment,
                'balance'=>$stock->stock->totalBalance(),
                'department_balance'=>$stock->stock->getCurrentlevel($stock->department)
            ];
        });

        dispatch(new AddLogToProductBinCard($bincards));

        return true;
    }


    public static function returnStocks(Invoice $invoice, $batches, $columns = [])
    {
        //Stockbatch::upsert($batches, ['id'], $columns);

        foreach ($batches as $batch)
        {
            $b =  Stockbatch::with(['stock'])->find($batch['id']);
            $b->{$batch['department']} =  $batch[$batch['department']];
            $b->update();
            $b->stock->updateQuantity();
        }


        //Stock::whereIn('id', Arr::pluck($invoice->invoiceitems->toArray(), 'stock_id'))->get()->each->updateQuantity();

        $cards = [];
        if($invoice->department !=="bulksales") {
            $comment =  "Stock Returned Invoice : by " .auth()->user()->name;
        }else{
            $comment =  "Stock Returned from Online Invoice Deleted Because of repacking or re-process : by ".auth()->user()->name;
        }
        $invoice->invoiceitembatches()->get()->each(function ($item) use(&$invoice, &$cards, &$comment) {
            $cards[] = [
                'bin_card_type' => "APP//RETURN",
                'bin_card_date' => todaysDate(),
                'user_id' => auth()->id(),
                'stock_id' => $item->stock_id,
                'return_qty' => $item->quantity,
                'stockbatch_id' => $item->stockbatch_id,
                'to_department' => $item->department,
                'comment' => $comment,
                'balance' => $item->stock->totalBalance(),
                'department_balance' => $item->stock->{$item->department}
            ];
        });

        dispatch(new AddLogToProductBinCard($cards));


    }


    public function pingStockLocation($online_quantity, $activeBatches = false, $departments = []) : array|bool
    {
        if($activeBatches === false){

            $activeBatches = $this->activeBatches;
        }

        $allbatches = [];

        foreach ($departments as $department){

            if($department === 'retail') {
                $cost_price_column = cost_price_column(4);
            }else{
                $cost_price_column = cost_price_column();
            }

            foreach ($activeBatches as $batch)
            {
                if($batch->{$department} === 0) continue;
                if($batch->{$department} - $online_quantity < 0){
                    $online_quantity = $online_quantity - $batch->{$department};
                    $allbatches[] = array_merge([
                        'id' => $batch->id,
                        $department => 0,
                        'qty' => $batch->{$department},
                        'cost_price' => $batch->{$cost_price_column},
                        'department' => $department
                    ], addOtherDepartment($batch, $department));
                }else{
                    $newqty = $batch->{$department} - $online_quantity;

                    $allbatches[] = array_merge([
                        'id' => $batch->id,
                        $department => $newqty,
                        'cost_price' => $batch->{$cost_price_column},
                        'department' => $department,
                        'qty' => $online_quantity
                    ],addOtherDepartment($batch, $department));
                    $online_quantity = 0;
                }

                if($online_quantity === 0) return $allbatches;
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
            if($batch->{$from} === 0) continue;
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
                    'qty' => $batch->{$from},
                    $to =>  ($to !=='retail' ?  $batch->{$from} :  $batch->{$from} * $this->box),
                    $cost_price => ($to !== 'retail') ? $batch->{$cost_price} : round(abs($batch->cost_price / $this->box))
                ];
            }else{
                $newqty = $batch->{$from} - $qty;
                $neededBatches[] = [
                    'id' => $batch->id,
                    'qty' => $qty,
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
            $this->{$dept->quantity_column} =  $this->stockbatches()->sum($dept->quantity_column);
            $this->update();
        }
    }


    public static function completeTransfer(array $batches, Stocktransfer $stocktransfer)
    {
        $cost_price =  $stocktransfer->to === 'retail' ? 'retail_cost_price' : 'cost_price';

        $bincards = [];

        foreach ($batches as $batch)
        {
            $b = Stockbatch::with(['stock'])->find($batch['id']);
            $b->{$stocktransfer->from} = $batch[$stocktransfer->from];
            $b->{$cost_price} = $batch[$cost_price];
            $b->update();
            $b->stock->updateQuantity();

            $bincards[] = [
                'bin_card_type'=>'APP//TRANSFER',
                'bin_card_date'=>todaysDate(),
                'user_id'=>Auth::id(),
                'out_qty'=>$batch['qty'],
                'in_qty' => 0,
                'stock_id'=>$b->stock->id,
                'stockbatch_id'=>$batch['id'],
                'from_department'=>$stocktransfer->from,
                'to_department'=>$stocktransfer->to,
                'stocktransfer_id'=>$stocktransfer->id,
                'comment'=>"Stock Transfer Transfer ID : ".$stocktransfer->id." by ".Auth::user()->name,
                'balance'=>$b->stock->totalBalance(),
                'department_balance'=>$b->stock->getCurrentlevel($stocktransfer->from)
            ];

            $b = Stockbatch::with(['stock'])->find($batch['id']);
            $b->{$stocktransfer->to} = $batch[$stocktransfer->to];
            $b->update();
            $b->stock->updateQuantity();


            $bincards[] = [
                'bin_card_type'=>'APP//RECEIVED',
                'bin_card_date'=>todaysDate(),
                'user_id'=>Auth::id(),
                'in_qty'=>$batch['qty'],
                'out_qty' => 0,
                'stock_id'=> $b->stock->id,
                'stockbatch_id'=>$batch['id'],
                'from_department'=>$stocktransfer->from,
                'to_department'=>$stocktransfer->to,
                'stocktransfer_id'=>$stocktransfer->id,
                'comment'=>"Stock Received Transfer ID : ".$stocktransfer->id." by ".Auth::user()->name,
                'balance'=>$b->stock->totalBalance(),
                'department_balance'=>$b->stock->getCurrentlevel($stocktransfer->to)
            ];

        }




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
        return  $this->wholesales + $this->bulksales + $this->quantity + ($this->retail === 0 || $this->box === 0) ? 0 : abs(round($this->retail/$this->box));
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
