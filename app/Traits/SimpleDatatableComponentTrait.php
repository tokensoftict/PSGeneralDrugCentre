<?php
namespace App\Traits;
use App\Classes\Column;
use App\Classes\Settings;
use App\Exports\GeneralDataExport;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Maatwebsite\Excel\Facades\Excel;

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



    public function bulkActions(): array
    {
        return [
            'export_all' => 'Export All  (XLS)',
        ];
    }


    public function getExportColumns() : array{
        $columns = $this->columns();

        $titleColumn = [];

        foreach ($columns as $column)
        {
            if($column->getColumnTitle() == "No.") continue;

            if(in_array($column->getColumnTitle(), $titleColumn))  continue;

            $titleColumn[] = $column->getColumnTitle();
        }

        return $titleColumn;
    }

    public function getExportFields() : array{
        $columns = $this->columns();

        $titleField = [];

        foreach ($columns as $column)
        {
            $titleField[] = $column->getColumnField();
        }

        return $titleField;
    }

    public function export_selected()
    {
        $selected = $this->getSelected();

        if(count($selected) == 0)
        {
            $this->alert(
                "error",
                "Data Export",
                [
                    'position' => 'center',
                    'timer' => 1500,
                    'toast' => false,
                    'text' =>  "Please select at least One Record to Export",
                ]
            );

        }else {

            $export = $this->prepareExport();

            $this->clearSelected();

            return Excel::download(new GeneralDataExport($export['data'], $export['headings']), $this->getTableName() . '.xlsx');
        }
    }


    function renderValue($column, $row) : string | null
    {
        if ($column->isLabel()) {
            $value = call_user_func($column->getLabelCallback(), $row, $column);

            if ($column->isHtml()) {
                return $value;
            }

            return $value;
        }

        $value = $column->getValue($row);


        if ($column->hasFormatter()) {
            $value = call_user_func($column->getFormatCallback(), $value, $row, $column);

            $value = strip_tags($value);

            if ($column->isHtml()) {
                return $value;
            }

            return $value;
        }

        return $value;
    }

    public function export_all()
    {
        $this->clearSelected();

        $export = $this->prepareExport();

        return Excel::download(new GeneralDataExport( $export['data'],  $export['headings']), $this->getTableName().'.xlsx');
    }


    public function prepareExport() : array
    {
        $data = [];
        $headings = [];
        $rows = $this->getExportBuilder();

        $rows->chunk(1000, function($rowws) use(&$data, &$headings) {
            foreach ($rowws as $row){
                $columns_to_value = [];
                foreach ($this->getColumns() as $column){
                    if($column->getTitle() == "Action") continue;
                    if(!in_array($column->getTitle(), $headings)) {
                        $headings[] = $column->getTitle();
                    }
                    $columns_to_value[$column->getTitle()] = $this->renderValue($column, $row);
                }
                $data[] = $columns_to_value;
            }
        });

        return ['data' => $data, 'headings' => $headings];
    }


}
