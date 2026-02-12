<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelHasPermission
 * 
 * @property string|null $permission_id
 * @property string|null $model_type
 * @property string|null $model_id
 *
 * @package App\Models
 */
class ModelHasPermission extends Model
{
	protected $table = 'model_has_permissions';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'permission_id',
		'model_type',
		'model_id'
	];
}
