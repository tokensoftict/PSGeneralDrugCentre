<?php

namespace App\Http\Livewire\ProductModule\Batch;

use App\Models\Stock;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class StockBatch extends Component
{

    public Stock $stock;

    use LivewireAlert;


    public function boot()
    {

    }

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.product-module.batch.stock-batch');
    }
}
