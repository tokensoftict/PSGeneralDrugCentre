<?php

namespace App\Http\Livewire\InvoiceAndSales\Scan;

use App\Repositories\InvoiceRepository;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class InvoiceCheckoutScanBarCode extends Component
{
    use LivewireAlert;

    public string $invoice_number;

    private InvoiceRepository $invoiceRepository;

    public function boot()
    {

    }

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.invoice-and-sales.scan.invoice-checkout-scan-bar-code');
    }

    public function getListeners()
    {
        return [
            'confirmed'
        ];
    }

    public function checkoutInvoice(){
        $invoiceRepo = (new InvoiceRepository());
        $this->invoice_number = (int) $this->invoice_number;
        $invoice = $invoiceRepo->findByInvoiceNumber($this->invoice_number);

        if(!$invoice) {
            $this->alert(
                "error",
                "Invoice Checkout",
                [
                    'position' => 'center',
                    'toast' => false,
                    'timer' => 100000000000,
                    'showConfirmButton' => true,
                    'confirmButtonText' => 'Okay',
                    'onConfirmed' => 'confirmed',
                    'text' => 'Invoice ('.$this->invoice_number.') not found, Please make sure you are scanning the correct invoice number'
                ]
            );
        } else{
            $checkout = $invoiceRepo->checkOut($invoice);

            if($checkout['status'] === true){
                $this->alert(
                    "success",
                    "Invoice Checkout",
                    [
                        'position' => 'center',
                        'toast' => false,
                        'timer' => 100000000000,
                        'showConfirmButton' => true,
                        'confirmButtonText' => 'Okay',
                        'onConfirmed' => 'confirmed',
                        'text' => 'Invoice has been checkout successfully!'
                    ]
                );
            }else{
                $this->alert(
                    "error",
                    "Invoice Checkout",
                    [
                        'position' => 'center',
                        'toast' => false,
                        'timer' => 100000000000,
                        'showConfirmButton' => true,
                        'confirmButtonText' => 'Okay',
                        'onConfirmed' => 'confirmed',
                        'text' => $checkout['message']
                    ]
                );
            }
        }
    }

    public function confirmed()
    {
        return redirect()->route('invoiceandsales.checkoutScan');
    }
}
