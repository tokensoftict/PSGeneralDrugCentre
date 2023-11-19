<?php

namespace App\Http\Controllers\Ajax;

use App\Classes\Settings;
use App\Http\Controllers\Controller;
use App\Models\Invoiceitem;
use App\Repositories\CustomerRepository;
use App\Repositories\ProductionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\PurchaseOrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AjaxController extends Controller
{


    protected ProductRepository $productRepository;
    protected CustomerRepository $customerRepository;

    public function __construct(
                                ProductRepository $productRepository,
                                CustomerRepository $customerRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
    }

    public function findStockByBarcode(Request $request)
    {
        return $this->productRepository->findProductByBarcode($request->get('barcode'));
    }

    public function findstock(Request $request)
    {
        return  $this->productRepository->findProduct($request->get('query') ?? $request->get("searchTerm"));

    }

    public function findpurchasestock(Request $request)
    {
        return  $this->productRepository->findPurchaseProduct($request->get('query') ?? $request->get("searchTerm"));
    }


    public function findcustomer(Request $request)
    {
      return  $this->customerRepository->findCustomer($request->get('query') ?? $request->get("searchTerm"));
    }


    public function andlossprofitdatatablebydepartment(Request $request)
    {
        $invoiceItems = InvoiceItem::select('stock_id',
            DB::raw( 'SUM(quantity) as total_qty'),
            DB::raw( 'SUM(quantity * (selling_price - discount_amount)) as total_selling_total'),
            DB::raw( 'SUM(quantity * (cost_price)) as total_cost_total')
        )->whereHas('invoice',function($q) use(&$request){
            $q->whereBetween('invoice_date',[$request->get('from'),$request->get('to')])
                ->whereIn("status_id",[2,4,6]);
        })->with(['stock','stock.category'])
            ->groupBy('stock_id');

        return Datatables::of($invoiceItems)
            ->addIndexColumn()
            ->addColumn('product_name',function ($item){
                return ucwords($item->stock->name);
            })
            ->addColumn('category',function ($item){
                return ($item->stock->category->name ?? "Un-categorized");
            })
            ->addColumn('department',function ($item) use($request){
                return Settings::$department[$request->get('department')];
            })
            ->addColumn('selling_price',function($item) use (&$request){
                $average_selling_price = (abs($item->total_selling_total / $item->total_qty));
                return money($average_selling_price);
            })
            ->addColumn('cost_price',function($item) use (&$request){
                $average_cost_price = (abs($item->total_cost_total / $item->total_qty));
                return money($average_cost_price);
            })
            ->addColumn('tt_selling_price',function($item) use (&$request){
                $average_selling_price = (abs($item->total_selling_total / $item->total_qty)) * $item->total_qty;
                return money($average_selling_price);
            })
            ->addColumn('tt_cost_price',function($item) use (&$request){
                $average_cost_price = (abs($item->total_cost_total / $item->total_qty)) * $item->total_qty;
                return money($average_cost_price);
            })
            ->addColumn('profit',function($item) use (&$request){
                $profit = ((abs($item->total_selling_total / $item->total_qty)) - (abs($item->total_cost_total / $item->total_qty))) * $item->total_qty;
                return  money($profit);
            })
            ->escapeColumns(null)
            ->make(true);
    }

    public function andlossprofitdatatablebydepartment(Request $request)
    {
        $invoiceItems = InvoiceItem::select('stock_id',
            DB::raw( 'SUM(quantity) as total_qty'),
            DB::raw( 'SUM(quantity * (selling_price - discount_amount)) as total_selling_total'),
            DB::raw( 'SUM(quantity * (cost_price)) as total_cost_total')
        )->whereHas('invoice',function($q) use(&$request){
            $q->whereBetween('invoice_date',[$request->get('from'),$request->get('to')])
                ->where('in_department', $request->get('department'))
                ->whereIn("status_id",[2,4,6]);
        })->with(['stock','stock.category'])
            ->groupBy('stock_id');

        return Datatables::of($invoiceItems)
            ->addIndexColumn()
            ->addColumn('product_name',function ($item){
                return ucwords($item->stock->name);
            })
            ->addColumn('category',function ($item){
                return ($item->stock->category->name ?? "Un-categorized");
            })
            ->addColumn('department',function ($item) use($request){
                return Settings::$department[$request->get('department')];
            })
            ->addColumn('selling_price',function($item) use (&$request){
                $average_selling_price = (abs($item->total_selling_total / $item->total_qty));
                return money($average_selling_price);
            })
            ->addColumn('cost_price',function($item) use (&$request){
                $average_cost_price = (abs($item->total_cost_total / $item->total_qty));
                return money($average_cost_price);
            })
            ->addColumn('tt_selling_price',function($item) use (&$request){
                $average_selling_price = (abs($item->total_selling_total / $item->total_qty)) * $item->total_qty;
                return money($average_selling_price);
            })
            ->addColumn('tt_cost_price',function($item) use (&$request){
                $average_cost_price = (abs($item->total_cost_total / $item->total_qty)) * $item->total_qty;
                return money($average_cost_price);
            })
            ->addColumn('profit',function($item) use (&$request){
                $profit = ((abs($item->total_selling_total / $item->total_qty)) - (abs($item->total_cost_total / $item->total_qty))) * $item->total_qty;
                return  money($profit);
            })
            ->escapeColumns(null)
            ->make(true);
    }
}
