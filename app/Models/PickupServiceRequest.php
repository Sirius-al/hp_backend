<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupServiceRequest extends Model
{
    protected $table = 'sr_pickup';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
}
