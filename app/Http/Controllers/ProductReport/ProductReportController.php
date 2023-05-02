<?php

namespace App\Http\Controllers\ProductReport;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductReportController extends Controller
{

    public function bincard_report(Request $request)
    {
        $data = [
            'title' => 'Product Bincard Report',
            'subtitle' => 'View Product Movement from different department',
            'filters' => [
                'stock' => Stock::find(1),
                'department'=> 'quantity',
                'from' =>monthlyDateRange()[0],
                'to'=>monthlyDateRange()[1],
                'stock_id' => 1,
                'filters' => [
                    'between.stockbincards.bin_card_date' => monthlyDateRange(),
                    'stock_id' => 1,
                    'department'=> 'quantity',
                ]
            ]
        ];
        if($request->get('filter'))
        {

            $data['filters'] = $request->get('filter');
            $data['filters']['stock'] = Stock::find($data['filters']['stock_id']);
            $data['filters']['filters']['between.stockbincards.bin_card_date'] = Arr::only(array_values( $request->get('filter')), [0,1]);
            $data['filters']['filters']['stock_id'] = $data['filters']['stock_id'];
            $data['filters']['filters']['department'] = $data['filters']['department'];

        }
        return view('reports.product.bincard', $data);
    }


    public function nearoutofstock()
    {
        $data = [
            'title' => 'Product Near OS Report',
            'subtitle' => 'View Product that is almost out of stock and require purchasing',
        ];

        return view('reports.product.nearos', $data);
    }


}
