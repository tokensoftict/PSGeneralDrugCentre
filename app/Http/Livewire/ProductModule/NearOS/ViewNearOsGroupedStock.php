<?php

namespace App\Http\Livewire\ProductModule\NearOs;

use App\Models\Stockgroup;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;


class ViewNearOsGroupedStock extends Component
{
    use LivewireAlert;

    public function boot()
    {

    }

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.product-module.near-os.view-near-os-grouped-stock');
    }
}
