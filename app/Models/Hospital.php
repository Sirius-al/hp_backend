<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    protected $table = 'hospital';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	
	protected $fillable = ['hospitalName','hospitalAddress','email_for_a_t','email_for_other','departments','image','url','contact','status'];

	public static function getHospitalName($id)
	{
		$info = Hospital::find($id);
		if(!empty($info)){
			$hospitalName = $info->hospitalName;
		}else{
			$hospitalName = "";
		}
		return $hospitalName;
	}
	
}
