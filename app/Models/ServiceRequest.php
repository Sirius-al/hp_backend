<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ServiceRequest extends Model
{
    protected $table = 'service_request';
	
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	public static function getServiceWiseData($id,$serviceRequestID)
	{
		if($id == 'DA')
			{
				$data = DB::table('sr_doctor_apmt')
						->leftJoin('service_request', 'service_request.id', '=', 'sr_doctor_apmt.service_request_id')
						->select('sr_doctor_apmt.*','service_request.*')
						->selectRaw('(SELECT GROUP_CONCAT(`files`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_doctor_apmt`.`service_request_id`) as files')
						->selectRaw('(SELECT `hospitalName` FROM `hospital` WHERE `hospital`.`id`= `service_request`.`hospital_id`) as hospitalName')
						->selectRaw('(SELECT `name` FROM `doctors` WHERE `doctors`.`id`= `service_request`.`doctor_id`) as doctorName')
						->where(['sr_doctor_apmt.service_request_id' => $serviceRequestID])
						->get();
			}
		else if($id == 'SM')
			{
				$data = DB::table('sr_smo')
						->leftJoin('service_request', 'service_request.id', '=', 'sr_smo.service_request_id')
						->select('sr_smo.*','service_request.*')
						->selectRaw('(SELECT GROUP_CONCAT(`files`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_smo`.`service_request_id`) as files')
						->selectRaw('(SELECT `hospitalName` FROM `hospital` WHERE `hospital`.`id`= `sr_smo`.`hospital`) as hospitalName')
						->selectRaw('(SELECT `specialization` FROM `departments` WHERE `departments`.`id`= `sr_smo`.`speciality`) as specality')
						->selectRaw('(SELECT `name` FROM `doctors` WHERE `doctors`.`id`= `service_request`.`doctor_id`) as doctorName')
						->where(['sr_smo.service_request_id' => $serviceRequestID])
						->get();
			}
		else if($id == 'TM')
			{
				$data = DB::table('sr_telemedicine')
						->leftJoin('service_request', 'service_request.id', '=', 'sr_telemedicine.service_request_id')
						->select('sr_telemedicine.*','service_request.*')
						->selectRaw('(SELECT GROUP_CONCAT(`files`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_telemedicine`.`service_request_id`) as files')
						->selectRaw('(SELECT `hospitalName` FROM `hospital` WHERE `hospital`.`id`= `sr_telemedicine`.`hospital`) as hospitalName')
						->selectRaw('(SELECT `specialization` FROM `departments` WHERE `departments`.`id`= `sr_telemedicine`.`speciality`) as specality')
						->selectRaw('(SELECT `name` FROM `doctors` WHERE `doctors`.`id`= `service_request`.`doctor_id`) as doctorName')
						->where(['sr_telemedicine.service_request_id' => $serviceRequestID])
						->get();
			}
		else if($id == 'AP')
			{
				$data = DB::table('sr_pickup')
						->leftJoin('service_request', 'service_request.id', '=', 'sr_pickup.service_request_id')
						->select('sr_pickup.*','service_request.*')
						->selectRaw('(SELECT GROUP_CONCAT(`files`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_pickup`.`service_request_id`) as files')
						->selectRaw('(SELECT `hospitalName` FROM `hospital` WHERE `hospital`.`id`= `service_request`.`hospital_id`) as hospitalName')
						->selectRaw('(SELECT `firstName` FROM `patients` WHERE `patients`.`id`= `service_request`.`patient_id`) as patientName')
						->where(['sr_pickup.service_request_id' => $serviceRequestID])
						->get();
			}
		else if($id == 'HB')
			{
				$data = DB::table('sr_hotel_booking')
						->leftJoin('service_request', 'service_request.id', '=', 'sr_hotel_booking.service_request_id')
						->select('sr_hotel_booking.*','service_request.*')
						->selectRaw('(SELECT GROUP_CONCAT(`files`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_hotel_booking`.`service_request_id`) as files')
						->selectRaw('(SELECT `hospitalName` FROM `hospital` WHERE `hospital`.`id`= `service_request`.`hospital_id`) as hospitalName')
						->where(['sr_hotel_booking.service_request_id' => $serviceRequestID])
						->get();
			}
		else if($id == 'VR')
			{
				$data = DB::table('sr_visa_request')
						->leftJoin('service_request', 'service_request.id', '=', 'sr_visa_request.service_request_id')
						->select('sr_visa_request.*','service_request.*')
						->selectRaw('(SELECT GROUP_CONCAT(`files`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_visa_request`.`service_request_id`) as files')
						->selectRaw('(SELECT `hospitalName` FROM `hospital` WHERE `hospital`.`id`= `service_request`.`hospital_id`) as hospitalName')
						->selectRaw('(SELECT `name` FROM `doctors` WHERE `doctors`.`id`= `service_request`.`doctor_id`) as doctorName')
						->where(['sr_visa_request.service_request_id' => $serviceRequestID])
						->get();
			}
		return $data;
	}
}
