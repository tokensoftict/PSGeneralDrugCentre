<?php

namespace App\Http\Livewire\StockTransfer\Report;

use App\Models\Stocktransferitem;
use App\Traits\PowerGridComponentTrait;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Rules\{Rule, RuleActions};
use PowerComponents\LivewirePowerGrid\Traits\{ActionButton, WithExport};
use PowerComponents\LivewirePowerGrid\Filters\Filter;
use PowerComponents\LivewirePowerGrid\{Button, Column, Exportable, Footer, Header, PowerGrid, PowerGridComponent, PowerGridEloquent};

final class StockTransferSummaryReport extends PowerGridComponent
{
   use PowerGridComponentTrait;

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
     * @return Builder<\App\Models\Stocktransferitem>
     */
    public function datasource(): Builder
    {
        return Stocktransferitem::query()->select(
            'stock_id',
            DB::raw( 'SUM(quantity * selling_price) as total_sub_total'),
            DB::raw( 'SUM(selling_price) as cost'),
            DB::raw( 'SUM(quantity) as total_qty')
        )->whereHas('stocktransfer', function ($query){
            $query->where('to',$request->get('department'))
                ->whereBetween('transfer_date',[$request->get('from'),$request->get('to')])
                ->where('status',$request->status)
                ->orderBy("id","DESC");
        });
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
        return [];
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
            ->addColumn('stocktransfer_id')
            ->addColumn('stock_id')
            ->addColumn('quantity')
            ->addColumn('selling_price')
            ->addColumn('cost_price')
            ->addColumn('stockbatch_id')
            ->addColumn('user_id')
            ->addColumn('rem_quantity');
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
            Column::make('Id', 'id'),
            Column::make('Stocktransfer id', 'stocktransfer_id'),
            Column::make('Stock id', 'stock_id'),
            Column::make('Quantity', 'quantity'),
            Column::make('Selling price', 'selling_price')
                ->sortable()
                ->searchable(),

            Column::make('Cost price', 'cost_price')
                ->sortable()
                ->searchable(),

            Column::make('Stockbatch id', 'stockbatch_id'),
            Column::make('User id', 'user_id'),
            Column::make('Rem quantity', 'rem_quantity'),
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

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
     * PowerGrid Stocktransferitem Action Buttons.
     *
     * @return array<int, Button>
     */

    /*
    public function actions(): array
    {
       return [
           Button::make('edit', 'Edit')
               ->class('bg-indigo-500 cursor-pointer text-white px-3 py-2.5 m-1 rounded text-sm')
               ->route('stocktransferitem.edit', function(\App\Models\Stocktransferitem $model) {
                    return $model->id;
               }),

           Button::make('destroy', 'Delete')
               ->class('bg-red-500 cursor-pointer text-white px-3 py-2 m-1 rounded text-sm')
               ->route('stocktransferitem.destroy', function(\App\Models\Stocktransferitem $model) {
                    return $model->id;
               })
               ->method('delete')
        ];
    }
    */

    /*
    |--------------------------------------------------------------------------
    | Actions Rules
    |--------------------------------------------------------------------------
    | Enable the method below to configure Rules for your Table and Action Buttons.
    |
    */

    /**
     * PowerGrid Stocktransferitem Action Rules.
     *
     * @return array<int, RuleActions>
     */

    /*
    public function actionRules(): array
    {
       return [

           //Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($stocktransferitem) => $stocktransferitem->id === 1)
                ->hide(),
        ];
    }
    */
}
