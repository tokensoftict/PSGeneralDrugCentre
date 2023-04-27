<?php

namespace App\Http\Controllers\InvoiceReport;

use App\Http\Controllers\Controller;
use App\Models\Production;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class InvoiceReportController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            'title' => 'Invoice Report By Date',
            'subtitle' => 'View Invoice Report By Date Range',
            'filters' => [
                'from' =>todaysDate(),
                'to'=>todaysDate(),
                'filters' => [
                    'between.invoice_date' => [todaysDate(),todaysDate()],
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.invoice_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
        }
        return setPageContent('reports.invoice.index', $data);
    }

    public function by_customer(Request $request)
    {
        $data = [
            'title' => 'Invoice Report By Customer',
            'subtitle' => 'View Invoice Report By Customer',
            'filters' => [
                'from' =>todaysDate(),
                'to'=>todaysDate(),
                'customer_id' => 1,
                'filters' => [
                    'between.invoice_date' =>[todaysDate(),todaysDate()],
                    'customer_id' => 1
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.invoice_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['customer_id'] = $data['filters']['customer_id'];
        }
        return setPageContent('reports.invoice.index', $data);
    }


    public function by_system_user(Request $request)
    {
        $data = [
            'title' => 'Invoice Report By User',
            'subtitle' => 'View Invoice Report By System User',
            'filters' => [
                'from' =>todaysDate(),
                'to'=>todaysDate(),
                'created_by' => 1,
                'filters' => [
                    'between.invoice_date' => [todaysDate(),todaysDate()],
                    'created_by' => 1
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.invoice_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['created_by'] = $data['filters']['created_by'];
        }
        return setPageContent('reports.invoice.index', $data);
    }


    public function by_product(Request $request)
    {
        $data = [
            'title' => 'Invoice Report By Product',
            'subtitle' => 'View Report By Date Range and Product',
            'filters' => [
                'stock' => Stock::find(1),
                'from' =>todaysDate(),
                'to'=>todaysDate(),
                'stock_id' => 1,
                'filters' => [
                    'between.invoices.invoice_date' =>[todaysDate(),todaysDate()],
                    'stock_id' => 1,
                ]
            ]
        ];
        if($request->get('filter'))
        {

            $data['filters'] = $request->get('filter');
            $data['filters']['stock'] = Stock::find($data['filters']['stock_id']);
            $data['filters']['filters']['between.invoices.invoice_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['stock_id'] = $data['filters']['stock_id'];


        }
        return setPageContent('reports.invoice.invoiceitem', $data);
    }


    public function by_status(Request $request)
    {
        $data = [
            'title' => 'Invoice Report By Status',
            'subtitle' => 'View Report By Date Range and System Status',
            'filters' => [
                'from' =>todaysDate(),
                'to'=>todaysDate(),
                'status_id' => 1,
                'filters' => [
                    'between.invoice_date' => [todaysDate(),todaysDate()],
                    'status_id' => 1
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['between.invoice_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['status_id'] = $data['filters']['status_id'];

        }
        return setPageContent('reports.invoice.index', $data);
    }




}
