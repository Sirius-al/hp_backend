<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patientmeta extends Model
{
    protected $table = 'patient_meta';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
}
