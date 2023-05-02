<?php

namespace App\Http\Livewire\ProductModule\NearOs;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use LivewireUI\Modal\ModalComponent;

class ViewNearOsGroupedStock extends ModalComponent
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
