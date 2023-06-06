<?php

namespace App\Http\Livewire\ProductModule\Datatable;

use App\Classes\ExportDataTableComponent;
use App\Traits\SimpleDatatableComponentTrait;
use App\Classes\Column;
use App\Models\Stockbincard;
use Illuminate\Database\Eloquent\Builder;

class ProductBinCardComponentDatatable extends ExportDataTableComponent
{

    public array $perPageAccepted = [100, 200, 500, 1000, 1500, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5000, 6000, 6500];

    use SimpleDatatableComponentTrait;

    public array $filters;

    protected $model = Stockbincard::class;

    public function builder(): Builder
    {
       return  Stockbincard::query()->select('*')->filterdata($this->filters);

    }



    public static function mountColumn() : array {

        return [
            Column::make("Name", "stock.name")->searchable(),
            Column::make("User", "user.name")->sortable()->searchable(),
            Column::make("Type", "type")->sortable(),
            Column::make("Date", "date_added")->sortable(),
            Column::make("In", "in")->sortable(),
            Column::make("Out", "out")->sortable(),
            Column::make("Sold", "sold")->sortable(),
            Column::make("Return", "return")->sortable(),
            Column::make("Total", "total")->sortable(),
        ];
    }
/*
    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Stock id", "stock_id")
                ->sortable(),
            Column::make("Stockbatch id", "stockbatch_id")
                ->sortable(),
            Column::make("User id", "user_id")
                ->sortable(),
            Column::make("In", "in")
                ->sortable(),
            Column::make("Out", "out")
                ->sortable(),
            Column::make("Sold", "sold")
                ->sortable(),
            Column::make("Return", "return")
                ->sortable(),
            Column::make("Total", "total")
                ->sortable(),
            Column::make("Type", "type")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
        ];
    }
*/
}
