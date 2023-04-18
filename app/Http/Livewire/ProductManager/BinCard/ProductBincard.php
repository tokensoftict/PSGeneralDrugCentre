<?php

namespace App\Http\Livewire\ProductManager\BinCard;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Stockbincard;

class ProductBincard extends DataTableComponent
{
    protected $model = Stockbincard::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Stock id", "stock_id")
                ->sortable(),
            Column::make("Bin card type", "bin_card_type")
                ->sortable(),
            Column::make("Bin card date", "bin_card_date")
                ->sortable(),
            Column::make("User id", "user_id")
                ->sortable(),
            Column::make("In qty", "in_qty")
                ->sortable(),
            Column::make("Out qty", "out_qty")
                ->sortable(),
            Column::make("Sold qty", "sold_qty")
                ->sortable(),
            Column::make("Return qty", "return_qty")
                ->sortable(),
            Column::make("Stockbatch id", "stockbatch_id")
                ->sortable(),
            Column::make("To department", "to_department")
                ->sortable(),
            Column::make("From department", "from_department")
                ->sortable(),
            Column::make("Supplier id", "supplier_id")
                ->sortable(),
            Column::make("Invoice id", "invoice_id")
                ->sortable(),
            Column::make("Stocktransfer id", "stocktransfer_id")
                ->sortable(),
            Column::make("Purchase id", "purchase_id")
                ->sortable(),
            Column::make("Balance", "balance")
                ->sortable(),
            Column::make("Comment", "comment")
                ->sortable(),
            Column::make("Department balance", "department_balance")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
        ];
    }
}
