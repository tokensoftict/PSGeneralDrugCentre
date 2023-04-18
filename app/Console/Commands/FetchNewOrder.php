<?php

namespace App\Console\Commands;

use App\Http\Livewire\InvoiceAndSales\InvoiceFormComponent;
use App\Jobs\AddLogToProductBinCard;
use App\Jobs\PushStockUpdateToServerFromDeletedFetchInvoice;
use App\Models\Invoice;
use App\Models\Onlineordertotal;
use App\Models\Stock;
use App\Repositories\InvoiceRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class FetchNewOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:refresh';

    public static array $onlineWholeSalesDepartment = [
        "quantity",
        "bulksales",
        "wholesales",
    ];

    public static array $onlineRetailSalesDepartment = ['retail'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching New Order from Website';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $baseurl = onlineBase();
        $errors = [];
        Auth::loginUsingId(1);
        $url = $baseurl."api/data/unprocessedorder";

        $contents = _FETCH($url);

        $this->info('Checking for new Order from '.onlineBase());

        $order =  $contents;

        if(is_array($contents)){

            if(count($order) == 0){
                $this->info('No Pending order to process');
                return Command::SUCCESS;
            }

            $invoice = Invoice::with(['invoiceitembatches','invoiceitembatches.stock','invoiceitems', 'customer', 'payment', 'customer', 'user'])->where('invoice_number',$order['invoice_no'])->first();

            if($invoice){

                $this->warn('Already exist in local database '.$order['invoice_no']);

                if( $invoice->payment_id && $invoice->payment()->exists()){
                    $invoice->payment->delete();
                }

                $returnBatches = [];
                $columns = [];
                $cards = [];
                 $invoice->invoiceitembatches->map(function($item) use(&$returnBatches, &$columns, &$cards){
                    $columns[] = $item->department;
                    $returnBatches[] = [
                        'id' => $item->stockbatch_id,
                        $item->department=> ($item->stock->{ $item->department} + $item->quantity)
                    ];
                     $cards[] = [
                        'bin_card_type'=>"APP//RETURN",
                        'bin_card_date'=>todaysDate(),
                        'user_id'=>auth()->id(),
                        'stock_id' => $item->stock_id,
                        'return_qty'=>$item->quantity,
                        'stockbatch_id'=> $item->stockbatch_id,
                        'to_department'=>$item->department,
                        'comment'=>"Stock Returned from Online Invoice Deleted Because of repacking or re-process : by ".Auth::user()->name,
                        'balance'=>$item->stock->totalBalance(),
                        'department_balance'=>$item->stock->{$item->department}
                    ];
                });

                Stock::returnStocks($invoice, $returnBatches, array_unique($columns)); //return the stock batches


                dispatch(new PushStockUpdateToServerFromDeletedFetchInvoice(array_column($cards, 'product_id')));

                dispatch(new AddLogToProductBinCard($cards));

                dispatch(new PushStockUpdateToServerFromDeletedFetchInvoice(
                    array_column($invoice->invoiceitems->toArray(), 'stock_id')
                ));

                $invoice->delete();

                $this->info("Invoice items has been returned and invoice has been deleted successfully");

                return Command::SUCCESS;
            }

        }

        //lets process the invoice
        $invoiceRepo = new InvoiceRepository();


        //now lets try to process the order, let get all department we can pick stock from
        //before we start, the default place to pick the stock from the bulk department
        //so lets start
        $orderproducts = $order['order_products']; // lets get the order product
        //validating local database stock level to see if it can process this order
        $products = [];

        array_walk( $orderproducts, function($item) use(&$products){
            $products[$item['localid']]['item'] = $item;
        });

        $stocks = Stock::with(['activeBatches'])->whereIn('id',array_keys($products))->get();

        $stocks->each(function($stock, $key) use(&$products){

            $products[$stock->id]['stock'] = $stock;
        });

        foreach ($products as $key=>$product)
        {
            $batches = $product['stock']->pingStockLocation($product['item']['quantity'],  $product['stock']->activeBatches, ($order['store'] == "WHOLESALES" ? self::$onlineWholeSalesDepartment : self::$onlineRetailSalesDepartment));

            if($batches === false) {

                $errors[] = "Not enough quantity to process this stock on local database ".$product['item']['name'];
                $this->error("Not enough quantity to process this stock on local database ".$product['item']['name']);
            }else {
                $products[$key]['batches'] = $batches;
            }

        }

        //all have been validated and store successfully, now we can store the order products inside the array

        //lets check if there is an error
        if(count($errors) > 0){

            _POST('order_validation_error/'.$order['id'], ['errors'=>$errors]);
            return Command::SUCCESS;
        }


        //after validating the order we can remove stock finalize processing

        //oya lets do normal local sales computation first

        $customer = $order['user']['cus_exist'] ?? 1;

        $invoiceData = InvoiceRepository::invoice(new Invoice(), new InvoiceFormComponent());

        $total_cost = 0;

        $total_sub = 0;

        $invoiceData['invoiceitems'] = collect($products)->map(function($item, $key) use($customer, &$total_cost, &$total_sub){

            $cost =  abs((collect($item['batches'])->sum('cost_price')) / count($item['batches']));

            $total_cost += ($cost * $item['item']['quantity']);
            $total_sub  +=($cost *  $item['item']['price']);

            return [
                'stock_id' => $key,
                'quantity' => $item['item']['quantity'],
                'customer_id' => $customer,
                'added_by' => auth()->user()->id,
                'discount_added_by' => null,
                'cost_price' => abs((collect($item['batches'])->sum('cost_price')) / count($item['batches'])),
                'selling_price' => $item['item']['price'],
                'profit' => $item['item']['price'] - $item['stock']->cost_price,
                'discount_value' => 0,
                'discount_amount' => 0,
                'discount_type' => 'Fixed',
                'batches' => $item['batches']
            ] ;
        })->toArray();

        $invoiceData['customer_id'] = $customer;

        $invoiceData['invoice_number'] = $order['invoice_no'];

        $invoiceData['department'] =  $order['store'] == "WHOLESALES" ? 'bulksales' : "retail";

        $invoiceData['in_department'] =$order['store'] == "WHOLESALES" ? 'bulksales' : "retail";

        $invoiceData['online_order_status'] = 1;

        $invoiceData['onliner_order_id']= $order['id'];

        $invoiceData["sub_total"] = $order['total'];

        $invoiceData['total_cost'] = $total_cost;

        $invoiceData['total_profit'] = $total_sub - $total_cost;

        $invoiceRepo->createOnlineInvoice($invoiceData, collect($order['order_total_orders']));

        _GET('processorder/'.$order['id']."/2");

        //now check if the payment method is a payment gateway
        $this->info('Order ID '.$order['id'].' has been processed successfully!.');

        return Command::SUCCESS;
    }
}
