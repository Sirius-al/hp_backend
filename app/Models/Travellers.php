<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Travellers extends Model
{
    protected $table = 'travellers';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	
}
