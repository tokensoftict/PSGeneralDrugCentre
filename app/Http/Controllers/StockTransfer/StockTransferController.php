<?php

namespace App\Http\Controllers\StockTransfer;

use App\Http\Controllers\Controller;
use App\Models\Stocktransfer;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{


    public function index()
    {
        $data = [
            'title' => "List Draft Stock Transfer List",
            'subtitle' => "Drafted Stock Transfer List",
            'filters' => ['status_id' => status('Draft'), 'transfer_date'=>todaysDate()]
        ];

        return setPageContent('stocktransfer.index',$data);
    }

    public function approved()
    {
        $data = [
            'title' => "List Approved Stock Transfer List",
            'subtitle' => "Approved Stock Transfer List",
            'filters' => ['status_id' => status('Approved'), 'transfer_date'=>todaysDate()]
        ];

        return setPageContent('stocktransfer.index',$data);
    }

    public function create(Request $request)
    {
        $data = [
            'title' => "New Transfer List",
            'subtitle' => "Create new Transfer List",
            'stocktransfer' => new Stocktransfer(),
            'departments' => departments()->filter(function($item){
                return $item->quantity_column !== NULL;
            }),
            'from' => NULL,
            'to' => NULL
        ];

        if(isset($request->from) && isset($request->to))
        {
            $data['from'] = $request->from;
            $data['to'] = $request->to;
        }



        return setPageContent('stocktransfer.form',$data);
    }


    public function show(Stocktransfer $stocktransfer)
    {
        $data = [
            'title' => "Show Transfer Details",
            'subtitle' => "Show Product Transfer List",
            'stocktransfer' => $stocktransfer
        ];

        return setPageContent('stocktransfer.show',$data);
    }


    public function edit(Stocktransfer $stocktransfer)
    {
        $data = [
            'title' => "Edit Transfer List",
            'subtitle' => "Edit and Update Stock Transfer",
            'stocktransfer' => $stocktransfer,
            'departments' => departments()->filter(function($item){
                return $item->quantity_column !== NULL;
            }),
            'from' => $stocktransfer->from,
            'to' => $stocktransfer->to
        ];
        return setPageContent('stocktransfer.form',$data);
    }

    public function destroy(Stocktransfer $stocktransfer)
    {

    }

    public function complete(Stocktransfer $stocktransfer)
    {

    }

}
