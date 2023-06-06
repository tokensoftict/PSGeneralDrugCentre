<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

abstract class ExportDataTableComponent extends DataTableComponent
{


    public function getExportBuilder() : Builder
    {
        $this->setupColumnSelect();
        $this->setupPagination();
        $this->setupSecondaryHeader();
        $this->setupFooter();
        $this->setupReordering();
        $this->baseQuery();
       return $this->getBuilder();
    }

}
