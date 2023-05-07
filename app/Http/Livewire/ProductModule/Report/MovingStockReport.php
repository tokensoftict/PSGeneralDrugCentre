<?php

namespace App\Http\Livewire;

use App\Models\Movingstock;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Rules\{Rule, RuleActions};
use PowerComponents\LivewirePowerGrid\Traits\{ActionButton, WithExport};
use PowerComponents\LivewirePowerGrid\Filters\Filter;
use PowerComponents\LivewirePowerGrid\{Button, Column, Exportable, Footer, Header, PowerGrid, PowerGridComponent, PowerGridEloquent};

final class MovingStockReport extends PowerGridComponent
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
     * @return Builder<\App\Models\Movingstock>
     */
    public function datasource(): Builder
    {
        return Movingstock::query();
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
            ->addColumn('supplier_id')
            ->addColumn('stockgroup_id')
            ->addColumn('retail_qty')
            ->addColumn('no_qty_sold')
            ->addColumn('daily_qty_sold')
            ->addColumn('average_inventory')
            ->addColumn('turn_over_rate')
            ->addColumn('group_os_id')
            ->addColumn('is_grouped')
            ->addColumn('turn_over_rate2')
            ->addColumn('lastpurchase_days')
            ->addColumn('moving_stocks_constant2')
            ->addColumn('name')

           /** Example of custom column using a closure **/
            ->addColumn('name_lower', fn (Movingstock $model) => strtolower(e($model->name)))

            ->addColumn('box')
            ->addColumn('threshold')
            ->addColumn('cartoon')
            ->addColumn('supplier_name')
            ->addColumn('av_cost_price')
            ->addColumn('av_rt_cost_price')
            ->addColumn('rt_qty')
            ->addColumn('all_qty')
            ->addColumn('tt_av_cost_price')
            ->addColumn('tt_av_rt_cost_price')
            ->addColumn('created_at_formatted', fn (Movingstock $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
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
            Column::make('Supplier id', 'supplier_id'),
            Column::make('Stockgroup id', 'stockgroup_id'),
            Column::make('Retail qty', 'retail_qty')
                ->sortable()
                ->searchable(),

            Column::make('No qty sold', 'no_qty_sold')
                ->sortable()
                ->searchable(),

            Column::make('Daily qty sold', 'daily_qty_sold')
                ->sortable()
                ->searchable(),

            Column::make('Average inventory', 'average_inventory')
                ->sortable()
                ->searchable(),

            Column::make('Turn over rate', 'turn_over_rate')
                ->sortable()
                ->searchable(),

            Column::make('Group os id', 'group_os_id'),
            Column::make('Is grouped', 'is_grouped'),
            Column::make('Turn over rate2', 'turn_over_rate2')
                ->sortable()
                ->searchable(),

            Column::make('Lastpurchase days', 'lastpurchase_days'),
            Column::make('Moving stocks constant2', 'moving_stocks_constant2')
                ->sortable()
                ->searchable(),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Box', 'box'),
            Column::make('Threshold', 'threshold')
                ->sortable()
                ->searchable(),

            Column::make('Cartoon', 'cartoon'),
            Column::make('Supplier name', 'supplier_name')
                ->sortable()
                ->searchable(),

            Column::make('Av cost price', 'av_cost_price')
                ->sortable()
                ->searchable(),

            Column::make('Av rt cost price', 'av_rt_cost_price')
                ->sortable()
                ->searchable(),

            Column::make('Rt qty', 'rt_qty')
                ->sortable()
                ->searchable(),

            Column::make('All qty', 'all_qty')
                ->sortable()
                ->searchable(),

            Column::make('Tt av cost price', 'tt_av_cost_price')
                ->sortable()
                ->searchable(),

            Column::make('Tt av rt cost price', 'tt_av_rt_cost_price')
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
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('supplier_name')->operators(['contains']),
            Filter::inputText('av_cost_price')->operators(['contains']),
            Filter::inputText('av_rt_cost_price')->operators(['contains']),
            Filter::inputText('rt_qty')->operators(['contains']),
            Filter::inputText('all_qty')->operators(['contains']),
            Filter::inputText('tt_av_cost_price')->operators(['contains']),
            Filter::inputText('tt_av_rt_cost_price')->operators(['contains']),
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
     * PowerGrid Movingstock Action Buttons.
     *
     * @return array<int, Button>
     */

    /*
    public function actions(): array
    {
       return [
           Button::make('edit', 'Edit')
               ->class('bg-indigo-500 cursor-pointer text-white px-3 py-2.5 m-1 rounded text-sm')
               ->route('movingstock.edit', function(\App\Models\Movingstock $model) {
                    return $model->id;
               }),

           Button::make('destroy', 'Delete')
               ->class('bg-red-500 cursor-pointer text-white px-3 py-2 m-1 rounded text-sm')
               ->route('movingstock.destroy', function(\App\Models\Movingstock $model) {
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
     * PowerGrid Movingstock Action Rules.
     *
     * @return array<int, RuleActions>
     */

    /*
    public function actionRules(): array
    {
       return [

           //Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($movingstock) => $movingstock->id === 1)
                ->hide(),
        ];
    }
    */
}
