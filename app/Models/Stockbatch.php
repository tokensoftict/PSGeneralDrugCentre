<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ModelFilterTraits;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Stockbatch
 *
 * @property int $id
 * @property Carbon|null $received_date
 * @property Carbon|null $expiry_date
 * @property int $wholesales
 * @property int $bulksales
 * @property int $retail
 * @property int $quantity
 * @property float|null $cost_price
 * @property float|null $retail_cost_price
 * @property int|null $stock_id
 * @property int|null $supplier_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Stock|null $stock
 * @property Supplier|null $supplier
 *
 * @package App\Models
 */
class Stockbatch extends Model
{

    use ModelFilterTraits;

	protected $table = 'stockbatches';

	protected $casts = [
		'received_date' => 'datetime',
		'expiry_date' => 'datetime',
		'wholesales' => 'int',
		'bulksales' => 'int',
		'retail' => 'int',
		'quantity' => 'int',
		'cost_price' => 'float',
		'retail_cost_price' => 'float',
		'stock_id' => 'int',
		'supplier_id' => 'int'
	];

	protected $fillable = [
		'received_date',
		'expiry_date',
		'wholesales',
		'bulksales',
		'retail',
		'quantity',
		'cost_price',
		'retail_cost_price',
		'stock_id',
		'supplier_id'
	];

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function supplier()
	{
		return $this->belongsTo(Supplier::class);
	}
}
