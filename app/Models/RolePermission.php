<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RolePermission
 * 
 * @property string|null $id
 * @property string|null $role_id
 * @property string|null $permission_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @package App\Models
 */
class RolePermission extends Model
{
	protected $table = 'role_permission';
	public $incrementing = false;

	protected $fillable = [
		'id',
		'role_id',
		'permission_id'
	];
}
