<?php
namespace App\Traits;
use App\Classes\Settings;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Rappasoft\LaravelLivewireTables\Views\Column;

trait SimpleDatatableComponentTrait
{
    use LivewireAlert;

    public array $_columns;

    public array $iconsLink;

    public array $unitConfig = [];

    public int $index;



    public function boot(): void
    {

        $this->listeners = [
            'refreshData' => '$refresh',
        ];
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setOfflineIndicatorDisabled();
        $this->setQueryStringDisabled();

        //$this->setAdditionalSelects([\DB::raw('SUM(purchaseitems.cost_price) as purchaseitems.total_cost_price')]);

        //$this->setEagerLoadAllRelationsEnabled();
        $this->setEmptyMessage('No Data found..');
        $this->setTableAttributes([
            'class' => 'table-bordered table-striped table-nowrap mb-0',
        ]);

    }


    public function columns(): array
    {
        $this->index = $this->page > 1 ? ($this->page - 1) * $this->perPage : 0;
        return [
            Column::make('No.','id')->format(fn () => ++$this->index),
            ...self::mountColumn()
        ];
    }


    public function edit($id)
    {
        $this->emit('editData',$id);
    }


    public function toggle($id)
    {
        $this->emit('toggleData',$id);
    }

    public function destroy($id)
    {
        $this->emit('destoryData',$id);
    }

}
