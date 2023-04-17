<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user) : bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Invoice $invoice) : bool
    {
        //if(!userCanView(type().'view')) return false;

        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user) : bool
    {
        if(!userCanView('invoiceandsales.create')) return false;

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Invoice $invoice) : bool
    {
        return $this->edit($user, $invoice);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function edit(User $user, Invoice $invoice) : bool
    {
        if(!userCanView(type()."edit")) return false;

        if(in_array($invoice->status_id, [
            status('Dispatched'),
            status('Paid'),
            status('Deleted'),
            status('Discount'),
        ])) return false;

        return true;
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function return(User $user, Invoice $invoice) : bool
    {
        if(!userCanView("invoiceandsales.return")) return false;

        if(!in_array($invoice->status_id, [
            status('Dispatched'),
            status('Paid'),
            status('Complete'),
        ])) return false;

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Invoice $invoice) : bool
    {
        if(!userCanView('invoiceandsales.destroy')) return false;

        if(in_array($invoice->status_id, [
            status('Dispatched'),
            status('Complete'),
            status('Paid'),
            status('Deleted'),
        ])) return false;

        return true;

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function pay(User $user, Invoice $invoice) : bool
    {
        if(!userCanView('payment.createInvoicePayment')) return false;

        if($invoice->status_id !== status("Draft"))  return false;

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function printAfour(User $user, Invoice $invoice)
    {
        if(!userCanView("invoiceandsales.print_afour")) return false;

        if(
            $invoice->status_id !== status("Dispatched") &&
            $invoice->status_id !== status("Paid") &&
            $invoice->status_id !== status("Complete")
        )  return false;

        return true;
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function printThermal(User $user, Invoice $invoice)
    {
        if(!userCanView(type()."pos_print")) return false;

        if($invoice->department === 'retail' && ($invoice->status_id !== status('Complete') || $invoice->status_id !== status('Paid'))) return false;

        if($invoice->status_id ==  status('Deleted')) return false;

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function printWaybill(User $user, Invoice $invoice)
    {
        if(!userCanView("invoiceandsales.print_way_bill")) return false;

        if(
            $invoice->status_id !== status("Dispatched") &&
            $invoice->status_id !== status("Paid") &&
            $invoice->status_id !== status('Complete')
        )  return false;

        return true;
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function dispatched(User $user, Invoice $invoice)
    {
        if(!userCanView("invoiceandsales.dispatchInvoice")) return false;

        if($invoice->status_id !== status("Paid"))  return false;

        return true;
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function applyProductDiscount(User $user, Invoice $invoice)
    {
        if(!userCanView("invoiceandsales.applyProductDiscount")) return false;

        if($invoice->status_id !== status("Discount"))  return false;

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function applyInvoiceDiscount(User $user, Invoice $invoice)
    {
        if(!userCanView("invoiceandsales.applyInvoiceDiscount")) return false;

        if($invoice->status_id !== status("Discount"))  return false;

        return true;
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function showPayment(User $user, Invoice $invoice)
    {
        //if(!userCanView("invoiceandsales.applyProductDiscount")) return false;

        if(!(in_array($invoice->status_id, [
            status("Paid")
            ,status("Dispatched"),
            status('Complete'),
            ])))  return false;

        return true;
    }

}
