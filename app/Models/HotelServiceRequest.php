<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelServiceRequest extends Model
{
    protected $table = 'sr_hotel_booking';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
}
