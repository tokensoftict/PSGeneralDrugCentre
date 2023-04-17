<?php

namespace App\Http\Livewire\PaymentManager;

use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ViewpaymentComponent extends Component
{
    use LivewireAlert;


    public Payment $payment;

    private PaymentRepository $paymentRepository;

    public function boot(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.payment-manager.viewpayment-component');
    }


    public function deletePayment()
    {

        $this->paymentRepository->deletePayment($this->payment);

        $this->dispatchBrowserEvent('invoiceDiscountModal', []);
        $this->dispatchBrowserEvent('refreshBrowser', []);
        $this->alert(
            "success",
            "Show Payment",
            [
                'position' => 'center',
                'timer' => 1500,
                'toast' => false,
                'text' =>  "Payment has been deleted successfully!.",
            ]
        );

    }

}
