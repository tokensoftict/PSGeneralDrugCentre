<?php

namespace App\Http\Controllers\CustomerReport;

use App\Http\Controllers\Controller;
use App\Models\Creditpaymentlog;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Department;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CustomerReportController extends Controller
{

    public function balance_sheet(Request $request)
    {
        $data = [
            'title' => 'Customer Balance Sheet Report',
            'subtitle' => 'View Customer Balance Sheet Report By Date Range and customer',
            'filters' => [
                'customer' => Customer::find(2),
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'customer_id' => 2,
                'filters' => [
                    'between.return_date' => monthlyDateRange(),
                    'customer_id' => 2
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['customer'] = Customer::find($data['filters']['customer_id']);
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
                'customer' => Customer::find(2),
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'customer_id' => 2,
                'filters' => [
                    'between.return_date' => monthlyDateRange(),
                    'customer_id' => 2
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['customer'] = Customer::find($data['filters']['customer_id']);
            $data['filters']['filters']['between.return_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['customer_id'] = $data['filters']['customer_id'];
        }


        $data['opening'] = CustomerLedger::where('customer_id',  $data['filters']['filters']['customer_id'])->where('transaction_date','<',  $data['filters']['filters']['between.return_date'][0])->sum('amount');

        $data['histories'] = CustomerLedger::where('customer_id', $data['filters']['filters']['customer_id'])->whereBetween('transaction_date',  $data['filters']['filters']['between.return_date'])->get();



        return view('reports.customer.customer_ledger', $data);
    }



    public function customer_ranking(Request $request)
    {
        $items = [
            [
                'id' =>2,
                'name' => department_by_id(2)->name
            ],
            [
                'id' =>3,
                'name' => department_by_id(3)->name
            ],
            [
                'id' =>4,
                'name' => department_by_id(4)->name
            ],
            [
                'id' =>"2-3-4",
                'name' => "All Departments"
            ]
        ];
        $data = [
            'title' => 'Customer Ledger  Report',
            'subtitle' => 'View Customer  Ledger Report By Date Range and customer',
            'filters' => [
                'custom_dropdown_id' => 2,
                'label_name' => 'Departments',
                'items' => $items,
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'items' => $items ,
                'label_name' => 'Departments',
                'filters' => [
                    'between.invoice_date' => monthlyDateRange(),
                    'custom_dropdown_id' => 2,
                ]
            ]
        ];

        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['items'] = $items;
            $data['filters']['label_name'] = 'Departments';
            $data['filters']['custom_dropdown_id'] = $data['filters']['custom_dropdown_id'];
            $data['filters']['filters']['between.invoice_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['custom_dropdown_id'] = $data['filters']['custom_dropdown_id'];
            $data['filters']['filters']['items'] = $items;
            $data['filters']['filters']['label_name'] = 'Departments';
        }

        $department = explode("-",  $data['filters']['filters']['custom_dropdown_id']);

        $reports =  Invoice::with(['customer'])->select(
            'customer_id',
            DB::raw('count(customer_id) as invoice_count'),
            DB::raw('SUM(total_amount_paid) as total_invoice_amount')
        )
            ->whereIn('in_department', Department::whereIn("id", $department)->pluck('name')->toArray())
            ->whereBetween('invoice_date', $data['filters']['filters']['between.invoice_date'])
            ->where(function($qq){
                $qq->orWhere("invoices.status_id",status("Paid"))
                    ->orWhere('invoices.status_id',status("Complete"));
            })
            ->orderBy('total_invoice_amount','DESC')
            ->groupBy('invoices.customer_id')->get();

        $data['reports'] = $reports;
        return view('reports.customer.customer_ranking_report', $data);
    }

}
