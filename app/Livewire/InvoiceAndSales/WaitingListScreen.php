<?php

namespace App\Livewire\InvoiceAndSales;

use App\Models\WaitingCustomer;
use Livewire\Component;

class WaitingListScreen extends Component
{

    public function render()
    {
        return view('livewire.invoice-and-sales.waiting-list-screen', [
            'waitingList' => WaitingCustomer::with('invoice.customer')
                ->where('date_added', now()->format('Y-m-d'))
                ->where('status', '<>', WaitingCustomer::$waitingInvoiceStatus['complete'])
                ->orderBy('entered_at')
                ->get(),
        ]);
    }
}
