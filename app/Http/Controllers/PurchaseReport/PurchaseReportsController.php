<?php

namespace App\Http\Controllers\PurchaseReport;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Supplier;
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



}
