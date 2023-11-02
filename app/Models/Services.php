<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $table = 'services';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
}
