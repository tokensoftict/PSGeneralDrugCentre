<?php

namespace App\Http\Livewire\ProductModule\Report;

use App\Models\Stockbatch;
use App\Models\Stockopening;
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

final class StockOpeningReport extends PowerGridComponent
{
    use PowerGridComponentTrait;

    public $key = "id";

    public array  $filters;
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
     * @return Builder<\App\Models\Stockopening>
     */
    public function datasource(): Builder
    {
        $date = $this->filters['payment_date'];

        return Stockopening::query()->with(['stock', 'supplier', 'stock.category', 'stock.promotion_items'])->where('date_added', $date);
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
            'stock' => [
                'name',
            ],
            'supplier' => [
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
            ->addColumn('stock_id')
            ->addColumn('name', function(Stockopening $stockbatch){
                return $stockbatch->stock->name;
            })
            ->addColumn('category', function(Stockopening $stockbatch){
                return $stockbatch->stock->category->name ?? "N/A";
            })
            ->addColumn('box', function (Stockopening $stockopening){
                return $stockopening->stock->box;
            })
            ->addColumn('carton', function (Stockopening $stockopening){
                return $stockopening->stock->carton;
            })->addColumn('av_qty', function (Stockopening $stockopening){
                return $stockopening->stock->cacheTotalBalance();
            })
            ->addColumn('average_retail_cost_price')
            ->addColumn('average_cost_price')
            ->addColumn('wholesales')
            ->addColumn('bulksales')
            ->addColumn('retail')
            ->addColumn('quantity')
            ->addColumn('supplier', function(Stockopening $stockopening){
                return $stockopening->supplier->name ?? "NILL" ;
            })
            ->addColumn('total');
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
            Column::make('Stock ID', 'stock_id')->sortable()
                ->searchable(),
            Column::make('Name', 'name')->sortable()
                ->searchable(),
            Column::make('Category', 'category')->sortable()
                ->searchable(),
            Column::make('Box', 'box')->sortable()
                ->searchable(),
            Column::make('Carton', 'carton')->sortable()
                ->searchable(),
            Column::make('Current Quantity Available', 'av_qty')->sortable()
                ->searchable(),
            Column::make('Wholesales', 'wholesales')->sortable(),
            Column::make('Bulksales', 'bulksales')->sortable(),
            Column::make('Retail', 'retail')->sortable(),
            Column::make('Average retail cost price', 'average_retail_cost_price'),
            Column::make('Average cost price', 'average_cost_price')->sortable()
                ->searchable(),
            Column::make('Main Store', 'quantity')->sortable(),
            Column::make('Total Opening Qty', 'total'),
            Column::make('Last Supplier', 'supplier'),
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
     * PowerGrid Stockopening Action Buttons.
     *
     * @return array<int, Button>
     */

    /*
    public function actions(): array
    {
       return [
           Button::make('edit', 'Edit')
               ->class('bg-indigo-500 cursor-pointer text-white px-3 py-2.5 m-1 rounded text-sm')
               ->route('stockopening.edit', function(\App\Models\Stockopening $model) {
                    return $model->id;
               }),

           Button::make('destroy', 'Delete')
               ->class('bg-red-500 cursor-pointer text-white px-3 py-2 m-1 rounded text-sm')
               ->route('stockopening.destroy', function(\App\Models\Stockopening $model) {
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
     * PowerGrid Stockopening Action Rules.
     *
     * @return array<int, RuleActions>
     */

    /*
    public function actionRules(): array
    {
       return [

           //Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($stockopening) => $stockopening->id === 1)
                ->hide(),
        ];
    }
    */
}
