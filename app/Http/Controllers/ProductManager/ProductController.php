<?php

namespace App\Http\Controllers\ProductManager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Stock;
use Illuminate\Http\Request;

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


    public function expired()
    {

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
            return $item->id == 1 ||  $item->id == 2 || $item->id == 3 || $item->id == 5;
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

}
