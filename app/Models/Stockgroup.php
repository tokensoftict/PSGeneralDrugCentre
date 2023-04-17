<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Jobs\PushDataServer;
use App\Traits\ModelFilterTraits;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Stockgroup
 *
 * @property int $id
 * @property string|null $name
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Stockgroup extends Model
{

    use  ModelFilterTraits;

	protected $table = 'stockgroups';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'name',
		'status'
	];



    public function getBulkPushData() : array{
        return [
            'id'=>$this->id,
            'name'=> $this->name,
            'status'=>$this->status
        ];
    }


    public function newonlinePush()
    {
        dispatch(new PushDataServer(['action'=>'new','table'=>'stock_groups','data'=>$this->getBulkPushData()]));
    }

    public function updateonlinePush()
    {
        dispatch(new PushDataServer(['action'=>'update','table'=>'stock_groups','data'=>$this->getBulkPushData()]));
    }
}
