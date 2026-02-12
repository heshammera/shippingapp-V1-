<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShippingRate
 * 
 * @property int $id
 * @property string|null $governorate
 * @property float $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class ShippingRate extends Model
{
	protected $table = 'shipping_rates';

	protected $casts = [
		'price' => 'float'
	];

	protected $fillable = [
		'governorate',
		'price'
	];
}
