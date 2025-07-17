<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductCustomPrice
 * 
 * @property int $id
 * @property int $stock_id
 * @property int $user_id
 * @property float $price
 * @property int $min_qty
 * @property int $max_qty
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Stock $stock
 * @property User $user
 *
 * @package App\Models
 */
class ProductCustomPrice extends Model
{
	protected $table = 'product_custom_prices';

	protected $casts = [
		'stock_id' => 'int',
		'user_id' => 'int',
		'price' => 'float',
		'min_qty' => 'int',
		'max_qty' => 'int'
	];

	protected $fillable = [
		'stock_id',
		'user_id',
		'price',
		'min_qty',
		'max_qty'
	];

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
