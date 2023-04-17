<?php

namespace App\Http\Livewire\PaymentManager;

use App\Models\Customer;
use App\Traits\PaymentComponentTrait;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AdddepositComponent extends Component
{
    use PaymentComponentTrait;


    public $amount = 0;

    public int $customer_id = 0;

    public Customer $customer;

    protected $listeners = [
        'generateDepositPayment' => 'generateDepositPayment',
    ];

    public function mount()
    {
        $this->mountProperties();

        $this->payments = $this->payments->filter(function($payment){
            return ($payment->id !== 4 && $payment->id !== 5);
        });

        $this->sub_total = $this->amount;

        $this->totalDeposit = 0;
    }

    public function render()
    {
        $this->updateDisplay($this);

        return view('livewire.payment-manager.adddeposit-component');
    }
}
