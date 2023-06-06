<?php

namespace App\Http\Livewire\Customer\Datatable;

use App\Classes\BooleanColumn;
use App\Classes\ExportDataTableComponent;
use App\Traits\SimpleDatatableComponentTrait;
use App\Classes\Column;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;


class CustomerDataTable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait;

    protected $model = Customer::class;

    public array $perPageAccepted = [100, 200, 500, 1000, 1500, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5000, 6000, 6500];

    public array $filters = [];

    public function builder(): Builder
    {
        return  Customer::query()->select('*')->filterdata($this->filters);
    }

    public function refresh($what)
    {

    }

    public static function mountColumn() : array
    {
        return [
            Column::make("Name", 'firstname')
                ->format(fn($value,$row, Column $column) => $row->firstname.' '.$row->lastname)
                ->sortable()->searchable(),
            Column::make("Email", "email")
                ->sortable()->searchable(),
            Column::make("Status", "status")
                ->format(function($value, $row, Column $column){
                    return '  <div class="form-check form-switch mb-3" dir="ltr">
                                    <input wire:change="toggle('.$row->id .')" id="user'.$row->id.'" type="checkbox" class="form-check-input" id="customSwitch1" '.($value ? 'checked' : '').'>
                                    <label class="form-check-label" for="customSwitch1">'.$value ? 'Active' : 'Inactive' .'</label>
                                </div>';
                })->html()
                ->sortable(),
            Column::make("Address", "address")
                ->sortable(),
            Column::make("Phone number", "phone_number")
                ->sortable()->searchable(),
            BooleanColumn::make("Retail customer", "retail_customer")
                ->sortable(),
            Column::make("City", "city.name")
                ->sortable(),
            Column::make("Credit balance", "credit_balance")
                ->format(fn($value, $row, Column $column) => money($value))
                ->sortable(),
            Column::make("Action","id")
                ->format(function($value, $row, Column $column) {
                    $html = "No Action";

                    if (userCanView('customer.update')){

                        $html=' <a class="btn btn-outline-primary btn-sm edit" wire:click="edit('.$value.')" href="javascript:void(0);" >

                                        <span wire:loading wire:target="edit('.$value .')" class="spinner-border spinner-border-sm me-2" role="status"></span>

                                        <i wire:loading.remove wire:target="edit('.$value.')" class="fas fa-pencil-alt"></i>

                                    </a>';
                    }

                    return $html;
                })->html()
        ];
    }



    public function edit($id)
    {
        $this->emit('editCustomer', $id);
    }

}
