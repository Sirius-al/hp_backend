<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisaRequest extends Model
{
    protected $table = 'sr_visa_request';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	
}
