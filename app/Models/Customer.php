<?php
namespace App\Models;
use App\Jobs\PushDataServer;

/**
 * Created by Reliese Model.
 */



use App\Traits\ModelFilterTraits;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Customer
 *
 * @property int $id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $email
 * @property bool|null $status
 * @property string|null $address
 * @property string|null $phone_number
 * @property bool $retail_customer
 * @property int|null $city_id
 * @property float $credit_balance
 * @property float $deposit_balance
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property City|null $city
 *
 * @package App\Models
 */
class Customer extends Model
{

    use ModelFilterTraits;

	protected $table = 'customers';

	protected $casts = [
		'status' => 'bool',
		'retail_customer' => 'bool',
		'city_id' => 'int',
		'credit_balance' => 'float',
		'deposit_balance' => 'float'
	];

	protected $fillable = [
		'firstname',
		'lastname',
		'email',
		'status',
		'address',
		'phone_number',
		'retail_customer',
		'city_id',
		'credit_balance',
		'deposit_balance'
	];

    public function updateCreditBalance()
    {
        $this->credit_balance =  $this->creditpaymentlogs()->sum('amount');
        $this->update();
    }


    public function creditpaymentlogs()
    {
        return $this->hasMany(Creditpaymentlog::class);
    }

    public function customer_ledgers()
    {
        return $this->hasMany(CustomerLedger::class);
    }

    public function city()
	{
		return $this->belongsTo(City::class);
	}

    public function getBulkPushData() : array{
        return [
            'local_id'=>$this->id,
            'firstname'=> $this->firstname,
            'lastname'=>$this->lastname,
            'email'=>$this->email,
            'address'=>$this->address,
            'phone_number'=>$this->phone_number
        ];
    }

    public function newonlinePush()
    {
        dispatch(new PushDataServer(['action'=>'new','table'=>'existing_customer','data'=>$this->getBulkPushData()]));
    }

    public function updateonlinePush()
    {
        dispatch(new PushDataServer(['action'=>'update','table'=>'existing_customer','data'=>$this->getBulkPushData()]));
    }

}

