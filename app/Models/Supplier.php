<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ModelFilterTraits;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Supplier
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $address
 * @property string|null $email
 * @property string|null $phonenumber
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Supplier extends Model
{
    use  ModelFilterTraits;

	protected $table = 'suppliers';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'name',
		'address',
		'email',
		'phonenumber',
		'status'
	];
}
