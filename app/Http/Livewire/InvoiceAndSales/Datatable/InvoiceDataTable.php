<?php

namespace App\Http\Livewire\InvoiceAndSales\Datatable;


use App\Classes\ExportDataTableComponent;
use App\Classes\Settings;
use App\Traits\SimpleDatatableComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Classes\Column;
use App\Models\Invoice;


class InvoiceDataTable extends ExportDataTableComponent
{

    use SimpleDatatableComponentTrait,LivewireAlert;

    protected $model = Invoice::class;

    public array $filters = [];



    public function builder(): Builder
    {
        return Invoice::query()->select('*')->where('invoices.status_id','<>',status('Deleted'))->filterdata($this->filters);

    }

    public static function  mountColumn() : array
    {
        return [
            Column::make("Invoice number", "invoice_number")
                ->sortable()->searchable(),
            Column::make("Department", "in_department")
                ->format(fn($value, $row, Column $column)=> Settings::$department[$value])
                ->sortable()->searchable(),
            Column::make("Customer", "customer.firstname")
                ->format(fn($value, $row, Column $column)=> $row->firstname." ".$row->lastname)
                ->sortable()->searchable(),
            Column::make("Sub total", "sub_total")
                ->format(fn($value, $row, Column $column)=> money($row->sub_total))
                ->sortable()
                ->footer(function($rows){
                    return money($rows->sum('sub_total'));
                })
                ->searchable(),
            Column::make("Status", "status.name")
                ->format(fn($value, $row, Column $column) => showStatus($value))->html()
                ->sortable()->searchable(),
            Column::make("Discount", "discount_amount")
                ->format(fn($value, $row, Column $column)=> money($row->discount_amount))
                ->sortable()
                ->footer(function($rows){
                    return money($rows->sum('discount_amount'));
                })
                ->searchable(),
            Column::make("Total paid", "total_amount_paid")
                ->format(fn($value, $row, Column $column)=> money($row->total_amount_paid))
                ->sortable()
                ->searchable()
                ->footer(function($rows){
                    return money($rows->sum('total_amount_paid'));
                })
            ,
            Column::make("Date", "invoice_date")
                ->format(fn($value, $row, Column $column)=> eng_str_date($row->invoice_date))
                ->sortable()->searchable(),
            Column::make("Time", "sales_time")
                ->format(fn($value, $row, Column $column)=> twelveHourClock($row->sales_time))
                ->sortable()->searchable(),
            Column::make("By", "last_updated.name")
                ->format(fn($value, $row, Column $column)=> $value)
                ->sortable()->searchable(),
            Column::make("Action","id")
                ->format(function($value, $row, Column $column){
                    $html = "No Action";
                    if(can(['view','edit','printAfour','printThermal','printWaybill','delete', 'return'], $row)) {
                        $html = '<div class="dropdown"><button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-dots-horizontal-rounded"></i></button>';
                        $html .= '<ul class="dropdown-menu dropdown-menu-end">';
                        if (auth()->user()->can('view', $row)) {
                            $html .= '<li><a href="' . route('invoiceandsales.view', $row->id) . '" class="dropdown-item">Invoice Details</a></li>';
                        }
                        if (auth()->user()->can('edit', $row)) {
                            $html .= '<li><a href="' . route('invoiceandsales.edit', $row->id) . '" class="dropdown-item">Edit Invoice</a></li>';
                        }

                        if (auth()->user()->can('return', $row)) {
                            $html .= '<li><a href="' . route('invoiceandsales.return', $row->id) . '" class="dropdown-item">Return Invoice</a></li>';
                        }

                        if (auth()->user()->can('printAfour', $row)) {
                            $html .= '<li><a href="' . route('invoiceandsales.print_afour', $row->id) . '" class="dropdown-item print">Print A4</a></li>';
                        }

                        if (auth()->user()->can('return', $row)) {
                            $html .= '<li><a href="' . route('invoiceandsales.return', $row->id) . '" class="dropdown-item">Return Invoice</a></li>';
                        }

                        if (auth()->user()->can('printThermal', $row)) {
                            $html .= '<li><a href="' . route('invoiceandsales.pos_print', $row->id) . '" class="dropdown-item print">Print Thermal</a></li>';
                        }

                        if (auth()->user()->can('printWaybill', $row)) {
                            $html .= '<li><a href="' . route('invoiceandsales.print_way_bill', $row->id) . '" class="dropdown-item print">Print Waybill</a></li>';
                        }

                        if (auth()->user()->can('delete', $row)) {
                            $html .= '<li><a href="' . route('invoiceandsales.destroy', $row->id) . '" href="javascript:" class="dropdown-item">Delete Invoice</a></li>';
                        }

                        $html .= '</ul></div>';
                    }
                    return $html;
                })
                ->html()
        ];
    }

}
