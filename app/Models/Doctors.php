<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctors extends Model
{
    protected $table = 'doctors';
	
	protected $fillable = ['name','hospital_id','phone','email','speciality_id','designation','shortDescription','areaInterested','status'];
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	
}
