<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customerrequest extends Model
{
    protected $table = 'customerrequest';
	
	const CREATED_AT = 'date_added';
	const UPDATED_AT = 'date_updated';
	
}
