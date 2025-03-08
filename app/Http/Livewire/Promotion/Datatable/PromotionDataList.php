<?php

namespace App\Http\Livewire\Promotion\Datatable;

use App\Models\Promotion;
use App\Traits\PowerGridComponentTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\{Button,
    Column,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent};
use Jantinnerezo\LivewireAlert\LivewireAlert;
use PowerComponents\LivewirePowerGrid\Filters\Filter;
use PowerComponents\LivewirePowerGrid\Rules\{Rule, RuleActions};
use PowerComponents\LivewirePowerGrid\Traits\{ActionButton, WithExport};

final class PromotionDataList extends PowerGridComponent
{
    use PowerGridComponentTrait, LivewireAlert;

    public $key = "id";
    public $promoId ;

    public array $filters;
    /*
    |--------------------------------------------------------------------------
    |  Datasource
    |--------------------------------------------------------------------------
    | Provides data to your Table using a Model or Collection
    |
    */

    protected function getListeners(): array
    {
        return array_merge(
            parent::getListeners(), [
            'delete_promo' => 'confirm_delete_promo',
            'approve_promo' => 'confirm_approve_promo',
            'stop_promo' => 'confirm_stop_promo',
            'deletePromo' => 'deletePromo',
            'approvePromo' => 'approvePromo',
            'stopPromo' => 'stopPromo',

        ]);
    }

    /**
     * PowerGrid datasource.
     *
     * @return Builder<\App\Models\Promotion>
     */
    public function datasource(): Builder
    {
        return Promotion::query();
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
            ->addColumn('name')
            ->addColumn('user', fn (Promotion $model) => $model->user->name)
            ->addColumn('status',  fn (Promotion $model) => showStatus($model->status_id))
            ->addColumn('from_date_formatted', fn (Promotion $model) => Carbon::parse($model->from_date)->format('d/m/Y'))
            ->addColumn('end_date_formatted', fn (Promotion $model) => Carbon::parse($model->end_date)->format('d/m/Y'))
            ->addColumn('created_formatted', fn (Promotion $model) => Carbon::parse($model->created)->format('d/m/Y'))
            ->addColumn('created_at_formatted', fn (Promotion $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
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
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('User', 'user'),
            Column::make('Date Created', 'created_formatted', 'created')->sortable(),
            Column::make('Status', 'status'),
            Column::make('From date', 'from_date_formatted', 'from_date')->sortable(),
            Column::make('End date', 'end_date_formatted', 'end_date')->sortable(),
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
     * PowerGrid Promotion Action Buttons.
     *
     * @return array<int, Button>
     */


    public function actions(): array
    {
        return [
            Button::make('edit', 'Edit')
                ->class('btn btn-sm btn-primary')
                ->route('promo.update', ['promotion' =>'id']),

            Button::make('approve', 'Approve')
                ->class('btn btn-sm btn-success')
                ->emit('approve_promo', fn (Promotion $promotion) => ['id'=> $promotion->id]),

            Button::make('stop-promotion', 'Stop Promotion')
                ->class('btn btn-sm btn-warning')
                ->emit('stop_promo', fn (Promotion $promotion) => ['id'=> $promotion->id]),

            Button::make('view', 'View')
                ->class('btn btn-sm btn-secondary')
                ->route('promo.show', ['promotion' =>'id']),

            Button::add('destroy')
                ->caption('Delete')
                ->class('btn btn-sm btn-danger')
                ->emit('delete_promo', fn (Promotion $promotion) => ['id'=> $promotion->id])
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
     * PowerGrid Promotion Action Rules.
     *
     * @return array<int, RuleActions>
     */


    public function actionRules(): array
    {
        return [

            //Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($promotion) => !userCanView('promo.update'))
                ->hide(),
            Rule::button('destroy')
                ->when(fn($promotion) => !userCanView('promo.destroy'))
                ->hide(),
            Rule::button('view')
                ->when(fn($promotion) => !userCanView('promo.show'))
                ->hide(),
            Rule::button('approve')
                ->when(fn($promotion) => !(userCanView('promo.approve') && $promotion->status_id === status('Pending')))
                ->hide(),
            Rule::button('stop-promotion')
                ->when(fn($promotion) => !(userCanView('promo.approve') && $promotion->status_id === status('Approved')))
                ->hide(),
        ];
    }


    public function deletePromo( array $data)
    {
        Promotion::find($this->promoId)->delete();
        $this->refresh();
    }

    public function confirm_approve_promo( array $data){
        $this->promoId = $data['id'];

        $this->alert('warning', 'Are you sure , you want to approve this promotion ? ' , [
            'icon'=>'warning',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Approve',
            'cancelButtonText' => 'Cancel',
            'onConfirmed' => 'approvePromo',
            'allowOutsideClick' => false,
            'timer' => null ,
            'position' => 'center',
        ]);
    }

    public function confirm_delete_promo( array $data){

        $this->promoId = $data['id'];

        $this->alert('warning', 'Are you sure , you want to delete this promo ? ' , [
            'icon'=>'warning',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Delete',
            'cancelButtonText' => 'Cancel',
            'allowOutsideClick' => false,
            'timer' => null ,
            'position' => 'center',
            'onConfirmed' => 'deletePromo'
        ]);
    }


    public function confirm_stop_promo( array $data){

        $this->promoId = $data['id'];

        $this->alert('warning', 'Are you sure , you want to stop running this promo ? ' , [
            'icon'=>'warning',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Stop Promotion',
            'cancelButtonText' => 'Cancel',
            'allowOutsideClick' => false,
            'timer' => null ,
            'position' => 'center',
            'onConfirmed' => 'stopPromo'
        ]);
    }


    public function approvePromo( array $data)
    {
        $promo = Promotion::findorfail($this->promoId);
        $promo->status_id = status('Approved');
        $promo->promotion_items()->update(['status_id'=> status('Approved')]);
        $promo->update();
        $this->refresh();
    }

    public function stopPromo( array $data)
    {
        $promo = Promotion::findorfail($this->promoId);
        $promo->status_id = status('Pending');
        $promo->promotion_items()->update(['status_id'=> status('Pending')]);
        $promo->update();
        $this->refresh();
    }
}
