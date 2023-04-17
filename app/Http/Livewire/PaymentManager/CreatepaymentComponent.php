<?php

namespace App\Http\Livewire\PaymentManager;

use App\Models\Creditpaymentlog;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;


class CreatepaymentComponent extends Component
{
    use LivewireAlert;

    public Invoice $invoice;
    public Creditpaymentlog $creditpaymentlog;

    public string $invoice_number = "";

    public string $customer_id = "";

    public string $amount = "";

    public string $deposit_customer_id = "";

    public string $deposit_amount = "";

    public function boot()
    {

    }

    public function mount()
    {


        $this->invoice = new Invoice();
        $this->creditpaymentlog =new Creditpaymentlog();

        $this->invoice->sub_total = 0;
        $this->invoice->discount_amount = 0;

        $this->creditpaymentlog->sub_total = 0;
        $this->creditpaymentlog->discount_amount = 0;


    }

    public function render()
    {
        return view('livewire.payment-manager.createpayment-component');
    }


    public function createInvoicePayment()
    {
        $invoice = Invoice::where('invoice_number', $this->invoice_number)->first();
        if(!$invoice)
        {
            $this->alert(
                "error",
                "Create Payment",
                [
                    'position' => 'center',
                    'timer' => 2000,
                    'toast' => false,
                    'text' =>  "Invoice not found, please check invoice number and try again",
                ]
            );
        }else {

            if($invoice->status_id !== status('Draft')){

                $this->alert(
                    "error",
                    "Create Payment",
                    [
                        'position' => 'center',
                        'timer' => 2000,
                        'toast' => false,
                        'text' =>  "Invoice status has already been paid/cancelled//dispatched",
                    ]
                );

                return false;
            }

            $this->invoice = $invoice;
            $this->emit('generateInvoicePayment',$this->invoice->id);
            $this->dispatchBrowserEvent("openinvoicePaymentModal", []);
        }
    }


    public function createCreditPayment()
    {
        $this->emit('generateCreditPayment',['customer_id'=>$this->customer_id, 'amount'=>$this->amount]);
        $this->dispatchBrowserEvent("opencreditPaymentModal",[]);

    }

    public function createDepositPayment()
    {
        $this->emit('generateDepositPayment',['customer_id'=>$this->deposit_customer_id, 'amount'=>$this->deposit_amount]);
        $this->dispatchBrowserEvent("opendepositPaymentModal",[]);

    }

}
