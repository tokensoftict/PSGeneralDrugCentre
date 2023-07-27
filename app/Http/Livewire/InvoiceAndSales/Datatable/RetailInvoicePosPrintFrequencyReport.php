<?php

namespace App\Http\Livewire\InvoiceAndSales\Datatable;

use App\Models\Invoiceactivitylog;
use App\Traits\PowerGridComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    Exportable,
    Footer,
    Header,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent};
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Filters\Filter;
use PowerComponents\LivewirePowerGrid\Rules\{Rule, RuleActions};
use PowerComponents\LivewirePowerGrid\Traits\{ActionButton, WithExport};

final class RetailInvoicePosPrintFrequencyReport extends PowerGridComponent
{
    use PowerGridComponentTrait;

    public $key = "id";


    public array $filters;

    /*
    |--------------------------------------------------------------------------
    |  Datasource
    |--------------------------------------------------------------------------
    | Provides data to your Table using a Model or Collection
    |
    */

    /**
     * PowerGrid datasource.
     *
     * @return Builder<\App\Models\Invoiceactivitylog>
     */
    public function datasource(): Builder
    {
        return Invoiceactivitylog::select('user_id', DB::raw('count(invoice_id) as print_count'), 'invoice_id', 'invoice_number')
            ->where('activity', 'LIKE', '%Thermal%')
            ->whereBetween('activity_date', $this->filters['filters']['between.invoice_date'])
            ->having('print_count', '>=', 2)
             ->whereHas('invoice', function($query){
                $query
                    ->where(function($q){
                        $q->orWhere('status_id', status('Complete'))
                            ->orWhere('status_id', status('Paid'));
                    })
                    ->where('in_department', 'retail');
            })

            ->groupBy('user_id', 'invoice_id', 'invoice_number');
    }

    /*
    |--------------------------------------------------------------------------
    |  Relationship Search
    |--------------------------------------------------------------------------
    | Configure here relationships to be used by the Search and Table Filters.
    |
    */

    /**
     * Relationship search.
     *
     * @return array<string, array<int, string>>
     */
    public function relationSearch(): array
    {
        return [
            'user' => [
                'name'
            ]
        ];
    }

    /*
    |--------------------------------------------------------------------------
    |  Add Column
    |--------------------------------------------------------------------------
    | Make Datasource fields available to be used as columns.
    | You can pass a closure to transform/modify the data.
    |
    | ❗ IMPORTANT: When using closures, you must escape any value coming from
    |    the database using the `e()` Laravel Helper function.
    |
    */
    public function addColumns(): PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('id')
            ->addColumn('invoice_id')
            ->addColumn('print_count')
            ->addColumn('invoice_number')
            ->addColumn('user_id')
            ->addColumn('username', function (Invoiceactivitylog $log){
                return $log->user->name;
            });

    }

    /*
    |--------------------------------------------------------------------------
    |  Include Columns
    |--------------------------------------------------------------------------
    | Include the columns added columns, making them visible on the Table.
    | Each column can be configured with properties, filters, actions...
    |
    */

     /**
      * PowerGrid Columns.
      *
      * @return array<int, Column>
      */
    public function columns(): array
    {
        return [
            Column::make('Invoice number', 'invoice_number')->sortable(),
            Column::make('Invoice ID', 'invoice_id')->sortable(),
            Column::make('No. of Times',  'print_count'),
            Column::make('User', 'username')->sortable()->searchable(),
        ];
    }

    /**
     * PowerGrid Filters.
     *
     * @return array<int, Filter>
     */
    public function filters(): array
    {
        return [

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Actions Method
    |--------------------------------------------------------------------------
    | Enable the method below only if the Routes below are defined in your app.
    |
    */

    /**
     * PowerGrid Invoiceactivitylog Action Buttons.
     *
     * @return array<int, Button>
     */


    public function actions(): array
    {
       return [
           Button::make('view', 'View Invoice')
               ->class('btn btn-primary btn-sm')
               ->route('invoiceandsales.view', function(Invoiceactivitylog $model) {
                    return ['invoice' => $model->invoice_id];;
               })
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Actions Rules
    |--------------------------------------------------------------------------
    | Enable the method below to configure Rules for your Table and Action Buttons.
    |
    */

    /**
     * PowerGrid Invoiceactivitylog Action Rules.
     *
     * @return array<int, RuleActions>
     */


    public function actionRules(): array
    {
       return [

           //Hide button edit for ID 1
            Rule::button('view')
                ->when(fn($invoiceactivitylog) => $invoiceactivitylog->id === 1)
                ->hide(),
        ];
    }

}
