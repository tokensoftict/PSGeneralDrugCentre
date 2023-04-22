<?php

namespace App\Repositories;

use App\Jobs\AddLogToCustomerLedger;
use App\Models\Creditpaymentlog;
use App\Models\Customerdeposit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Paymentmethoditem;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class PaymentRepository
{

    public InvoiceRepository $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }


    public function addPayment(array $data) : Payment
    {
        $methods = $data['methods'];

        Arr::forget($data, ['methods']);

        $data = array_merge($data, [
            'user_id' => auth()->id(),
            'payment_time' => Carbon::now()->toDateTimeLocalString(),
            'payment_date' => !isset($data['payment_date']) ? dailyDate() : $data['payment_date'],
        ]);

        $paymentItems = $this->parsePaymentMethods($methods, $data);

        $data = array_merge($data , ['total_paid' => $paymentItems['total']]);

        $payment = Payment::create($data);

        $payment->paymentmethoditems()->saveMany($paymentItems['items']);

        if(count( $paymentItems['credit']) > 0)
        {
            $credit = $payment->paymentmethoditems()->where('paymentmethod_id', 4)->first();

            $paymentItems['credit']['paymentmethoditem_id'] = $credit->id;

            $paymentItems['credit']['payment_id'] = $payment->id;

            $this->addCreditPayment($paymentItems['credit']);
        }

        if(count($paymentItems['deposit']) > 0)
        {
            $deposit = $payment->paymentmethoditems()->where('paymentmethod_id', 5)->first();

            $paymentItems['deposit']['paymentmethoditem_id'] =  $deposit->id;

            $paymentItems['deposit']['payment_id'] = $payment->id;

            $this->addDepositPayment( $paymentItems['deposit']);
        }

        if($payment->invoice_type == Invoice::class){

            $payment->invoice()->update([
                'payment_id' => $payment->id,
                'status_id' =>  (auth()->user()->department_id === 4 ? status('Complete') : status('Paid')),
                'total_amount_paid' =>$paymentItems['total']
            ]);

            logActivity($payment->invoice->id, $payment->invoice->invoice_number, "Payment(s) was added to invoice Status :".$payment->invoice->status->name);
        }

        $totalPaid = $payment->paymentmethoditems()->whereIn('paymentmethod_id', [1,2,3])->sum('amount');

        if($totalPaid > 0 && $payment->customer_id !== 1) { // customer ledger for walking customer
            dispatch(new AddLogToCustomerLedger([
                'payment_id' => $payment->id,
                'invoice_id' => NULL,
                'customer_id' => $payment->customer_id,
                'amount' => $totalPaid,
                'transaction_date' => $payment->payment_date,
                'user_id' => auth()->id(),
            ]));
        }

        return $payment;

    }



    public function parsePaymentMethods(array $methods, array $data) : array
    {
        $pmethods = [];

        $credit = [];

        $deposit = [];

        foreach ($methods as $method)
        {
            $pmethods[] = new Paymentmethoditem(
                array_merge($method, [
                    'user_id' => $data['user_id'],
                    'customer_id' => $data['customer_id'],
                    'invoice_type' => $data['invoice_type'],
                    'invoice_id' => $data['invoice_id'],
                    'payment_date' => $data['payment_date']
                ])
            );

            if($method['paymentmethod_id'] == "4")
            {
                $credit = [
                    'credit_number' => creditPaymentReference(),
                    'user_id' =>$data['user_id'],
                    'paymentmethod_id' => NULL,
                    'customer_id' => $data['customer_id'],
                    'paymentmethoditem_id' => NULL,
                    'payment_id' => NULL,
                    'invoicelog_type' => $data['invoice_type'],
                    'invoicelog_id' => $data['invoice_id'],
                    'amount' => -($method['amount']),
                    'payment_date' => $data['payment_date']
                ];
            }

            if($method['paymentmethod_id'] == "5")
            {
                $deposit = [
                    'deposit_number' => depositPaymentReference(),
                    'payment_id' => NULL,
                    'customer_id' => $data['customer_id'],
                    'amount' => -($method['amount']),
                    'created_by' => auth()->id(),
                    'last_updated_by' =>auth()->id(),
                    'paymentmethoditem_id' => NULL,
                    'deposit_date' => todaysDate(),
                    'deposit_time' => Carbon::now()->toDayDateTimeString(),
                    'description' => " ",
                ];
            }

        }

        return [
            'total' => collect($methods)->sum('amount'),
            'items' => $pmethods,
            'credit' => $credit,
            'deposit' => $deposit
        ];
    }



    public function addCreditPayment(array $data)
    {
        $credit = Creditpaymentlog::create($data);
        $credit->customer->updateCreditBalance();
    }

    public function addDepositPayment(array $data)
    {
       // Customerdeposit::create($data);
    }

    public function getPaymentinfo(&$component) : array
    {
        if($component->payment_method === "1")
        {
            return [
                'cash_tendered' => $component->cash_tendered,
                'change' => $component->cash_tendered - $component->sub_total
            ];
        }
        else if($component->payment_method === "2" || $component->payment_method === "3")
        {
            return [
                'bank_account_id' => $component->bank_account_id
            ];
        }
        else {
            return [

            ];
        }
    }


    public  function validateSplitPayment(&$obj) : bool
    {

        $tt = collect($obj->split_payments)->sum(function($item){
            return (float)$item['amount'] != "" ?  (float)$item['amount'] : 0 ;
        });

        $obj->totalSplitAmount = money($tt);

        if(isset($obj->split_payments[3])) {
            if ($obj->split_payments[2]['amount'] > 0 && $obj->split_payments[2]['bank_account_id'] == "") return false;
        }

        if(isset($obj->split_payments[3])) {
            if ($obj->split_payments[3]['amount'] > 0 && $obj->split_payments[3]['bank_account_id'] == "") return false;
        }

        if(isset($obj->split_payments[5])) {
            if ($obj->split_payments[5]['amount'] > 0 && $this->validateDepositPayment($obj, $obj->split_payments[5]['amount']) === false) return false;
        }
        if(isset($obj->split_payments[5])) {
            if ($obj->split_payments[5]['amount'] == 0) {
                $obj->error_deposit = "";
            }
        }

        if(($tt < $obj->sub_total)) return false;

        if(($tt > $obj->sub_total)) return false;


        return true;

    }


    public  function updateDisplay(&$obj) : void
    {

        $this->error_deposit = "";

        if ($obj->payment_method === "6") {

            $obj->btnEnabled =  $this->validateSplitPayment( $obj);
        }
        else if($obj->payment_method === "1") {

            if(!($obj->cash_tendered === "" || $obj->cash_tendered < $obj->sub_total))
            {
                $obj->change =   $obj->cash_tendered - $obj->sub_total;
                if($obj->change > $obj->sub_total){
                    $obj->btnEnabled = false;
                }else {
                    $obj->btnEnabled = true;
                }
            }else{
                $obj->btnEnabled = false;
            }

        }
        else if($obj->payment_method === "2" || $obj->payment_method === "3"){

            if($obj->bank_account_id != "")
            {
                $obj->btnEnabled = true;
            }
        }else if($obj->payment_method === "5"){

            if(!$this->validateDepositPayment($obj, $obj->sub_total))
            {
                $obj->btnEnabled = false;

            }else{

                $obj->btnEnabled = true;
            }
        }
        else if($obj->payment_method === "")
        {
            $obj->btnEnabled = false;
        }
        else{

            $obj->btnEnabled = true;
        }

    }



    public function bridgePayment(&$obj, &$payment_data, &$payment_data_items): void
    {
        if($obj->payment_method === "6")
        {
            $payment_data['total_paid'] = collect($obj->split_payments)->sum('amount');

            foreach ($obj->split_payments as $key=>$split_payment)
            {
                if( $split_payment['amount'] > 0) {
                    $payment_data_items[] = [
                        'amount' => $split_payment['amount'],
                        'paymentmethod_id' => $key,
                        'payment_info' => json_encode($obj->split_payments[$key]),
                    ];
                }
            }
        }
        else {
            $payment_data['total_paid'] = $obj->sub_total;

            $payment_data_items[] = [
                'amount' => $obj->sub_total,
                'paymentmethod_id' => $obj->payment_method,
                'amount_tendered' => $obj->cash_tendered ?? NULL,
                'bank_account_id' => $obj->bank_account_id ?? NULL,
                'payment_info' => json_encode($this->getPaymentInfo($obj))
            ];
        }


        if(isset( $obj->invoice) && $obj->invoice->online_order_status == "1"){

            if((int)$obj->payment_method === 4){

                _GET('processorder/' . $obj->invoice->onliner_order_id . "/6");

                $obj->invoice->online_order_debit = 1;

                $obj->invoice->update();

            }
        }


        $payment_data['methods'] = $payment_data_items;
    }


    public function saveDepositPayment(&$obj) {

        $payment_data_items = [];

        $deposit = Customerdeposit::create([
            'deposit_number' => depositPaymentReference(),
            'payment_id' => NULL,
            'customer_id' => $obj->customer_id,
            'amount' =>  $obj->sub_total,
            'created_by' => auth()->id(),
            'last_updated_by' => auth()->id(),
            'paymentmethoditem_id' => NULL,
            'deposit_date' => todaysDate(),
            'deposit_time' => Carbon::now()->toDayDateTimeString(),
            'description' => $obj->description,
        ]);


        $payment_data = [
            'user_id' => auth()->id(),
            'customer_id' => $obj->customer_id,
            'subtotal' => $obj->sub_total,
            'payment_date' => dailyDate(),
            'invoice_number' => $deposit->deposit_number,
            'invoice_type' => Customerdeposit::class,
            'invoice_id' =>  $deposit->id
        ];

        $this->bridgePayment($obj, $payment_data, $payment_data_items);

        $payment =  $this->addPayment($payment_data);


        $deposit->payment_id = $payment->id;

        $deposit->update();

        $obj->alert(
            "success",
            "Deposit Payment",
            [
                'position' => 'center',
                'timer' => 1500,
                'toast' => false,
                'text' =>  "Deposit Payment has been added successfully!.",
            ]
        );

        $this->completePayment($obj,$payment->id);

    }


    public function saveCreditPayment(&$obj) : void
    {
        $payment_data_items = [];

        $credit = Creditpaymentlog::create(
            [
                'credit_number' => creditPaymentReference(),
                'payment_id' => NULL,
                'user_id' =>  auth()->id(),
                'paymentmethod_id' => $obj->payment_method,
                'customer_id' =>  $obj->customer_id,
                'paymentmethoditem_id' => NULL,
                'invoicelog_type' =>NULL ,
                'invoicelog_id' => NULL,
                'amount' => $obj->sub_total,
                'payment_date' => dailyDate()
            ]
        );

        $payment_data = [
            'user_id' => auth()->id(),
            'customer_id' => $obj->customer_id,
            'subtotal' => $obj->sub_total,
            'payment_date' => dailyDate(),
            'invoice_number' =>  $credit->credit_number,
            'invoice_type' => Creditpaymentlog::class,
            'invoice_id' =>  $credit->id
        ];

        $this->bridgePayment($obj, $payment_data, $payment_data_items);

        $payment =  $this->addPayment($payment_data);

        $credit->payment_id = $payment->id;

        $credit->update();

        $credit->customer->updateCreditBalance();

        if($obj->online_credit_invoice !== "")
        {
            _GET('processorder/' . $obj->online_credit_invoice . "/3");

            $in = Invoice::find($obj->online_credit_invoice);
            $in->online_order_debit = 0;
            $in->update();
        }

        $obj->alert(
            "success",
            "Credit Payment",
            [
                'position' => 'center',
                'timer' => 1500,
                'toast' => false,
                'text' =>  "Credit Payment has been added successfully!.",
            ]
        );

        $this->completePayment($obj,$payment->id);

        $obj->dispatchBrowserEvent('openPaymentPage', ['link'=>route('payment.show',$payment->id)]);
    }


    public function completePayment(&$obj, $payment_id)
    {

        if(session()->get('current_route') == "payment.create"){

            $obj->dispatchBrowserEvent('openPaymentPage', ['link'=>route('payment.show',$payment_id)]);
        }else{
            $obj->dispatchBrowserEvent('invoiceDiscountModal', []);
            $obj->dispatchBrowserEvent('refreshBrowser', []);
        }
    }


    public function savePayment(&$obj) : void
    {

        $payment_data_items = [];

        $payment_data = [
            'invoice_number' => $obj->invoice->invoice_number,
            'invoice_type' => get_class($obj->invoice),
            'subtotal' => $obj->sub_total,
            'customer_id' => $obj->invoice->customer_id,
            'invoice_id' => $obj->invoice->id,
            'payment_date' => $obj->invoice->invoice_date
        ];

        $this->bridgePayment($obj, $payment_data, $payment_data_items);

        $payment = $this->addPayment($payment_data);

        $obj->alert(
            "success",
            "Add Payment",
            [
                'position' => 'center',
                'timer' => 1500,
                'toast' => false,
                'text' =>  "Payment has been added successfully!.",
            ]
        );

        $this->completePayment($obj,$payment->id);
    }



    public function deletePayment(Payment $payment)
    {
        if($payment->invoice_type == Invoice::class) {
            logActivity($payment->invoice->id,  $payment->invoice->invoice_number, "Invoice payment was deleted");
            $payment->invoice->wipePayment();
        }else{
            $payment->invoice()->delete();
            $payment->delete();
        }

    }


    public function validateDepositPayment(&$obj, $amount) : bool
    {
        $status =  $obj->customer->deposit_balance >= $amount;
        if($status === false)
        {
            $obj->error_deposit = "Insufficient Deposit Amount, Please Add more deposit to use this method of payment";
        }
        else {
            $obj->error_deposit = "";
        }
        return $status;
    }

}
