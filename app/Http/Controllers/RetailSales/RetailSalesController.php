<?php

namespace App\Http\Controllers\RetailSales;

use App\Classes\Settings;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Traits\InvoiceTrait;
use Illuminate\Http\Request;

class RetailSalesController extends Controller
{

    use InvoiceTrait;

    public Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function create()
    {
        $data = [];

        $data['title'] = 'New Retail ';
        $data['subtitle'] = 'New Retail ';
        $data['invoice'] = new Invoice();
        $data['department'] = 4;
        return view('invoiceandsales.form', $data);
    }

    public function sales()
    {
        $data = [];

        $data['title'] = "Today's Completed Invoice(s)";
        $data['subtitle'] = 'List of Completed Invoice - '.todaysDate();
        $data['filters'] = ['in_department'=>'retail' ,'status_id'=>status('Complete'), 'invoice_date'=>todaysDate()];

        return setPageContent('invoiceandsales.index', $data);
    }

    public function draft()
    {
        $data = [];

        $data['title'] = "Today's Draft Invoice(s)";
        $data['subtitle'] = 'List of Completed Invoice - '.todaysDate();
        $data['filters'] = ['in_department'=>'retail' ,'status_id'=>status('Draft'), 'invoice_date'=>todaysDate()];

        return setPageContent('invoiceandsales.index', $data);
    }


}
