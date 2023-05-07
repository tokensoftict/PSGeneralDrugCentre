<?php

namespace App\Http\Livewire;

use App\Models\Stockbincard;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Rules\{Rule, RuleActions};
use PowerComponents\LivewirePowerGrid\Traits\{ActionButton, WithExport};
use PowerComponents\LivewirePowerGrid\Filters\Filter;
use PowerComponents\LivewirePowerGrid\{Button, Column, Exportable, Footer, Header, PowerGrid, PowerGridComponent, PowerGridEloquent};

final class StockBatchUpdateReport extends PowerGridComponent
{
    use ActionButton;
    use WithExport;

    /*
    |--------------------------------------------------------------------------
    |  Features Setup
    |--------------------------------------------------------------------------
    | Setup Table's general features
    |
    */
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

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
     * @return Builder<\App\Models\Stockbincard>
     */
    public function datasource(): Builder
    {
        return Stockbincard::query();
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
            ->addColumn('stock_id')
            ->addColumn('bin_card_type')

           /** Example of custom column using a closure **/
            ->addColumn('bin_card_type_lower', fn (Stockbincard $model) => strtolower(e($model->bin_card_type)))

            ->addColumn('bin_card_date_formatted', fn (Stockbincard $model) => Carbon::parse($model->bin_card_date)->format('d/m/Y'))
            ->addColumn('user_id')
            ->addColumn('in_qty')
            ->addColumn('out_qty')
            ->addColumn('sold_qty')
            ->addColumn('return_qty')
            ->addColumn('stockbatch_id')
            ->addColumn('to_department')
            ->addColumn('from_department')
            ->addColumn('supplier_id')
            ->addColumn('invoice_id')
            ->addColumn('stocktransfer_id')
            ->addColumn('purchase_id')
            ->addColumn('balance')
            ->addColumn('comment')
            ->addColumn('department_balance')
            ->addColumn('created_at_formatted', fn (Stockbincard $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
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
            Column::make('Stock id', 'stock_id'),
            Column::make('Bin card type', 'bin_card_type')
                ->sortable()
                ->searchable(),

            Column::make('Bin card date', 'bin_card_date_formatted', 'bin_card_date')
                ->sortable(),

            Column::make('User id', 'user_id'),
            Column::make('In qty', 'in_qty'),
            Column::make('Out qty', 'out_qty'),
            Column::make('Sold qty', 'sold_qty'),
            Column::make('Return qty', 'return_qty'),
            Column::make('Stockbatch id', 'stockbatch_id'),
            Column::make('To department', 'to_department')
                ->sortable()
                ->searchable(),

            Column::make('From department', 'from_department')
                ->sortable()
                ->searchable(),

            Column::make('Supplier id', 'supplier_id'),
            Column::make('Invoice id', 'invoice_id'),
            Column::make('Stocktransfer id', 'stocktransfer_id'),
            Column::make('Purchase id', 'purchase_id'),
            Column::make('Balance', 'balance'),
            Column::make('Comment', 'comment')
                ->sortable()
                ->searchable(),

            Column::make('Department balance', 'department_balance')
                ->sortable()
                ->searchable(),

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
            Filter::inputText('bin_card_type')->operators(['contains']),
            Filter::datepicker('bin_card_date'),
            Filter::inputText('to_department')->operators(['contains']),
            Filter::inputText('from_department')->operators(['contains']),
            Filter::datetimepicker('created_at'),
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
     * PowerGrid Stockbincard Action Buttons.
     *
     * @return array<int, Button>
     */

    /*
    public function actions(): array
    {
       return [
           Button::make('edit', 'Edit')
               ->class('bg-indigo-500 cursor-pointer text-white px-3 py-2.5 m-1 rounded text-sm')
               ->route('stockbincard.edit', function(\App\Models\Stockbincard $model) {
                    return $model->id;
               }),

           Button::make('destroy', 'Delete')
               ->class('bg-red-500 cursor-pointer text-white px-3 py-2 m-1 rounded text-sm')
               ->route('stockbincard.destroy', function(\App\Models\Stockbincard $model) {
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
     * PowerGrid Stockbincard Action Rules.
     *
     * @return array<int, RuleActions>
     */

    /*
    public function actionRules(): array
    {
       return [

           //Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($stockbincard) => $stockbincard->id === 1)
                ->hide(),
        ];
    }
    */
}
