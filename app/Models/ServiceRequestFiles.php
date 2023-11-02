<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestFiles extends Model
{
    protected $table = 'service_request_files';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
}
