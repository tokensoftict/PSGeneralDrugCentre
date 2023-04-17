<?php
namespace App\Traits;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use PDF;

trait InvoiceTrait
{

    public function print_pos(Invoice $invoice)
    {
        logActivity($invoice->id, $invoice->invoice_number,'Print Invoice Thermal Status:'.$invoice->status->name);

        $data['invoice'] =$invoice;
        $data['store'] = $this->settings->store();
        $page_size = $invoice->invoiceitems->count() * 15;
        if($invoice->onlineordertotals()->exists()){
            $page_size +=  ($invoice->onlineordertotals->count() -1) * 15;
        }
        $page_size += 180;
        $pdf = PDF::loadView('print.pos', $data,[],[
            'format' => [70,$page_size],
            'margin_left'          => 0,
            'margin_right'         => 0,
            'margin_top'           => 0,
            'margin_bottom'        => 0,
            'margin_header'        => 0,
            'margin_footer'        => 0,
            'orientation'          => 'P',
            'display_mode'         => 'fullpage',
            'custom_font_dir'      => '',
            'custom_font_data' 	   => [],
            'default_font_size'    => '12',
        ]);
        $pdf->getMpdf()->SetWatermarkText(strtoupper(status_name($invoice->status_id)));
        $pdf->getMpdf()->showWatermarkText = true;

        if($invoice->in_department !="retail" && $invoice->status !="COMPLETE") {
            if($invoice->online_order_status == "1"){
                $departments = [
                    "bulksales",
                    "quantity",
                    "wholesales",
                ];
                foreach($departments as $dept){
                    $data['department'] = $dept;
                    if($invoice->invoiceitembatches->where('department',$dept)->count() > 0) {
                        $pdf->getMpdf()->AddPage('P', '', '', '', '', 0, 0, 0, 0, 0, 0);
                        $pdf->getMpdf()->WriteHTML(view('print.pos_picker_online', $data));
                    }
                }
            }else {
                $pdf->getMpdf()->AddPage('P', '', '', '', '', 0, 0, 0, 0, 0, 0);
                $pdf->getMpdf()->WriteHTML(view('print.pos_picker', $data));
            }

        }
        if($invoice->in_department =="retail" && Auth::user()->department =="retail"){
            $pdf->getMpdf()->AddPage('P', '', '', '', '', 0, 0, 0, 0, 0, 0);
            $pdf->getMpdf()->WriteHTML(view('print.pos', $data));
        }
        logActivity($invoice->id, $invoice->invoice_number,"Print POS Invoice Status : ".$invoice->status);

        return $pdf->stream('document.pdf');
    }

    public function view(Invoice $invoice)
    {
        //$invoice->load(['customer','user','payment','payment.paymentmethoditems','create_by','last_updated','picked','checked','packed','user','invoiceactivitylogs','invoiceitembatches','invoiceitems','onlineordertotals']);

        $data = [];

        $data['invoice'] = $invoice;

        logActivity($invoice->id, $invoice->invoice_number,'Invoice viewed :'.status_name($invoice->status_id));

        return setPageContent('invoiceandsales.show', $data);
    }

    public function edit(Invoice $invoice)
    {
        $data = [];

        $data['title'] = 'Edit ';
        $data['subtitle'] = 'Update Already Generated ';
        $data['invoice'] = $invoice;
        $data['department'] =department_by_quantity_column( $invoice->in_department)->id;
        logActivity($invoice->id, $invoice->invoice_number,'Invoice edit/update page was viewed :'.status_name($invoice->status_id));

        return setPageContent('invoiceandsales.form', $data);
    }


    public function requestForDiscount()
    {

    }

}
