<?php

namespace App\Http\Controllers\CustomerManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(){
        $data = [
            'filters' => []
        ];
        return setPageContent('customermanager.index', $data);
    }

    public function retails(){
        $data = [
            'filters' => ['retail_customer' => 1]
        ];
        return setPageContent('customermanager.index', $data);
    }


    public function create(){
    }


    public function update(Request $request, $id){
    }


}
