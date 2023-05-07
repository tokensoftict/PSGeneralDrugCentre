<?php
namespace App\Traits;


use App\Classes\Settings;
use PowerComponents\LivewirePowerGrid\{
    Exportable,
    Footer,
    Header,
    };

use PowerComponents\LivewirePowerGrid\Rules\{RuleActions};
use PowerComponents\LivewirePowerGrid\Traits\{ActionButton, WithExport};

trait PowerGridComponentTrait
{
    use ActionButton;
    use WithExport;


    public function setUp(): array
    {
        $this->deferLoading = true;

        $this->showCheckBox();
        $this->primaryKey = $this->key;

        return [
            Exportable::make('export')
                ->type(
                    Exportable::TYPE_XLS,
                    Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage(100, Settings::$perPageAccepted)
                ->showRecordCount(),
        ];
    }

}
