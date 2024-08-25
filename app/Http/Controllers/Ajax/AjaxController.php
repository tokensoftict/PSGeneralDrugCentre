<?php

namespace App\Http\Controllers\Ajax;

use App\Classes\Settings;
use App\Http\Controllers\Controller;
use App\Models\Invoiceitem;
use App\Models\Stockopening;
use App\Repositories\CustomerRepository;
use App\Repositories\ProductionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\PurchaseOrderRepository;
use Carbon\Carbon;
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


    public function profitandlossdatatable(Request $request)
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

    public function profitandlossdatatablebydepartment(Request $request)
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




    public function supplierdboverview(Request $request)
    {
        $costPriceColumn = $request->get('department') === "retail" ? "average_retail_cost_price" : "average_cost_price";
        $quantityColumn = $request->get('department') === "retail" ? "stockopenings.retail" : "stockopenings.wholesales+stockopenings.bulksales+stockopenings.quantity";


        $supplierDBOverview = Stockopening::query()
            ->select(
                DB::raw("SUM(stockopenings.$costPriceColumn * ($quantityColumn)) as total_opening_cost_price"),
                DB::raw("SUM($quantityColumn) as total_opening_quantity"),
                //DB::raw("SUM(supplier_credit_payment_histories.amount) as total_supplier_outstanding"),
               // DB::raw("MAX(purchases.date_completed) as last_supplier_date"),
               // DB::raw( "suppliers.name as supplier_name"),
               //DB::raw("suppliers.id as supplier_id")
            )
            //->join("suppliers", "stockopenings.supplier_id", "=", "suppliers.id")
           // ->join("supplier_credit_payment_histories", "supplier_credit_payment_histories.supplier_id", "=", "stockopenings.supplier_id")
            //->join("purchases", "stockopenings.supplier_id", "=", "purchases.supplier_id")
            ->where("stockopenings.date_added", $request->get('payment_date'))
            ->groupBy("stockopenings.supplier_id");


        return Datatables::of($supplierDBOverview)
            ->addIndexColumn()
            ->editColumn('total_opening_cost_price',function ($item){
                return money($item->total_opening_cost_price);
            })
            ->editColumn('total_supplier_outstanding',function ($item){
                return money($item->total_supplier_outstanding);
            })
            ->editColumn('last_supplier_date',function ($item){
                return (new Carbon($item->last_supplier_date))->format("d/m/Y");
            })
            ->escapeColumns(null)
            ->make(true);

    }

}
