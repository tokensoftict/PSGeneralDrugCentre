<?php

namespace App\Http\Controllers\InvoiceAndSales;

use App\Classes\Settings;
use App\Http\Controllers\Controller;
use App\Jobs\PushStockUpdateToServer;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use App\Traits\InvoiceTrait;
use Illuminate\Http\Request;
use PDF;

class InvoiceController extends Controller
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

        $data['title'] = 'New ';
        $data['subtitle'] = 'Generate New ';
        $data['invoice'] = new Invoice();
        $data['department'] = auth()->user()->department_id;
        return setPageContent('invoiceandsales.form', $data);
    }

    public function draft(Request $request)
    {
        $data = [
            'filters' => [
                'invoice_date' =>dailyDate(),
                'filters' => [
                    'invoice_date' => todaysDate()
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['invoice_date'] = $request->get('filter')['invoice_date'];
        }

        $data['title'] = 'Draft Invoice(s)';
        $data['subtitle'] = 'List of Unpaid Invoice - '.$data['filters']['filters']['invoice_date'];

        if(auth()->user()->department_id !== 5) {
            $dpt = department_by_id(auth()->user()->department_id)->quantity_column;
            $data['filters']['filters']['in_department'] = $dpt;
        }

        $data['filters']['filters']['status_id'] = status('Draft');

        return setPageContent('invoiceandsales.index', $data);
    }

    public function discount(Request $request)
    {
        $data = [
            'filters' => [
                'invoice_date' =>dailyDate(),
                'filters' => [
                    'invoice_date' => todaysDate()
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['invoice_date'] = $request->get('filter')['invoice_date'];
        }

        $data['title'] = 'Discount Invoice(s)';
        $data['subtitle'] = 'List of Waiting for Discount Invoice - '.$data['filters']['filters']['invoice_date'];

        if(auth()->user()->department_id !== 5) {

            $dpt = department_by_id(auth()->user()->department_id)->quantity_column;
            $data['filters']['filters']['in_department'] = $dpt;

        }

        $data['filters']['filters']['status_id'] = status('Discount');

        return setPageContent('invoiceandsales.index', $data);
    }


    public function paid(Request $request)
    {
        $data = [
            'filters' => [
                'invoice_date' =>dailyDate(),
                'filters' => [
                    'invoice_date' => todaysDate()
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['invoice_date'] = $request->get('filter')['invoice_date'];
        }

        $data['title'] = 'Paid Invoice(s)';
        $data['subtitle'] = 'List of Paid Invoice - '.$data['filters']['filters']['invoice_date'];

        if(auth()->user()->department_id !== 5) {
            $dpt = department_by_id(auth()->user()->department_id)->quantity_column;
            $data['filters']['filters']['in_department'] = $dpt;
        }

        $data['filters']['filters']['status_id'] = status('Paid');

        return setPageContent('invoiceandsales.index', $data);
    }


    public function dispatched(Request $request)
    {
        $data = [
            'filters' => [
                'invoice_date' =>dailyDate(),
                'filters' => [
                    'invoice_date' => todaysDate()
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['invoice_date'] = $request->get('filter')['invoice_date'];
        }

        $data['title'] = 'Dispatched Invoice(s)';
        $data['subtitle'] = 'List of Completely Dispatched Invoice - '.$data['filters']['filters']['invoice_date'];

        if(auth()->user()->department_id !== 5) {
            $dpt = department_by_id(auth()->user()->department_id)->quantity_column;
            $data['filters']['filters']['in_department'] = $dpt;
        }

        $data['filters']['filters']['status_id'] = status('Complete');

        return setPageContent('invoiceandsales.index', $data);
    }

    public function deleted(Request $request)
    {
        $data = [
            'filters' => [
                'invoice_date' =>dailyDate(),
                'filters' => [
                    'invoice_date' => todaysDate()
                ]
            ]
        ];
        if($request->get('filter'))
        {
            $data['filters'] = $request->get('filter');
            $data['filters']['filters']['invoice_date'] = $request->get('filter')['invoice_date'];
        }

        $data['title'] = 'Deleted Invoice(s)';
        $data['subtitle'] = 'List of  Deleted Invoice - '. $data['filters']['filters']['invoice_date'];

        if(auth()->user()->department_id !== 5) {
            $dpt = department_by_id(auth()->user()->department_id)->quantity_column;
            $data['filters']['filters']['in_department'] = $dpt;
        }

        $data['filters']['filters']['status_id'] = status('Deleted');

        return setPageContent('invoiceandsales.index', $data);
    }



    public function return(Invoice $invoice)
    {
        $data = [];

        $data['title'] = 'Return ';
        $data['subtitle'] = 'Return Already Generated ';
        $data['invoice'] = $invoice;
        $data['department'] = department_by_quantity_column($invoice->in_department)->id;
        logActivity($invoice->id, $invoice->invoice_number,'Invoice return page was viewed :'.status_name($invoice->status_id));

        return setPageContent('invoiceandsales.form', $data);
    }


    public function print_way_bill(Invoice $invoice)
    {
        logActivity($invoice->id, $invoice->invoice_number,'Print Invoice Way Bill Status:'.status_name($invoice->status_id));

        $data['invoice'] = $invoice;
        $data['store'] = $this->settings->store();
        $pdf = PDF::loadView("print.pos_afour_waybill",$data);
        $pdf->getMpdf()->SetWatermarkText(strtoupper($invoice->status->name));
        $pdf->getMpdf()->showWatermarkText = true;
        return $pdf->stream('documentwaybill.pdf');
    }

    public function print_afour(Invoice $invoice)
    {
        logActivity($invoice->id, $invoice->invoice_number,'Print Invoice A4 Status:'.status_name($invoice->status_id));

        $data['invoice'] = $invoice;
        $data['store'] = $this->settings->store();
        $pdf = PDF::loadView("print.pos_afour",$data);
        $pdf->getMpdf()->SetWatermarkText(strtoupper($invoice->status->name));
        $pdf->getMpdf()->showWatermarkText = true;

        return $pdf->stream('documenta4.pdf');
    }

    public function applyInvoiceDiscount(Invoice $invoice)
    {

    }

    public function applyProductDiscount(Invoice $invoice)
    {
        $data = [];

        $data['title'] = 'Product Discount';
        $data['subtitle'] = 'Apply Product Discount ';
        $data['invoice'] = $invoice;

        logActivity($invoice->id, $invoice->invoice_number,'Apply invoice discount was page was viewed :'.$invoice->status->name);

        return setPageContent('invoiceandsales.applyproductdiscount', $data);
    }

    public function destroy(Invoice $invoice)
    {

        InvoiceRepository::returnStock($invoice);

        $invoice->status_id = status('Deleted');

        $invoice->update();

        LogActivity($invoice->id, $invoice->invoice_number,"Invoice payment was deleted ".$invoice->status);

        if($invoice->online_order_status == "1"){

            LogActivity($invoice->id, $invoice->invoice_number,"Online invoice, update sent to server ".$invoice->status);

            _GET('processorder/'.$invoice->onliner_order_id."/5");

        }

        dispatch(new PushStockUpdateToServer(array_column($invoice->invoiceitems->toArray(), 'stock_id')));

        return redirect()->route('invoiceandsales.draft');
    }

    public function update(Invoice $invoice)
    {

    }

    public function dispatchInvoice(Invoice $invoice)
    {

    }


    public function editInvoiceDate()
    {

    }


}
