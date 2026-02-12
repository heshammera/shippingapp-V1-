<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ImportedShipment
 * 
 * @property int $id
 * @property string $hash
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class ImportedShipment extends Model
{
	protected $table = 'imported_shipments';

	protected $fillable = [
		'hash'
	];
}
