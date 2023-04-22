<?php

namespace App\Http\Livewire\PaymentManager\Datatable;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Creditpaymentlog;

class CreditPaymentLogs extends DataTableComponent
{
    protected $model = Creditpaymentlog::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Credit number", "credit_number")
                ->sortable(),
            Column::make("Payment id", "payment_id")
                ->sortable(),
            Column::make("User id", "user_id")
                ->sortable(),
            Column::make("Paymentmethod id", "paymentmethod_id")
                ->sortable(),
            Column::make("Customer id", "customer_id")
                ->sortable(),
            Column::make("Paymentmethoditem id", "paymentmethoditem_id")
                ->sortable(),
            Column::make("Invoicelog type", "invoicelog_type")
                ->sortable(),
            Column::make("Invoicelog id", "invoicelog_id")
                ->sortable(),
            Column::make("Amount", "amount")
                ->sortable(),
            Column::make("Payment date", "payment_date")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
        ];
    }
}
