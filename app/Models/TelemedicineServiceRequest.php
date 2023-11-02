<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelemedicineServiceRequest extends Model
{
    protected $table = 'sr_telemedicine';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
}
