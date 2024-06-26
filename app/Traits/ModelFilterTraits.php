<?php
namespace App\Traits;

use App\Models\Pricechangehistory;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait ModelFilterTraits
{

    public function scopefilterdata($query, array $filter)
    {
        foreach ($filter as $key => $value) {
            if(str_contains($key ,'between'))
            {
                $key = str_replace('between.','', $key);

                $query->whereBetween($this->getQueryTable($key, true).$key,$value );
            }else if(str_contains($key, "array.")){
                $key = str_replace('array.','', $key);
                $query->whereIn($this->getQueryTable($key, true).$key, $value);
            }
            else if(str_contains($key, 'is_not_null')){
                $key = str_replace('is_not_null.', '', $key);
                $query->whereNotNull($key);
            }
            else {
                $query->where($this->getQueryTable($key).$key, $value);
            }
        }

        return $query;
    }


    protected function getQueryTable($criterial, $between = false) : string
    {
        if(str_contains($criterial, "."))  return $between === false ? $criterial : "";

        return $this->table.".";
    }


    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::created(function($model){

            $instance = (new self());
            Cache::forget($instance->table);
            if(function_exists($instance->table)){
                $function_name = $instance->table;
                $function_name();
            }

            if(method_exists($model, 'newonlinePush') && config('app.sync_with_online')== 1)
            {
                $model->newonlinePush(); // if the model is syncing with online push any update
            }

        });

        self::deleted(function($model){

            $instance = (new self());
            Cache::forget($instance->table);
            if(function_exists($instance->table)){
                $function_name = $instance->table;
                $function_name();
            }
        });

        self::updated(function($model){

            $instance = (new self());
            Cache::forget($instance->table);
            if(function_exists($instance->table)){
                $function_name = $instance->table;
                $function_name();
            }

            if(method_exists($model, 'updateonlinePush') && config('app.sync_with_online')== 1)
            {
                $model->updateonlinePush();
            }

        });

        self::updating(function($model){

            if(method_exists($model, 'quantityColumnChanges'))
            {
                $model->quantityColumnChanges();
            }

            if(get_class($model) == Stock::class){
                $trackDirtyColumn = ['whole_price' => 'wholesales', 'bulk_price'=>'bulksales', 'retail_price'=>'retail'];
                foreach ($trackDirtyColumn as $column => $department){
                    if($model->isDirty($column)){
                        Pricechangehistory::create([
                            'stock_id' => $model->id,
                            'from' => $model->getOriginal($column),
                            'to' => $model->{$column},
                            'change_date' => todaysDate(),
                            'change_time' => Carbon::now(),
                            'user_id' => auth()->id(),
                            'department' => $department,
                        ]);
                    }
                }
            }
        });


        if (!defined('STDIN') && \auth()->check() && self::class === Stock::class) {
            static::addGlobalScope('checkForPromo', function (Builder $builder){
                $builder->with(['promotion_item']);
            });
        }


    }



}
