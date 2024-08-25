<?php

namespace App\Http\Livewire\ProductModule\Datatable;

use App\Classes\ExportDataTableComponent;
use App\Classes\Settings;
use App\Jobs\AddLogToProductBinCard;
use App\Models\Stockbatch;
use App\Models\Stockopening;
use App\Models\SupplierStockOpening;
use App\Traits\SimpleDatatableComponentTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Classes\Column;
use Illuminate\Database\Eloquent\Builder;

class SupplierDbOverviewDatatable extends ExportDataTableComponent
{
    use SimpleDatatableComponentTrait, LivewireAlert;

    protected $primaryKey = "stockopenings.supplier_id";

    public $department;

    public $payment_date;

    public function builder(): Builder
    {
        return SupplierStockOpening::query()
            ->with("supplier")
            ->where("date_added", now()->format("Y-m-d"));
    }

    public static function mountColumn() : array
    {
        return [
            Column::make("Name", "supplier.name")->sortable()->searchable()->sortable(),
            Column::make("Total Opening Cost", "total_opening_cost_price")
            ->format(fn($value, $row, Column $column)=> money($value))->sortable(),
            Column::make("Total Opening Retail Cost", "total_opening_retail_cost_price")
            ->format(fn($value, $row, Column $column)=> money($value))->sortable(),
            Column::make("Total Supplier Our Standing", "total_supplier_outstanding")
            ->format(fn($value, $row, Column $column)=> money($value))->sortable(),
            Column::make("Total Opening Quantity", "total_opening_quantity")
            ->format(fn($value, $row, Column $column)=> money($value))->sortable(),
            Column::make("Total Retail Opening Quantity", "total_opening_quantity_retail")
                ->format(fn($value, $row, Column $column)=> money($value))->sortable(),
            Column::make("Last Supplier Date", "last_supplier_date")
                ->format(fn($value, $row, Column $column)=>  $value->format("d/m/Y"))->sortable(),
        ];
    }


}
