<?php

namespace App\Http\Controllers\ProductManager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductController extends Controller
{

    public function index()
    {
        $data = [];

        $filter = ['status' => 1];

        $data['filters'] = $filter;

        return setPageContent('product.index', $data);
    }


    public function available()
    {
        $data = [];

        $filter = ['status' => 1];

        $data['filters'] = $filter;

        return setPageContent('product.available', $data);
    }


    public function otherinfo()
    {
        $data = [];

        $filter = ['status' => 1];

        $data['filters'] = $filter;

        return setPageContent('product.otherinfo', $data);
    }


    public function create()
    {
        $data = [
            'product'=> new Stock(),
            'title'=>'New',
            'subtitle'=>'Create new'
        ];

        return setPageContent('product.form',$data);
    }


    public function store(Request $request)
    {

    }

    public function show(Stock $stock)
    {
        $data = [
            'product'=> $stock,
        ];

        return setPageContent('product.show',$data);
    }

    public function edit(Stock $stock)
    {
        $data = [
            'product'=> $stock,
            'title'=>'Update',
            'subtitle'=>'Update'
        ];

        return setPageContent('product.form',$data);
    }


    public function toggle(Stock $stock)
    {

    }

    public function disabled()
    {
        $data = [];

        $filter = ['status' => 0];

        $data['filters'] = $filter;

        return setPageContent('product.index', $data);
    }

    public function non_reorder()
    {
        $data = [];

        $filter = ['reorder' => 0];

        $data['filters'] = $filter;

        return view('product.index', $data);
    }


    public function expired()
    {
        $data = [];

        return view('product.expired', $data);
    }

    public function changeSellingPrice()
    {

    }

    public function changeCostPrice()
    {

    }


    public function balance_stock(Request $request) {

        $data = [];

        $data['departments'] = departments(true)->filter(function($item){
            return in_array($item->id, [1,2,3,4]);
        });
        $data['selectedDepartment'] = "";
        $data['stock_id'] = "";
        if($request->get('stock_id')){
            $data['stock_id'] = $request->get('stock_id');
            $data['stock'] = Stock::with(['activeBatches','minimumBatches'])->find($request->get('stock_id'));
            $data['selectedDepartment'] = $request->get('department_id');
        }

        return view('product.stockbatch', $data);
    }



    public function stock_balance_by_supplier(Request $request)
    {
        $data = [
            'title' => 'Stock Balance By Supplier',
            'subtitle' => 'View All Stock Balance by Supplier',
            'filters' => [
                'supplier_id' => 1,
                'filters' => [
                    'supplier_id' => 1
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['supplier_id'] = $data['filters']['supplier_id'];

        }
        return view('product.balance_by_supplier', $data);
    }



    public function near_expired()
    {
        $data = [];

        return view('product.nearexpired', $data);
    }


    public function batched_stock_list(Request $request)
    {
        $data = [
            'title' => 'Batched Stock List',
            'subtitle' => 'View Batched Stock List',
            'filters' => [
                'department'=> 'quantity',
                'filters' => [
                    'department'=> 'quantity',
                ]
            ]
        ];
        if($request->get('filter'))
        {

            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['department'] = $data['filters']['department'];
        }

        return view('product.batched_stock_list', $data);
    }

}
