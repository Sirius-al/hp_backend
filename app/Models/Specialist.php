<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    protected $table = 'specialist';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
}
