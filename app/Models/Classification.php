<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Jobs\PushDataServer;
use App\Traits\ModelFilterTraits;
use Carbon\Carbon;
use Google\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Classification
 *
 * @property int $id
 * @property string|null $name
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection[] $stocks
 *
 * @package App\Models
 */
class Classification extends Model
{

    use  ModelFilterTraits;

	protected $table = 'classifications';

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

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function newonlinePush()
    {
        dispatch(new PushDataServer(['action'=>'new','table'=>'classifications','data'=>$this->getBulkPushData()]));
    }

    public function updateonlinePush()
    {
        dispatch(new PushDataServer(['action'=>'update','table'=>'classifications','data'=>$this->getBulkPushData()]));
    }


}
