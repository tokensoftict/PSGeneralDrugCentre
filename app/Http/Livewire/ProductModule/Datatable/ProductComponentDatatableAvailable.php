<?php

namespace App\Http\Livewire\ProductModule\Datatable;

use App\Classes\ExportDataTableComponent;
use App\Classes\Settings;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use App\Classes\Column;
use App\Models\Stock;
use App\Classes\BooleanColumn;

class ProductComponentDatatableAvailable extends ExportDataTableComponent
{

    use SimpleDatatableComponentTrait;

    protected $model = Stock::class;

    public array $filters = [];

    public array $perPageAccepted = [100, 200, 500, 1000, 1500, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5000, 6000, 6500];

    public function builder(): Builder
    {
        return Stock::query()->select('*')->filterdata($this->filters);

    }

    public static function mountColumn() : array
    {
        return  [
            Column::make("Name", "name")
                ->sortable()->searchable(),
            Column::make("WS Price", "whole_price")
                ->format(fn($value, $row, Column $column)=> money($value))
                ->sortable(),
            Column::make("Bulk Price", "bulk_price")
                ->format(fn($value, $row, Column $column)=> money($value))
                ->sortable(),
            Column::make("Retail Price", "retail_price")
                ->format(fn($value, $row, Column $column)=> money($value))
                ->sortable(),
            Column::make("Box", "box")
                ->format(fn($value, $row, Column $column)=> $value)
                ->sortable(),
            Column::make("Carton", "carton")
                ->format(fn($value, $row, Column $column)=> $value)
                ->sortable(),
            Column::make("WholeSales", "wholesales")
                ->format(fn($value, $row, Column $column)=> $value)
                ->sortable(),
            Column::make("BulkSales", "bulksales")
                ->format(fn($value, $row, Column $column)=> $value)
                ->sortable(),
            Column::make("Retail", "retail")
                ->format(fn($value, $row, Column $column)=> $value)
                ->sortable(),
            Column::make("Main Store", "quantity")
                ->format(fn($value, $row, Column $column)=> $value)
                ->sortable(),

            Column::make("Action","id")
                ->format(function($value, $row, Column $column){
                    $html = 'No Action';
                    if(userCanView('product.edit')){
                        $html = '<div class="dropdown"><button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-dots-horizontal-rounded"></i></button>';

                        $html .='<ul class="dropdown-menu dropdown-menu-end">';
                        if(userCanView('product.edit')) {
                            $html .= '<a href="' . route('product.edit', $value) . '" class="dropdown-item">Edit Stock</a></li>';
                        }
                        $html .='</ul>';
                    }

                    return $html;
                })
                ->html()
        ];
    }


    public function toggle(Stock $stock)
    {
        $product = $stock;
        $product->status = !$product->status;
        $product->save();
        $this->emit('$refreshData');
    }


}
/*
 *      Column::make("Category", "category.name")
                ->format(fn($value, $row, Column $column)=> isset($row->category->name) ? $row->category->name : "N/A")
                ->sortable()->searchable(),
            Column::make("Group", "stockgroup.name")
                ->format(fn($value, $row, Column $column)=> isset($row->stockgroup->name) ? $row->stockgroup->name : "N/A")
                ->sortable(),

    Column::make("Manufacturer", "manufacturer.name")
                ->format(fn($value, $row, Column $column)=> isset($row->manufacturer->name) ? $row->manufacturer->name : "N/A")
                ->sortable(),
 */
