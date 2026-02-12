<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelHasRole
 * 
 * @property int|null $role_id
 * @property string|null $model_type
 * @property int|null $model_id
 *
 * @package App\Models
 */
class ModelHasRole extends Model
{
	protected $table = 'model_has_roles';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'role_id' => 'int',
		'model_id' => 'int'
	];

	protected $fillable = [
		'role_id',
		'model_type',
		'model_id'
	];
}
