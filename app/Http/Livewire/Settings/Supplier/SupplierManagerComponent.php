<?php

namespace App\Http\Livewire\Settings\Supplier;

use App\Models\Supplier;
use App\Traits\SimpleComponentTrait;
use Livewire\Component;

class SupplierManagerComponent extends Component
{

    use SimpleComponentTrait;

    public function mount()
    {
        $this->model = Supplier::class;
        $this->modalName = "Supplier";
        $this->data = [
            'name' => ['label' => 'Name', 'type'=>'text'],
            'phonenumber' => ['label' => 'Phone Number', 'type'=>'text'],
            'email' => ['label' => 'Email Address', 'type'=>'email'],
            'address' => ['label' => 'Address', 'type'=>'textarea']
        ];

        $this->newValidateRules = [
            'name' => 'required|min:1',
            'phonenumber' => 'required|digits_between:11,11',
        ];

        $this->updateValidateRules = $this->newValidateRules;

        $this->initControls();

    }

    public function booted()
    {
        $this->cacheModel = "suppliers";
    }

    public function render()
    {
        return view('livewire.settings.supplier.supplier-manager-component');
    }
}
