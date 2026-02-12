<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductPrice
 * 
 * @property int $id
 * @property int $product_id
 * @property int $min_qty
 * @property float $price_per_unit
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Product $product
 *
 * @package App\Models
 */
class ProductPrice extends Model
{
	protected $table = 'product_prices';

	protected $casts = [
		'product_id' => 'int',
		'min_qty' => 'int',
		'price' => 'float',

	];

	protected $fillable = [
		'product_id',
		'min_qty',
		'price_per_unit'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}
}
