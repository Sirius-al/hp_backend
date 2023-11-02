<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'country';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	
}
