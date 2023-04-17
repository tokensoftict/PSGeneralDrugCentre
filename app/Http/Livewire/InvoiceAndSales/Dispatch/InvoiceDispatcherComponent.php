<?php

namespace App\Http\Livewire\InvoiceAndSales\Dispatch;

use App\Models\Invoice;
use App\Models\User;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class InvoiceDispatcherComponent extends Component
{
    use LivewireAlert;

    public Invoice $invoice;

    public $users;

    public array $data;

    public function boot()
    {

    }

    public function mount()
    {
        $this->users = User::where('usergroup_id',2)->where('status',1)->get();

        $this->data['picked_by'] = "";
        $this->data['checked_by'] = "";
        $this->data['packed_by'] = "";
        $this->data['dispatched_by'] = "";
        $this->data['carton_no'] = "";

    }

    public function render()
    {
        return view('livewire.invoice-and-sales.dispatch.invoice-dispatcher-component');
    }

    public function dispatchedInvoice()
    {
        $this->validate(
            [
                'data.picked_by' => 'required',
                'data.checked_by' => 'required',
                'data.packed_by' => 'required',
                'data.dispatched_by' => 'required',
                'data.carton_no' => 'required'
            ]
        );

        $this->data['status_id'] = status('Complete');

        $this->invoice->update($this->data);


        $this->dispatchBrowserEvent('refreshBrowser', ['link'=>route('invoiceandsales.view',$this->invoice->id)]);

        logActivity($this->invoice->id, $this->invoice->invoice_number,'Invoice dispatched');

        if($this->invoice->online_order_status == "1"){

            _GET('processorder/'.$this->invoice->onliner_order_id."/4?cartoon=".$this->invoice->carton_no);

            logActivity($this->invoice->id, $this->invoice->invoice_number,'Online invoice -- dispatched update was sent to the serve');
        }


        $this->alert(
            "success",
            "Invoice Dispatcher",
            [
                'position' => 'center',
                'timer' => 1500,
                'toast' => false,
                'text' =>  "Invoice has been dispatched successfully, Invoice is now completed!",
            ]
        );

    }

}
