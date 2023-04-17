<?php

namespace App\Repositories;

use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class ProductRepository
{

    public function __construct()
    {

    }

    public static array $productFields = [
        'name' => NULL,
        'description'=> NULL,
        'code'=> NULL,
        'category_id'=> NULL,
        'manufacturer_id'=> NULL,
        'classification_id'=> NULL,
        'stockgroup_id'=> NULL,
        'brand_id'=> NULL,
        'whole_price'=> NULL,
        'bulk_price'=> NULL,
        'retail_price'=> NULL,
        'barcode'=> NULL,
        'location'=> NULL,
        'expiry'=> '1',
        'reorder' => 1,
        'piece'=> NULL,
        'box'=> NULL,
        'carton'=> NULL,
        'sachet'=> '0',
        'status'=> '1',
    ];


    public function create($data) : Stock{

        return Stock::create($data);
    }


    public function getStock($id) : Stock {
        return Stock::find($id);
    }


    public function update($id, $data) : Stock{

        $stock  = $this->getStock($id);

        $stock->update($data);

        return $stock;
    }


    public function destroy($id) : void
    {
        $this->getStock($id)->delete();
    }

    public function findProduct(mixed $name)
    {
        if(empty($name) || \Str::length($name) < 3) return collect([])->toJson();

        $name = explode(" ",$name);

        $selling_price = match (request()->column){
            'wholesales', 'bulksales', 'quantity', '', NULL => 'whole_price',
            'retail' => 'retail_price',
        };

        $cost_price = match (request()->column){
            'wholesales', 'bulksales', 'quantity', '', NULL => 'cost_price',
            'retail' => 'retail_cost_price',
        };
//->where(request()->column ,'>',0)
        return DB::table('stocks')->select('id', request()->column.' as quantity', $cost_price." as cost_price", $selling_price." as selling_price",'name', 'box', 'location','name as text', 'carton')->where(function($query) use(&$name){
            foreach ($name as $char) {
                $query->where('name', 'LIKE', "%$char%");
            }
        })->get()->toJson();

    }

    public function findPurchaseProduct(mixed $name)
    {
        if(empty($name) || \Str::length($name) < 3) return collect([])->toJson();

        $name = explode(" ",$name);

        $selling_price = match (request()->column){
            'wholesales', 'bulk-sales', 'quantity', '', NULL => 'whole_price',
            'retail' => 'retail_price',
        };

        $cost_price = match (request()->column){
            'wholesales', 'bulk-sales', 'quantity', '', NULL => 'cost_price',
            'retail' => 'retail_cost_price',
        };

        return DB::table('stocks')->select('id', $cost_price, $selling_price, 'name', 'box', 'location', 'name as text')->where(function($query) use(&$name){
            foreach ($name as $char) {
                $query->where('name', 'LIKE', "%$char%");
            }
        })->get()->toJson();

    }


}
