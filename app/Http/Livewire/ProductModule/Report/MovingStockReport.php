<?php

namespace App\Http\Livewire\ProductModule\Report;

use App\Models\Movingstock;
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
use PowerComponents\LivewirePowerGrid\Filters\Filter;
use PowerComponents\LivewirePowerGrid\Rules\{RuleActions};
use PowerComponents\LivewirePowerGrid\Traits\{ActionButton, WithExport};

final class MovingStockReport extends PowerGridComponent
{
  use PowerGridComponentTrait;

  public $key = "id";
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
        return Movingstock::query()->with(['stock', 'stock.category']);
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
            ->addColumn('category', function (Movingstock $movingstock) {
                return $movingstock->stock->category->name ?? "";
            })
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
            ->addColumn('box')
            ->addColumn('threshold')
            ->addColumn('cartoon')
            ->addColumn('formatted_carton', function (Movingstock $movingstock){
                return $movingstock->cartoon ?? $movingstock->stock->carton;
            })
            ->addColumn('supplier_name')
            ->addColumn('whole_price', function($movingstock){
                return $movingstock->stock->whole_price ? number_format($movingstock->stock->whole_price) : "";
            })
            ->addColumn('retail_price', function($movingstock){
                return $movingstock->stock->retail_price ? number_format($movingstock->stock->retail_price) : "";
            })
            ->addColumn('av_cost_price')
            ->addColumn('av_rt_cost_price')
            ->addColumn('rt_qty')
            ->addColumn('all_qty')
            ->addColumn('tt_av_cost_price')
            ->addColumn('tt_av_rt_cost_price')
            ->addColumn('last_supply_date', function(Movingstock $movingstock){
                return $movingstock->last_supply_date != NULL ? eng_str_date($movingstock->last_supply_date) : "N/A";
            })
            ->addColumn('last_supply_quantity');
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
            Column::add()->index()->title('SN')->visibleInExport(false),
            Column::make('Stock ID', 'stock_id')->sortable(),
            Column::make('Name', 'name') ->sortable()->searchable(),
            Column::make('Category', 'category') ->sortable()->searchable(),
            Column::make('Box', 'box'),
            Column::make('Carton', 'formatted_carton'),
            Column::make('Threshold', 'threshold'),
            Column::make('Whole Price', 'whole_price'),
            Column::make('Retail Price', 'retail_price'),
            Column::make('Av.Cost price', 'av_cost_price'),
            Column::make('Av. Rt .Cost price', 'av_rt_cost_price'),
            Column::make('Rt Qty', 'rt_qty'),
            Column::make('WS,MS,BS Qty', 'all_qty'),
            Column::make('Daily Qty Sold', 'daily_qty_sold'),
            Column::make('Av. Inventory', 'average_inventory'),
            Column::make('Turn Over rate', 'turn_over_rate'),
            Column::make('Turn Over rate2', 'turn_over_rate2'),
            Column::make('Worth', 'tt_av_cost_price'),
            Column::make('RT Worth', 'tt_av_rt_cost_price'),
            Column::make('Last Supplier', 'supplier_name') ->sortable()->searchable(),
            Column::make('Last Sup. Date', 'last_supply_date'),
            Column::make('Last Sup. Qty', 'last_supply_quantity'),
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
