<?php

namespace App\Http\Controllers\PurchaseReport;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\SupplierCreditPaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PurchaseReportsController extends Controller
{

    public function index(Request $request)
    {
        $data = [
            'title' => 'Purchase Report By Date',
            'subtitle' => 'View Report By Date Range',
            'filters' => [
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'filters' => [
                    'between.date_created' => monthlyDateRange()
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.date_created'] = Arr::only(array_values( $request->get('filter')), [0,1]);
        }
        return setPageContent('reports.purchases.index', $data);
    }

    public function by_supplier(Request $request)
    {
        $data = [
            'title' => 'Purchase Report By Date and Supplier',
            'subtitle' => 'View Report By Date Range and Supplier',
            'filters' => [
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'supplier_id' => 1,
                'filters' => [
                    'between.date_created' => monthlyDateRange(),
                    'supplier_id' => 1
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.date_created'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['supplier_id'] = $data['filters']['supplier_id'];

        }
        return setPageContent('reports.purchases.index', $data);
    }

    public function by_system_user(Request $request)
    {
        $data = [
            'title' => 'Purchase Report By System User',
            'subtitle' => 'View Report By Date Range and System User',
            'filters' => [
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'user_id' => 1,
                'filters' => [
                    'between.date_created' => monthlyDateRange(),
                    'user_id' => 1
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.date_created'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['user_id'] = $data['filters']['user_id'];

        }
        return setPageContent('reports.purchases.index', $data);
    }

    public function by_stock(Request $request)
    {
        $data = [
            'title' => 'Purchase Report By Date and Stock',
            'subtitle' => 'View Report By Date Range and Stock',
            'filters' => [
                'stock' => Stock::find(1),
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'stock_id' => 1,
                'filters' => [
                    'between.purchases.date_created' => monthlyDateRange(),
                    'stock_id' => 1
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['stock'] = Stock::find($data['filters']['stock_id']);
            $data['filters']['filters']['between.purchases.date_created'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['stock_id'] = $data['filters']['stock_id'];

        }
        return view('reports.purchases.purchaseorderbymaterial', $data);
    }

    public function by_status(Request $request)
    {
        $data = [
            'title' => 'Purchase Report By Date',
            'subtitle' => 'View Report By Date Range and System Status',
            'filters' => [
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'status_id' => 1,
                'filters' => [
                    'between.date_created' => monthlyDateRange(),
                    'status_id' => 1
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.date_created'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['status_id'] = $data['filters']['status_id'];

        }
        return setPageContent('reports.purchases.index', $data);
    }

    public function by_department(Request $request)
    {
        $data = [
            'title' => 'Purchase Report By Date',
            'subtitle' => 'View Report By Date Range and Department',
            'filters' => [
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'department' => 'quantity',
                'filters' => [
                    'between.date_created' => monthlyDateRange(),
                    'department' => 'quantity'
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.date_created'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['department'] = $data['filters']['department'];

        }
        return setPageContent('reports.purchases.index', $data);
    }



    public function supplier_payment(Request $request)
    {
        $data = [
            'title' => 'Supplier Payment Report By Date',
            'subtitle' => 'View Supplier Payment By Date Range',
            'filters' => [
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'filters' => [
                    'between.payment_date' => monthlyDateRange(),
                    'type' => 'PAYMENT'
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.payment_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['type'] = 'PAYMENT';
        }
        return view('reports.purchases.supplier.payment.index', $data);
    }


    public function supplier_credit(Request $request)
    {
        $data = [
            'title' => 'Supplier Payment Report By Date',
            'subtitle' => 'View Supplier Payment By Date Range',
            'filters' => [
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'filters' => [
                    'between.payment_date' => monthlyDateRange(),
                    'type' => 'CREDIT'
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.payment_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['type'] = 'CREDIT';
        }
        return view('reports.purchases.supplier.credit.index', $data);
    }



    public function balance_sheet(Request $request)
    {
        $data = [
            'title' => 'Supplier Balance Sheet Report',
            'subtitle' => 'View Supplier Balance Sheet Report By Date Range and supplier',
            'filters' => [
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'supplier_id' => 1,
                'filters' => [
                    'between.return_date' => monthlyDateRange(),
                    'supplier_id' => 1
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.return_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['supplier_id'] = $data['filters']['supplier_id'];
        }

        $data['opening'] = SupplierCreditPaymentHistory::where('supplier_id',  $data['filters']['filters']['supplier_id'])->where('payment_date','<',  $data['filters']['filters']['between.return_date'][0])->sum('amount');

        $data['histories'] = SupplierCreditPaymentHistory::where('supplier_id', $data['filters']['filters']['supplier_id'])->whereBetween('payment_date',  $data['filters']['filters']['between.return_date'])->get();


        return view('reports.purchases.supplier.balance_sheet', $data);

    }

}
