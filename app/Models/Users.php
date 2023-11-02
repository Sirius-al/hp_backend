<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	
}
