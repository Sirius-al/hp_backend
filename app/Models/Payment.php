<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'tbl_payment';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	
}
