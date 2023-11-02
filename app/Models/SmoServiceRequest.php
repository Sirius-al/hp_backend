<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmoServiceRequest extends Model
{
    protected $table = 'sr_smo';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
}
