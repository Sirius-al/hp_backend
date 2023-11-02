<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	
	public static function getRoleName($id){
		$info = self::find($id);
		return $info->roleName;
	}
}
