<?php

namespace App\Jobs;

use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushStockUpdateToServerFromTransfer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    var $stock_array;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($_stock_array)
    {
        $this->stock_array = $_stock_array;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $items = Stock::whereIn('id',$this->stock_array)->get();
        $stock = [];
        foreach ($items as $item){
            //if($item->bulk_price > 0) {
            $stock[] = $item->getBulkPushData();
            //}
        }
        $data = ['action' => 'update', 'table' => 'stock', 'data' => $stock, 'url'=>onlineBase()."dataupdate/add_or_update_stock"];

        _POST('add_or_update_stock',$data);
    }
}
