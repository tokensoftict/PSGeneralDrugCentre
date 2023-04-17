<?php

namespace App\Http\Controllers\CustomerReport;

use App\Http\Controllers\Controller;
use App\Models\Creditpaymentlog;
use App\Models\CustomerLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CustomerReportController extends Controller
{

    public function balance_sheet(Request $request)
    {
        $data = [
            'title' => 'Customer Balance Sheet Report',
            'subtitle' => 'View Customer Balance Sheet Report By Date Range and customer',
            'filters' => [
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'customer_id' => 1,
                'filters' => [
                    'between.return_date' => monthlyDateRange(),
                    'customer_id' => 1
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.return_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['customer_id'] = $data['filters']['customer_id'];
        }

        $data['opening'] = CreditPaymentLog::where('customer_id',  $data['filters']['filters']['customer_id'])->where('payment_date','<',  $data['filters']['filters']['between.return_date'][0])->sum('amount');

        $data['histories'] = CreditPaymentLog::where('customer_id', $data['filters']['filters']['customer_id'])->whereBetween('payment_date',  $data['filters']['filters']['between.return_date'])->get();


        return view('reports.customer.balance_sheet', $data);

    }

    public function customer_ledger(Request $request)
    {
        $data = [
            'title' => 'Customer Ledger  Report',
            'subtitle' => 'View Customer  Ledger Report By Date Range and customer',
            'filters' => [
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'customer_id' => 1,
                'filters' => [
                    'between.return_date' => monthlyDateRange(),
                    'customer_id' => 1
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.return_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['customer_id'] = $data['filters']['customer_id'];
        }


        $data['opening'] = CustomerLedger::where('customer_id',  $data['filters']['filters']['customer_id'])->where('transaction_date','<',  $data['filters']['filters']['between.return_date'][0])->sum('amount');

        $data['histories'] = CustomerLedger::where('customer_id', $data['filters']['filters']['customer_id'])->whereBetween('transaction_date',  $data['filters']['filters']['between.return_date'])->get();



        return view('reports.customer.customer_ledger', $data);
    }


}
