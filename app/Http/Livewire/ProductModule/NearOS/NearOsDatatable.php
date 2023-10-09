<?php

namespace App\Http\Livewire\ProductModule\NearOS;

use App\Classes\Settings;
use App\Models\Nearoutofstock;
use App\Models\Stock;
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
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Filters\Filter;
use PowerComponents\LivewirePowerGrid\Rules\{RuleActions};
use PowerComponents\LivewirePowerGrid\Traits\{ActionButton, WithExport};

final class NearOsDatatable extends PowerGridComponent
{
    use PowerGridComponentTrait;

    public $key = 'nearoutofstocks.id';

    /*
    |--------------------------------------------------------------------------
    |  Features Setup
    |--------------------------------------------------------------------------
    | Setup Table's general features
    |
    */
    protected function getListeners(): array
    {
        return array_merge(
            parent::getListeners(), [
            'view_stock' => 'view_stock',
        ]);
    }


    public function datasource(): Builder
    {
        return Nearoutofstock::query()
            ->with(['stock', 'stockgroup', 'stockgroup.oneStock'])
            ->select(
                [
                    'nearoutofstocks.*',
                    'stocks.id as stock_id',
                    'stocks.name as stock_name',
                    'stocks.box as box',
                    'stocks.carton as carton',
                    'categories.name as category_name',
                    'suppliers.name as supplier_name',
                    'stockgroups.name as group_name',
                    DB::raw('(CASE
                        WHEN nearoutofstocks.stockgroup_id IS NOT NULL THEN stockgroups.name
                        ELSE stocks.name
                    END) AS name')
                ]
            )
            ->where("nearoutofstocks.threshold_type", "<>", "")
            ->where('is_grouped',0)
            ->leftJoin('stocks', function ($stocks) {
                $stocks->on('nearoutofstocks.stock_id', '=', 'stocks.id');
            })
            ->leftJoin('categories', 'stocks.category_id', '=', 'categories.id')
            ->leftJoin('stockgroups', function ($stockgroups) {
                $stockgroups->on('nearoutofstocks.stockgroup_id', '=', 'stockgroups.id');
            })
            ->leftJoin('suppliers', function ($suppliers) {
                $suppliers->on('nearoutofstocks.supplier_id', '=', 'suppliers.id');
            })->orderBy('id', 'DESC');
        //->whereNotNull('stocks.name');
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
            'stockgroup' => [
                'name',
            ],
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
            ->addColumn('stock_id')
            ->addColumn('name')
            ->addColumn('threshold_type', function(Nearoutofstock $nearoutofstock){
                return $nearoutofstock->threshold_type == "" ? "THRESHOLD" : $nearoutofstock->threshold_type;
            })
            ->addColumn('box', function(Nearoutofstock $nearoutofstock){
                return $nearoutofstock->stock->box ?? $nearoutofstock->stockgroup?->oneStock?->box;
            })
            ->addColumn('carton', function(Nearoutofstock $nearoutofstock){
                return $nearoutofstock->stock->carton ?? $nearoutofstock->stockgroup?->oneStock?->carton;
            })
            ->addColumn('category_name', function (Nearoutofstock $nearoutofstock){
                if($nearoutofstock->stockgroup_id === NULL) return $nearoutofstock->category_name;
                if($nearoutofstock->stockgroup_id !== NULL) return $nearoutofstock->stockgroup->stock->category->name;
            })
            ->addColumn('os_type')
            ->addColumn('supplier_name')
            ->addColumn('threshold_value')
            ->addColumn('current_qty')
            ->addColumn('current_sold')
            ->addColumn('group_os_id')
            ->addColumn('is_grouped')
            ->addColumn('last_qty_purchased')
            ->addColumn('last_purchase_date')
            ->addColumn('last_purchase_date_fomartted', function(Nearoutofstock $nearoutofstock){
                return  (new Carbon($nearoutofstock->last_purchase_date))->format('d/m/Y');
            })
            ->addColumn('purchaseitem_id');

    }

    /*
    |--------------------------------------------------------------------------
    |  Include Columns
    |--------------------------------------------------------------------------
    | Include the columns added columns, making them visible on the Table.
    | Each column can be configured with properties, filters, actions...
    |
    */

    public function actions(): array
    {
        return [

            Button::add('view')
                ->caption('View Stock')
                ->class('btn btn-sm btn-primary')
                ->emit('view_stock', fn ($nearoutofstock) => ['group_id'=> $nearoutofstock->stockgroup_id])

        ];
    }

    public function actionRules(): array
    {
        return [
            Rule::button('view')
                ->when(fn ($nearoutofstock) => $nearoutofstock->stockgroup_id === NULL)
                ->hide()

        ];
    }

    /**
     * PowerGrid Columns.
     *
     * @return array<int, Column>
     */
    public function columns(): array
    {
        return [
            Column::add()->index()->title('SN')->visibleInExport(false),
            Column::make('Product ID', 'stock_id'),
            Column::make('Name', 'name','name')->searchable()->sortable(),
            Column::make('Box', 'box','box')->sortable(),
            Column::make('Carton', 'carton','carton')->sortable(),
            Column::make('Category Name', 'category_name','category_name')->sortable(),
            Column::make('Qty to Buy', 'qty_to_buy')->sortable(),
            Column::make('Supplier', 'supplier_name', 'supplier_name')->sortable()->searchable(),
            Column::make('Threshold type', 'threshold_type')->sortable(),
            Column::make('Threshold value', 'threshold_value')->sortable(),
            Column::make('Stock Quantity', 'current_qty')->sortable(),
            Column::make('Total Sold', 'current_sold')->sortable(),
            Column::make('Last Qty Pur.', 'last_qty_purchased'),
            Column::make('Last Date Pur.', 'last_purchase_date_fomartted'),
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


    public function view_stock(array $data)
    {
        $this->emit('showModal', 'product-module.near-os.view-near-os-grouped-stock', $data['group_id']);
    }

}
