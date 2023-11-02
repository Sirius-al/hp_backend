<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorApmtServiceRequest extends Model
{
    protected $table = 'sr_doctor_apmt';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
}
