<?php

namespace App\Http\Livewire\ProductModule\Batch;

use App\Jobs\AddLogToProductBinCard;
use App\Models\Stock;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Stockbatch as batch;

class StockBatch extends Component
{

    public Stock $stock;
    public array $batches = [];

    public $suppliers;

    public string $selectedDepartment;

    public string $dept;

    use LivewireAlert;


    public function boot()
    {

    }

    public function mount()
    {
        if($this->stock->activeBatches->count() > 2)
        {
            foreach ($this->stock->activeBatches as $batch)
            {
                $this->batches[] = $batch->toArray();
            }
        }else {
            foreach ($this->stock->minimumBatches as $batch)
            {
                $this->batches[] = [
                    'id' => $batch->id,
                    'received_date' => $batch->received_date,
                    'expiry_date' => $batch->expiry_date,
                    $this->selectedDepartment => $batch->{$this->selectedDepartment},
                    cost_price_column(department_by_quantity_column($this->selectedDepartment)->id) => $batch->{cost_price_column(department_by_quantity_column($this->selectedDepartment)->id)},
                    'stock_id' => $batch->stock_id,
                    'supplier_id' => $batch->supplier_id
                ];
            }
        }

        $this->suppliers = suppliers(true);

        $this->dept = department_by_quantity_column($this->selectedDepartment)->label;

    }

    public function render()
    {
        return view('livewire.product-module.batch.stock-batch');
    }

    public function batchStock()
    {

        $b_whole = $this->stock->wholesales;
        $b_retail = $this->stock->retail;
        $b_quantity = $this->stock->quantity;
        $b_bulksales = $this->stock->bulksales;

        batch::upsert($this->batches,['id'], array_keys($this->batches[0]));

        $this->stock->updateQuantity();

        $bincards[] =  [
            'bin_card_type'=>'APP//BATCH_UPDATE',
            'bin_card_date'=>date('Y-m-d'),
            'user_id'=>auth()->id(),
            'stock_id'=> $this->stock->id,
            'out_qty'=>0,
            'comment'=>"Stock Batch Update was made
                                <br/>-----BEFORE UPDATE------<br/>
                                Wholesale : $b_whole, Bulk : $b_bulksales
                                , Retail : $b_retail
                                , Main Store : $b_quantity
                                <br/>
                                <br/>-----AFTER UPDATE------<br/>
                                  Wholesale : ".$this->stock->wholesales.", Bulk : ".$this->stock->bulksales.", Retail : ".$this->stock->retail.", Main Store : ".$this->stock->quantity."
                                ",
            'balance'=>$this->stock->totalBalance(),
        ];


        dispatch(new AddLogToProductBinCard($bincards));

        $this->alert(
            "success",
            "Quick Adjust Quantity",
            [
                'position' => 'center',
                'timer' => 6000,
                'toast' => false,
                'text' =>  "Stock Quantity has been updated successfully!",
            ]
        );

        return redirect()->route('product.balance_stock');
    }
}