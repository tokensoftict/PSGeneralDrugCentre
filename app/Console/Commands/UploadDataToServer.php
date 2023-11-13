<?php

namespace App\Console\Commands;


use App\Models\Brand;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Customer;
use App\Models\Manufacturer;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\Stockgroup;

use Illuminate\Console\Command;

class UploadDataToServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'online:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload All Required Data From Local Database to Online Database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);
        //start with normal data first before we move to stock to avoid relationship problem
        //not we will stock upload immediately we encounter an error, no worries any we push data to online bulk
        // the tables are truncated before insertion and also on the server we have turn off foreign key check
        //to avoid relation error

        //now we begin with manufacture
        /*
              $this->info('Gathering Manufacturer Data');
              $manufacturers = Manufacturer::all();
              $all_data = [];
              foreach($manufacturers as $manufacturer){
                  $all_data[] = $manufacturer->getBulkPushData();
              }
              $this->info('Gathering Manufacturer Data Complete');
              $this->info('Parsing Manufacturer Data');
              $postdata = ['table'=>'manufacturers','data'=> $all_data];
              $this->info('Parsing Manufacturer Data Complete');
              $this->info('Posting Manufacturer Data to '.onlineBase());
              $response = _POST('bulk_add_data',$postdata);
              if($response['status'] == true){
                  $this->info('Manufacturer data has been posted successfully');
                  sleep(4);
              }


              //now we begin with category data
              $this->info('Gathering Category Data');
              $categories = Category::all();
              $all_data = [];
              foreach($categories as $category){
                  $all_data[] = $category->getBulkPushData();
              }
              $this->info('Gathering Category Data Complete');
              $this->info('Parsing Category Data');
              $postdata = ['table'=>'product_category','data'=> $all_data];
              $this->info('Parsing Category Data Complete');
              $this->info('Posting Category Data to '.onlineBase());
              $response = _POST('bulk_add_data',$postdata);
              if($response['status'] == true){
                  $this->info('Category data has been posted successfully');
                  sleep(4);
              }

              //now we begin with classification data

              $this->info('Gathering Classification Data');
              $classifications = Classification::all();
              $all_data = [];
              foreach($classifications as $classification){
                  $all_data[] = $classification->getBulkPushData();
              }
              $this->info('Gathering Classification Data Complete');
              $this->info('Parsing Classification Data');
              $postdata = ['table'=>'classifications','data'=> $all_data];
              $this->info('Parsing Classification Data Complete');
              $this->info('Posting Classification Data to '.onlineBase());
              $response = _POST('bulk_add_data',$postdata);
              if($response['status'] == true){
                  $this->info('Classification data has been posted successfully');
                  sleep(4);
              }


              //now we begin with group data

              $this->info('Gathering Stock Group Data');
              $stockgroups = StockGroup::all();
              $all_data = [];
              foreach($stockgroups as $stockgroup){
                  $all_data[] = $stockgroup->getBulkPushData();
              }
              $this->info('Gathering Stock Group Data Complete');
              $this->info('Parsing Stock Group Data');
              $postdata = ['table'=>'stock_groups','data'=> $all_data];
              $this->info('Parsing Stock Group Data Complete');
              $this->info('Posting Stock Group Data to '.onlineBase());
              $response = _POST('bulk_add_data',$postdata);
              if($response['status'] == true){
                  $this->info('Stock Group data has been posted successfully');
                  sleep(4);
              }



              //now we begin with brand data
              $this->info('Gathering Brand Data');
              $brands = Brand::all();
              $all_data = [];
              foreach($brands as $brand){
                  $all_data[] = $brand->getBulkPushData();
              }
              $this->info('Gathering Brand Data Complete');
              $this->info('Parsing Brand Data');
              $postdata = ['table'=>'brands','data'=> $all_data];
              $this->info('Parsing Brand Data Complete');
              $this->info('Posting Brand Data to '.onlineBase());
              $response = _POST('bulk_add_data',$postdata);
              if($response['status'] == true){
                  $this->info('Brand data has been posted successfully');
                  sleep(4);
              }


              //now finally lets hanle stock pushing
                $this->info('Gathering Bulk Stock Data');
              $stocks = Stock::where(function($query){
                  $query->orWhere('bulk_price','>',0)->orWhere('retail_price','>',0);
              })->where('status',1);
              $chunk_numbers = round(($stocks->count() / 500));
              $stocks->chunk(500,function($stocks) use (&$chunk_numbers){
                  $all_data = [];
                  foreach($stocks as $stock){
                      $all_data[] = $stock->getBulkPushData();
                  }
                  $this->info('Gathering Stock Data Complete');
                  $this->info('Parsing Stock Data');
                  $postdata = ['table'=>'stock','data'=> $all_data];
                  $this->info('Parsing Stock Data Complete');
                  $this->info('Posting Stock Data to '.onlineBase());
                  $response = _POST('bulk_add_data',$postdata);
                  $chunk_numbers = $chunk_numbers-1;
                  $this->info(json_encode($response));
                  $this->info('Stock data has been posted successfully for chunk '.$chunk_numbers);
                  sleep(3);
              });


  */

              //now we begin with Customer date
              $this->info('Gathering Customer Data');
              $customers = Customer::where('status',1)->get();
              $all_data = [];
              foreach($customers as $customer){
                  $all_data[] = $customer->getBulkPushData();
              }
              $this->info('Gathering Customer Data Complete');
              $this->info('Parsing Customer Data');
              $postdata = ['table'=>'existing_customer','data'=> $all_data];
              $this->info('Parsing Customer Data Complete');
              $this->info('Posting Customer Data to '.onlineBase());
              $response = _POST('bulk_add_data',$postdata);

              if($response['status'] == true){
                  $this->info('Customer data has been posted successfully');
                  sleep(4);
              }




              //push recent PoItem so the website can display new arrivals
              $this->info('Gathering Recent Purchase Orders for Main Store');
              $poitems = Purchase::with([
                  'purchaseitems'=>function($q){
                      $q->where(function($query){
                          $query->where('for_department','quantity')->where('status','completed');
                      });
                  }
                  ,'stock'=>function($q){
                     $q->where('bulk_price','>',0);
                  }
              ])->orderBy('id','DESC')->limit(50)->get();
              $all_data = [];
              foreach($poitems as $poitem){
                  $all_data[] = $poitem->getBulkPushData();
              }
              $this->info('Gathering Recent Purchase Orders for Main Store Complete');
              $this->info('Parsing Purchase Orders');
              $postdata = ['table'=>'new_arrivals','data'=> $all_data];
              $this->info('Parsing Purchase Orders Data Complete');
              $this->info('Posting Purchase Orders to '.onlineBase());
              $response = _POST('bulk_add_data',$postdata);
              if($response['status'] == true){
                  $this->info('Purchase Orders data has been posted successfully');
                  sleep(4);
              }

        $this->info('Data has been uploaded to server successfully');

        return ;
    }







}
