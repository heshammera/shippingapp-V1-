<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PermissionRole
 * 
 * @property string|null $id
 * @property string|null $role_id
 * @property string|null $permission_id
 *
 * @package App\Models
 */
class PermissionRole extends Model
{
	protected $table = 'permission_role';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'id',
		'role_id',
		'permission_id'
	];
}
