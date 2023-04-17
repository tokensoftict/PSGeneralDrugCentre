<?php

namespace App\Jobs;

use App\Models\Stockbincard;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddLogToProductBinCard //implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $bincards;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $bincards)
    {
        $this->bincards = $bincards;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->bincards = collect($this->bincards)->map(function($item){
            $item['created_at'] = Carbon::now()->toDateTimeLocalString();
            $item['updated_at'] = Carbon::now()->toDateTimeLocalString();
            return $item;
        })->toArray();  // ad created and updated filed to column
//dd($this->bincards);
        \DB::table('stockbincards') ->insert($this->bincards);
      /*
        \DB::transaction(function(){

        });
      */

    }
}
