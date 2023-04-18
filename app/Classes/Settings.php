<?php

namespace App\Classes;

use Spatie\Valuestore\Valuestore;

class Settings extends Valuestore
{


    public static array $department = [
        'quantity' => 'Main Store',
        'wholesales' => 'Whole Sales Department',
        'bulksales' => 'Bulk Sales Department',
        'retail' => 'Retail Department'
    ];

    public static  $validation = [
        'store.name' => 'required|max:255',
        'store.first_address'=>'required',
        'store.contact_number'=>'required',
    ];

    public static $pagination = 10;

    public function store(){
        return json_decode(json_encode($this->all()));
    }

    public static array $reports = [10,11,12,13,14,15,16,17,18];


}
