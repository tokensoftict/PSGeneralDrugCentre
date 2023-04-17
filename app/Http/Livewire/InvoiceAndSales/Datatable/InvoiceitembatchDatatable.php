<?php

namespace App\Http\Livewire\InvoiceAndSales\Datatable;

use App\Classes\Settings;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Invoiceitembatch;

class InvoiceitembatchDatatable extends DataTableComponent
{


    public  array $filters;

    use SimpleDatatableComponentTrait;

    protected $model = Invoiceitembatch::class;

    public function builder(): Builder
    {
        return Invoiceitembatch::query()->select('*')->filterdata($this->filters)->with(['invoice'=>function($query){
            $query->whereIn('status_id',[2,4,6]);
        }]);
    }


    public static function  mountColumn() : array
    {
        return [
            Column::make("Invoice number", "invoice.invoice_number")
                ->sortable()->searchable(),
            Column::make("Department", "invoice.in_department")
                ->format(fn($value, $row, Column $column)=> Settings::$department[$value]),
            Column::make("Product", "stock.name")
                ->format(fn($value, $row, Column $column)=> $value)
                ->sortable()->searchable(),
            Column::make("Customer", "customer.firstname")
                ->format(fn($value, $row, Column $column)=> $row->customer->firstname." ".$row->customer->lastname)
                ->sortable()->searchable(),
            Column::make("Selling Price", "selling_price")
                ->format(fn($value, $row, Column $column)=> money($row->selling_price))
                ->sortable(),
            Column::make("Cost Price", "cost_price")
                ->format(fn($value, $row, Column $column)=> money($row->cost_price))
                ->sortable(),

            Column::make("Quantity", "quantity")
                ->format(fn($value, $row, Column $column)=> money($row->quantity))
                ->sortable(),
            Column::make("Date", "invoice.invoice_date")
                ->format(fn($value, $row, Column $column)=> eng_str_date($value))
                ->sortable(),
            Column::make("Time", "invoice.sales_time")
                ->format(fn($value, $row, Column $column)=> twelveHourClock($value))
                ->sortable(),
            Column::make("By", "invoice.last_updated.name")
                ->format(fn($value, $row, Column $column)=> $value)
                ->sortable(),
            Column::make("Action","id")
                ->format(function($value, $row, Column $column){
                    $html = "No Action";
                    if(can(['view','edit','printAfour','printThermal','printWaybill','delete'], $row->invoice)) {
                        $html = '<div class="dropdown"><button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-dots-horizontal-rounded"></i></button>';
                        $html .= '<ul class="dropdown-menu dropdown-menu-end">';
                        if (auth()->user()->can('view', $row->invoice)) {
                            $html .= '<li><a href="' . route('invoiceandsales.view', $row->invoice_id) . '" class="dropdown-item">Invoice Details</a></li>';
                        }
                        if (auth()->user()->can('edit', $row->invoice)) {
                            $html .= '<li><a href="' . route('invoiceandsales.edit', $row->invoice_id) . '" class="dropdown-item">Edit Invoice</a></li>';
                        }
                        if (auth()->user()->can('printAfour', $row->invoice)) {
                            $html .= '<li><a href="' . route('invoiceandsales.print_afour', $row->invoice_id) . '" class="dropdown-item print">Print A4</a></li>';
                        }
                        if (auth()->user()->can('printThermal', $row->invoice)) {
                            $html .= '<li><a href="' . route('invoiceandsales.pos_print', $row->invoice_id) . '" class="dropdown-item print">Print Thermal</a></li>';
                        }
                        if (auth()->user()->can('printWaybill', $row->invoice)) {
                            $html .= '<li><a href="' . route('invoiceandsales.print_way_bill', $row->invoice_id) . '" class="dropdown-item print">Print Waybill</a></li>';
                        }
                        if (auth()->user()->can('delete', $row->invoice)) {
                            $html .= '<li><a href="' . route('invoiceandsales.destroy', $row->invoice_id) . '" href="javascript:" class="dropdown-item">Delete Invoice</a></li>';
                        }
                        $html .= '</ul></div>';
                    }
                    return $html;

                })
                ->html()
        ];
    }

}
