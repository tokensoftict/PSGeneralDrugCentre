<?php

namespace App\Listeners;

use App\Events\UpdateOnlineOrderStatusEvent;
use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PaymentConfirmOnlineOrder
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UpdateOnlineOrderStatusEvent  $event
     * @return void
     */
    public function handle(UpdateOnlineOrderStatusEvent $event)
    {
        if($event->invoice->online_order_status == "1") {
            if($event->invoice->status_id == status('Paid')) {
                if ($event->invoice->online_credit_invoice !== "") {
                    _GET('processorder/' . $event->invoice->online_credit_invoice . "/3");

                    $in = Invoice::find($event->invoice->online_credit_invoice);
                    $in->online_order_debit = 0;
                    $in->update();
                }
            }
        }
    }
}
