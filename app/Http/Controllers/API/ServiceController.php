<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\Patients;
use App\Models\ServiceRequestFiles;
use App\Models\ServiceRequestComments;
use App\Models\SmoServiceRequest;
use App\Models\DoctorApmtServiceRequest;
use App\Models\PickupServiceRequest;
use App\Models\HotelServiceRequest;
use App\Models\TelemedicineServiceRequest;
use App\Models\VisaRequest;
use App\Models\Travellers;
use App\Models\Users;
use App\Models\Specialist;
use App\Models\Hospital;
use DB; use Auth; use Mail;
use Illuminate\Pagination\LengthAwarePaginator;
use File;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Jobs\SendEmail;

class ServiceController extends BaseController
{
    public function __construct()
    {
       // $this->middleware('auth');
    }


    // public function index()
    // {
	//    $specialistList =  Specialist::all();
	//    return view('specialist.index',compact('specialistList'));
    // }
	
	// public function serviceList()
    // {
	//    $servicelist =  Services::all();
	//    return view('specialist.servicelist',compact('servicelist'));
	// }
	
	public function requestConfirm(Request $request){
	    if($request->has('service_id')){
	        if($request->service_id == 1){ // Doctor Appointment DA
	            
	            $service_id = $request->service_id;
	            $doctor_id  = $request->doctor_id;
	            $speciality = $request->speciality;
	            $booked_date_time = $request->booked_date_time;
	            $additional_info_hospital = $request->additional_info_hospital;
	            
	            DB::table('sr_doctor_apmt')->where('service_request_id', $request->service_request_id)->update(array('booked_date_time' => date('Y-m-d H:i:s', strtotime($booked_date_time)), 'additional_info_hospital'=>$additional_info_hospital, 'doctor'=>$doctor_id,'department'=>$speciality));
	           
	           DB::table('service_request')->where('id', $request->service_request_id)->update(array('doctor_id'=>$doctor_id,'reply'=>1));
	           
	            $req_detail = $success['request_info'] = app('App\Http\Controllers\API\AjaxController')->getServiceWiseData($request->service_id, $request->service_request_id);
	            
				$files=array();
				if($files=$request->hasFile('files')){
					$files = $request->file('files');
					foreach($files as $file){
						$ServiceRequestFilesModel = new ServiceRequestFiles();
						$name=$file->getClientOriginalName();
						$destinationPath = public_path('services/doctorsAppointment');
						$fileName = 'DA-'.$request->service_request_id.'-'.time().'_'.$name; 
						$file->move($destinationPath,$fileName);
						$ServiceRequestFilesModel->service_request_id = $request->service_request_id;
						$ServiceRequestFilesModel->files = 'services/doctorsAppointment/'.$fileName;
						$ServiceRequestFilesModel->save();
					}
				}
				
				$patientInformation = Patients::find($req_detail[0]->patient_id);
				$subject = 'DA-'.$request->service_request_id.' DOCTOR APPOINTMENT REQUEST FOR '.$patientInformation->firstName.' '.$patientInformation->lastName.'[ C-0000'.$patientInformation->id.' ]';
				$success['REQ_ID'] =  'DA-'.$request->service_request_id;
				$data['ID'] = $request->service_request_id;
				$data['TYPE'] = 'DA';
				$data['patientInformation'] = $patientInformation;
				$data['subject'] = $subject;
				$data['hospitalinfo'] = Users::find($req_detail[0]->agent_id)->email;
				SendEmail::dispatch($data);
	           
	           $msg = 'Doctor booking Request Confirmed.';
	            
	        }
	        
	        if($request->service_id == 2){ // SMO SM
	            
	            $service_id = $request->service_id;
	            $doctor_id  = $request->doctor;
	            $speciality = $request->speciality;
	            $costQuot = $request->costQuot;
	            $duration_of_treatment = $request->duration_of_treatment;
	            $consultents_feedback = $request->consultents_feedback;
	            $generalDirectives = $request->generalDirectives;
	            
	            DB::table('sr_smo')->where('service_request_id', $request->service_request_id)->update(array('costQuot' => $costQuot, 'duration_of_treatment'=>$duration_of_treatment, 'doctor'=>$doctor_id,'speciality'=>$speciality,'consultents_feedback'=>$consultents_feedback,'generalDirectives'=>$generalDirectives));
	           
	           DB::table('service_request')->where('id', $request->service_request_id)->update(array('doctor_id'=>$doctor_id,'reply'=>1));
	           
	            $req_detail = $success['request_info'] = app('App\Http\Controllers\API\AjaxController')->getServiceWiseData($service_id, $request->service_request_id);
	            
				$files=array();
                if($files=$request->hasFile('files')){
                	$files = $request->file('files');
                	foreach($files as $file){
                		$ServiceRequestFilesModel = new ServiceRequestFiles();
                		$name=$file->getClientOriginalName();
                		$destinationPath = public_path('services/smo');
                		$fileName = 'SM-'.$request->service_request_id.'-'.time().'_'.$name; 
                		$file->move($destinationPath,$fileName);
                		$ServiceRequestFilesModel->service_request_id = $request->service_request_id;
                		$ServiceRequestFilesModel->files = 'services/smo/'.$fileName;
                		$ServiceRequestFilesModel->save();
                	}
                }
				
				$patientInformation = Patients::find($req_detail[0]->patient_id);
				$subject = 'SM-'.$request->service_request_id.' SMO REQUEST FOR '.$patientInformation->firstName.' '.$patientInformation->lastName.'[ C-0000'.$patientInformation->id.' ]';
				$success['REQ_ID'] =  'HB-'.$request->service_request_id;
				$data['ID'] = $request->service_request_id;
				$data['TYPE'] = 'SM';
				$data['patientInformation'] = $patientInformation;
				$data['subject'] = $subject;
				$data['hospitalinfo'] = Users::find($req_detail[0]->agent_id)->email;
				SendEmail::dispatch($data);
	            $msg = 'SMO response Sent Successfully';
	        }
	        
	        if($request->service_id == 3){ // Airport Pickup AP
	        
	            $service_id = $request->service_id;
	            $confirmationDetails = $request->confirmationDetails;
	            
	            DB::table('sr_pickup')->where('service_request_id', $request->service_request_id)->update(array('confirmationDetails' => $confirmationDetails));
	           
	           DB::table('service_request')->where('id', $request->service_request_id)->update(array('reply'=>1));
	           
	          $req_detail = $success['request_info'] = app('App\Http\Controllers\API\AjaxController')->getServiceWiseData($service_id, $request->service_request_id);
	            
				$files=array();
                if($files=$request->hasFile('files')){
                	$files = $request->file('files');
                	foreach($files as $file){
                		$ServiceRequestFilesModel = new ServiceRequestFiles();
                		$name=$file->getClientOriginalName();
                		$destinationPath = public_path('services/airportPickup');
                		$fileName = 'AP-'.$request->service_request_id.'-'.time().'_'.$name; 
                		$file->move($destinationPath,$fileName);
                		$ServiceRequestFilesModel->service_request_id = $request->service_request_id;
                		$ServiceRequestFilesModel->files = 'services/airportPickup/'.$fileName;
                		$ServiceRequestFilesModel->save();
                	}
                }
	           
				$patientInformation = Patients::find($req_detail[0]->patient_id);
				$subject = 'AP-'.$request->service_request_id.' AIRPORT PICKUP REQUEST FOR '.$patientInformation->firstName.' '.$patientInformation->lastName.'[ C-0000'.$patientInformation->id.' ]';
				$success['REQ_ID'] =  'AP-'.$request->service_request_id;
				$data['ID'] = $request->service_request_id;
				$data['TYPE'] = 'AP';
				$data['patientInformation'] = $patientInformation;
				$data['subject'] = $subject;
				$data['hospitalinfo'] = Users::find($req_detail[0]->agent_id)->email;
				SendEmail::dispatch($data);
	            $msg = 'Airport Pickup Confirmed';
	           
	        }
	        
	        if($request->service_id == 4){ // Hotel Booking HB
	            
	            $service_id = $request->service_id;
	            $confirmationDetails = $request->confirmationDetails;
	            
	            DB::table('sr_hotel_booking')->where('service_request_id', $request->service_request_id)->update(array('confirmationDetails' => $confirmationDetails));
	           
	           DB::table('service_request')->where('id', $request->service_request_id)->update(array('reply'=>1));
	           
	            $success['request_info'] = app('App\Http\Controllers\API\AjaxController')->getServiceWiseData($service_id, $request->service_request_id);
	            
				$files=array();
                if($files=$request->hasFile('files')){
                	$files = $request->file('files');
                	foreach($files as $file){
                		$ServiceRequestFilesModel = new ServiceRequestFiles();
                		$name=$file->getClientOriginalName();
                		$destinationPath = public_path('services/hotelBooking');
                		$fileName = 'HB-'.$request->service_request_id.'-'.time().'_'.$name; 
                		$file->move($destinationPath,$fileName);
                		$ServiceRequestFilesModel->service_request_id = $request->service_request_id;
                		$ServiceRequestFilesModel->files = 'services/hotelBooking/'.$fileName;
                		$ServiceRequestFilesModel->save();
                	}
                }
	           
			    $patientInformation = Patients::find($req_detail[0]->patient_id);
				$subject = 'HB-'.$request->service_request_id.' HOTEL BOOKING REQUEST FOR '.$patientInformation->firstName.' '.$patientInformation->lastName.'[ C-0000'.$patientInformation->id.' ]';
				$success['REQ_ID'] =  'HB-'.$request->service_request_id;
				$data['ID'] = $request->service_request_id;
				$data['TYPE'] = 'HB';
				$data['patientInformation'] = $patientInformation;
				$data['subject'] = $subject;
				$data['hospitalinfo'] = Users::find($req_detail[0]->agent_id)->email;
				SendEmail::dispatch($data);
				
	           $msg = 'Hotel Booking Confirmed';
	            
	        }
	        
	        if($request->service_id == 5){ // Visa Request VR
	        
	            $service_id = $request->service_id;
	            $doctor_id  = $request->doctor;
	            $speciality = $request->speciality;
	            $additional_info_hospital = $request->additional_info_hospital;
	            
	            DB::table('sr_visa_request')->where('service_request_id', $request->service_request_id)->update(array('additional_info_hospital' => $additional_info_hospital));
	           
	           DB::table('service_request')->where('id', $request->service_request_id)->update(array('reply'=>1));
	           
	           $req_detail = $success['request_info'] = app('App\Http\Controllers\API\AjaxController')->getServiceWiseData($service_id, $request->service_request_id);
	            
                $files=array();
				if($files=$request->file('files')){
					foreach($files as $file){
						$ServiceRequestFilesModel = new ServiceRequestFiles();
						$name=$file->getClientOriginalName();
						$destinationPath = public_path('services/visaRequest');
						$fileName = 'VR-'.$request->service_request_id.'-'.time().'_'.$name; 
						$file->move($destinationPath,$fileName);
						$ServiceRequestFilesModel->service_request_id = $request->service_request_id;
						$ServiceRequestFilesModel->files = 'services/visaRequest/'.$fileName;
						$ServiceRequestFilesModel->save();
					}
				}
	           
				$patientInformation = Patients::find($req_detail[0]->patient_id);
				$subject = 'VR-'.$request->service_request_id.' VISA REQUEST FOR '.$patientInformation->firstName.' '.$patientInformation->lastName.'[ C-0000'.$patientInformation->id.' ]';
				$success['REQ_ID'] =  'VR-'.$request->service_request_id;
				$data['ID'] = $request->service_request_id;
				$data['TYPE'] = 'VR';
				$data['patientInformation'] = $patientInformation;
				$data['subject'] = $subject;
				$data['hospitalinfo'] = Users::find($req_detail[0]->agent_id)->email;
				SendEmail::dispatch($data);
	            $msg = 'Visa Request Approved Successfully';
	            
	        }
	        
	        if($request->service_id == 6){ // Telemedicine TM
	            
	            $service_id = $request->service_id;
	            $doctor_id  = $request->doctor;
	            $speciality = $request->speciality;
	            $consultents_feedback = $request->consultents_feedback;
	            
	            DB::table('sr_telemedicine')->where('service_request_id', $request->service_request_id)->update(array('doctor'=>$doctor_id,'speciality'=>$speciality,'consultents_feedback'=>$consultents_feedback));
	           
	           DB::table('service_request')->where('id', $request->service_request_id)->update(array('doctor_id'=>$doctor_id,'reply'=>1));
	           
	           $req_detail = $success['request_info'] = app('App\Http\Controllers\API\AjaxController')->getServiceWiseData($service_id, $request->service_request_id);
	            
				$files=array();
                if($files=$request->hasFile('files')){
                	$files = $request->file('files');
                	foreach($files as $file){
                		$ServiceRequestFilesModel = new ServiceRequestFiles();
                		$name=$file->getClientOriginalName();
                		$destinationPath = public_path('services/telemedicine');
                		$fileName = 'TM-'.$request->service_request_id.'-'.time().'_'.$name; 
                		$file->move($destinationPath,$fileName);
                		$ServiceRequestFilesModel->service_request_id = $request->service_request_id;
                		$ServiceRequestFilesModel->files = 'services/telemedicine/'.$fileName;
                		$ServiceRequestFilesModel->save();
                	}
                }
				
				$patientInformation = Patients::find($req_detail[0]->patient_id);
				$subject = 'TM-'.$request->service_request_id.' TELEMEDICINE REQUEST FOR '.$patientInformation->firstName.' '.$patientInformation->lastName.'[ C-0000'.$patientInformation->id.' ]';
				$success['REQ_ID'] =  'TM-'.$request->service_request_id;
				$data['ID'] = $request->service_request_id;
				$data['TYPE'] = 'TM';
				$data['patientInformation'] = $patientInformation;
				$data['subject'] = $subject;
				$data['hospitalinfo'] = Users::find($req_detail[0]->agent_id)->email;
				SendEmail::dispatch($data);
	           
	           $msg = 'Telemedicine response Sent Successfully';
	        }
	        
	        return $this->sendResponse($success, $msg);
	    }
	}
	
	public function servicerequests_of_agent(Request $request)
	{
		if($request->has('agent_id')){
			$serviceRequests= DB::table('service_request')
						->join('patients', 'patients.id', '=', 'service_request.patient_id')
						->join('hospital', 'hospital.id', '=', 'service_request.hospital_id')
						->join('users', 'users.id', '=', 'service_request.agent_id')
						->join('services', 'services.id', '=', 'service_request.service_id')
						->select('service_request.*', 'patients.firstName', 'patients.lastName','patients.email','patients.id as pid','patients.uhID','hospital.hospitalName as hospital_id','users.name as agent_id','services.services as service_name')
						->where('service_request.agent_id', $request->agent_id)
						->orderBy('service_request.id', 'desc')
						->get();
		}elseif($request->has('hospital_id')){
			$serviceRequests= DB::table('service_request')
						->join('patients', 'patients.id', '=', 'service_request.patient_id')
						->join('hospital', 'hospital.id', '=', 'service_request.hospital_id')
						->join('users', 'users.id', '=', 'service_request.agent_id')
						->join('services', 'services.id', '=', 'service_request.service_id')
						->select('service_request.*', 'patients.firstName', 'patients.lastName','patients.email','patients.id as pid','patients.uhID','hospital.hospitalName as hospital_id','users.name as agent_id','services.services as service_name')
						->where('service_request.hospital_id', $request->hospital_id)
						->orderBy('service_request.id', 'desc')
						->get();
		}else{
		    $serviceRequests= DB::table('service_request')
						->join('patients', 'patients.id', '=', 'service_request.patient_id')
						->join('hospital', 'hospital.id', '=', 'service_request.hospital_id')
						->join('users', 'users.id', '=', 'service_request.agent_id')
						->join('services', 'services.id', '=', 'service_request.service_id')
						->select('service_request.*', 'patients.firstName', 'patients.lastName','patients.email','patients.id as pid','patients.uhID','hospital.hospitalName as hospital_id','users.name as agent_id','services.services as service_name')
						->orderBy('service_request.id', 'desc')
						->get();
		}
		$success['data'] = $serviceRequests;
		return $this->sendResponse($success, 'Hotel booking Request Submitted Successfully.');
	}
	
	// public function approveServiceRequest($id)
	// {
	// 	$serviceInfo = ServiceRequest::find($id);
	// 	$serviceInfo->patient = 0;
	// 	$serviceInfo->directReq = 1;
	// 	if($serviceInfo->save())
	// 	{
	// 		$mailbody = createmailbody($id);
	// 		$hospitalinfo = Hospital::find($serviceInfo->hospital_id);
	// 		$emails = ['email'=>$hospitalinfo->email];
			
	// 		Mail::send('mail.htmlmail',['html' => $mailbody,$emails], function ($message) use($emails) {
	// 			$message->from('link3developer@gmail.com', 'Healthplus');
			
	// 			if(strpos($emails['email'],',')!==false)
	// 			{
	// 				$emailarray = explode(',',$emails['email']);
	// 				foreach($emailarray as $email){
	// 					$message->to($email)->cc('joy.prokash@link3.net');
	// 				}
	// 			}else{
	// 				$message->to($emails['email'])->cc('joy.prokash@link3.net');
	// 			}
	// 			$message->subject('Service Request');
	// 		});
	// 	}
	// 	return redirect()->back()->with('success', 'SMO Request Submitted Successfully.');
	// }

	
	
	
	public function hotel_booking_request(Request $request)
    {
        
        if($request->has('pk')){
            $model = ServiceRequest::find($request->pk);
        }else{
            $model = new ServiceRequest();
            $model->service_id = 4;
        }
        
		$model->agent_id = $request->agent_id;
		$model->patient_id = $request->patient_id;
		$model->hospital_id = $request->hospital_id;
		if($request->has('pk')){
		    $model->reason_for_edit = $request->reason_for_edit;
		}
		if($model->save())
		{
		    if($request->has('pk')){
                $hotelBookingModel = HotelServiceRequest::where('service_request_id',$request->pk)->first();
            }else{
                $hotelBookingModel = new HotelServiceRequest();
                $hotelBookingModel->service_request_id = $model->id;
            }
			
			$hotelBookingModel->acmdtionType = $request->acmdtionType;
			$hotelBookingModel->checkin_date = date('Y-m-d H:i:s',strtotime($request->checkin_date));
			$hotelBookingModel->no_guest = $request->no_guest;
			$hotelBookingModel->no_patients = '1';
			$hotelBookingModel->additional_information = $request->additional_information;
			$hotelBookingModel->guestNames = $request->guestNames;
			$hotelBookingModel->referral_info = $request->referral_info;
			$hotelBookingModel->no_room = $request->no_room;
			$hotelBookingModel->payment_range = $request->payment_range;
			$hotelBookingModel->reservation_name = $request->reservation_name;
			if($hotelBookingModel->save()){
				$files=array();
				if($files=$request->hasFile('files')){
					$files = $request->file('files');
					foreach($files as $file){
						$ServiceRequestFilesModel = new ServiceRequestFiles();
						$name=$file->getClientOriginalName();
						$destinationPath = public_path('services/hotelBooking');
						$fileName = 'HB-'.$model->id.'-'.time().'_'.$name; 
						$file->move($destinationPath,$fileName);
						$ServiceRequestFilesModel->service_request_id = $model->id;
						$ServiceRequestFilesModel->files = 'services/hotelBooking/'.$fileName;
						$ServiceRequestFilesModel->save();
					}
				}
				
				$patientInformation = Patients::find($request->patient_id);
				$subject = 'HB-'.$model->id.' HOTEL BOOKING REQUEST FOR '.$patientInformation->firstName.' '.$patientInformation->lastName.'[ C-0000'.$patientInformation->id.' ]';
				$hospitalinfo = Hospital::find($request->hospital_id)->email_for_other;
				$success['REQ_ID'] =  'HB-'.$model->id;
				$data['ID'] = $model->id;
				$data['TYPE'] = 'HB';
				$data['patientInformation'] = $patientInformation;
				$data['subject'] = $subject;
				$data['hospitalinfo'] = $hospitalinfo;
				SendEmail::dispatch($data);
			    if($request->has('pk')){
			        $success['request_info'] = app('App\Http\Controllers\API\AjaxController')->getServiceWiseData($model->service_id, $model->id);
			    }
                
				return $this->sendResponse($success, 'Hotel booking Request Submitted Successfully.');
			}
		}
    }
	
	public function airport_pickup_request(Request $request)
	{
		$this->validate($request, [        
			//'files.*' => 'required|file|mimes:jpg,jpeg,pdf|max:204800',
		]);
		
        if($request->has('pk')){
            $model = ServiceRequest::find($request->pk);
        }else{
            $model = new ServiceRequest();
            $model->service_id = 3;
        }
		
		$model->agent_id = $request->agent_id;
		$model->patient_id = $request->patient_id;
		$model->hospital_id = $request->hospital_id;
		
		if($request->has('pk')){
		    $model->reason_for_edit = $request->reason_for_edit;
		}
		
		if($model->save()){
            if($request->has('pk')){
                $Pickupmodel = PickupServiceRequest::where('service_request_id',$request->pk)->first();
                
            }else{
                $Pickupmodel = new PickupServiceRequest();
                $Pickupmodel->service_request_id = $model->id;
            }
            
			$Pickupmodel->travelMode = $request->travelMode;
			$Pickupmodel->flightNumber = $request->flightNumber;
			$Pickupmodel->arrivalDestination = $request->arrivalDestination;
			$Pickupmodel->arrivalDT = date("Y-m-d H:i:s",strtotime($request->arrivalDT));
			$Pickupmodel->no_guest = $request->no_guest;
			$Pickupmodel->no_patients = '1';
			$Pickupmodel->additional_information = $request->additional_information;
			if($Pickupmodel->save()){
				$files=array();
				if($files=$request->hasFile('files')){
					$files = $request->file('files');
					foreach($files as $file){
						$ServiceRequestFilesModel = new ServiceRequestFiles();
						$name=$file->getClientOriginalName();
						$destinationPath = public_path('services/airportPickup');
						$fileName = 'AP-'.$model->id.'-'.time().'_'.$name; 
						$file->move($destinationPath,$fileName);
						$ServiceRequestFilesModel->service_request_id = $model->id;
						$ServiceRequestFilesModel->files = 'services/airportPickup/'.$fileName;
						$ServiceRequestFilesModel->save();
					}
				}
				$patientInformation = Patients::find($request->patient_id);
				$subject = 'AP-'.$model->id.' AIRPORT PICKUP REQUEST FOR '.$patientInformation->firstName.' '.$patientInformation->lastName.'[ C-0000'.$patientInformation->id.' ]';
				$hospitalinfo = Hospital::find($request->hospital_id)->email_for_other;
				$success['REQ_ID'] =  'AP-'.$model->id;
				$data['ID'] = $model->id;
				$data['TYPE'] = 'AP';
				$data['patientInformation'] = $patientInformation;
				$data['subject'] = $subject;
				$data['hospitalinfo'] = $hospitalinfo;
				SendEmail::dispatch($data);
				if($request->has('pk')){
			        $success['request_info'] = app('App\Http\Controllers\API\AjaxController')->getServiceWiseData($model->service_id, $model->id);
			    }
				return $this->sendResponse($success, 'Pickup Request Submitted Successfully.');
			}
		}
		
		
	}
	
	
	
	public function smoRequest(Request $request)
	{ 
		$this->validate($request, [        
				'hospital' => 'required|max:255', 
				//'files.*' => 'required|file|mimes:jpg,jpeg,pdf,txt|max:204800',
			]);
			
			if($request->has('pk')){
			    $model = ServiceRequest::find($request->pk);
			}else{
			    $model = new ServiceRequest();
			    $model->service_id = '2';
			}
	
			$model->agent_id = $request->agent_id;
			$model->patient_id = $request->patient_id;
			$model->hospital_id = $request->hospital;
			$model->doctor_id = $request->doctor;
			if($request->has('pk')){
    		    $model->reason_for_edit = $request->reason_for_edit;
    		}
			if($model->save()){
				
				if($request->has('pk')){
    			    $SmoServiceRequestmodel = SmoServiceRequest::where('service_request_id',$request->pk)->first();
    			}else{
    			    $SmoServiceRequestmodel = new SmoServiceRequest();
    			    $SmoServiceRequestmodel->service_request_id = $model->id;
    			}
				
				$SmoServiceRequestmodel->enquiryDetails = $request->inquiry;
				$SmoServiceRequestmodel->additionalInfo = $request->additionalInformation;
				$SmoServiceRequestmodel->hospital = $request->hospital;
				$SmoServiceRequestmodel->speciality = $request->speciality;
				$SmoServiceRequestmodel->doctor = $request->doctor;
				if($SmoServiceRequestmodel->save()){
					$files=array();
					if($files=$request->hasFile('files')){
						$files = $request->file('files');
						foreach($files as $file){
							$ServiceRequestFilesModel = new ServiceRequestFiles();
							$name=$file->getClientOriginalName();
							$destinationPath = public_path('services/smo');
							$fileName = 'SM-'.$model->id.'-'.time().'_'.$name; 
							$file->move($destinationPath,$fileName);
							$ServiceRequestFilesModel->service_request_id = $model->id;
							$ServiceRequestFilesModel->files = 'services/smo/'.$fileName;
							$ServiceRequestFilesModel->save();
						}
					}

					$patientInformation = Patients::find($request->patient_id);
					$subject = 'SM-'.$model->id.' SMO REQUEST FOR '.$patientInformation->firstName.' '.$patientInformation->lastName.'[ C-0000'.$patientInformation->id.' ]';
					$hospitalinfo = Hospital::find($request->hospital)->email_for_a_t;
				

					
					$success['REQ_ID'] =  'SM-'.$model->id;
					$data['ID'] = $model->id;
					$data['TYPE'] = 'SM';
					$data['patientInformation'] = $patientInformation;
					$data['subject'] = $subject;
					$data['hospitalinfo'] = $hospitalinfo;
                    SendEmail::dispatch($data);
			        if($request->has('pk')){
    			        $success['request_info'] = app('App\Http\Controllers\API\AjaxController')->getServiceWiseData($model->service_id, $model->id);
    			    }
					return $this->sendResponse($success, 'SMO Request Submitted Successfully.');
				}
			}
	}
	

	public function mailTemplateCheck()
	{
		$hospitalinfo = Hospital::find(8)->email_for_other;
		$patientInformation = Patients::find(8);
		$service_request_id = $data['ID'] = 11;
		$data['TYPE'] = 'SM';
		$data['patientInformation'] = $patientInformation;
		$data['subject'] = 'asdasdasdasdas';
		$data['hospitalinfo'] = $hospitalinfo;
		return view('mails.smo_mail',compact('data','service_request_id'));
	}

	public function telemedicine(Request $request)
	{ 
			$this->validate($request, [        
				'hospital' => 'required|max:255', 
				//'files.*' => 'required|file|mimes:jpg,jpeg,pdf|max:204800',
			]);
			if($request->has('pk')){
			    $model = ServiceRequest::find($request->pk);
			}else{
			    $model = new ServiceRequest();
			    $model->service_id = '6';
			}
			
			$model->agent_id = $request->agent_id;
			$model->patient_id = $request->patient_id;
			$model->hospital_id = $request->hospital;
			$model->doctor_id = $request->doctor;
			if($request->has('pk')){
    		    $model->reason_for_edit = $request->reason_for_edit;
    		}
			if($model->save()){
			    if($request->has('pk')){
			        $SmoServiceRequestmodel = TelemedicineServiceRequest::where('service_request_id',$request->pk)->first();
			    }else{
			        $SmoServiceRequestmodel = new TelemedicineServiceRequest();
				    $SmoServiceRequestmodel->service_request_id = $model->id;
			    }
				
				$SmoServiceRequestmodel->enquiryDetails = $request->inquiry;
				$SmoServiceRequestmodel->additionalInfo = $request->additionalInformation;
				$SmoServiceRequestmodel->hospital = $request->hospital;
				$SmoServiceRequestmodel->speciality = $request->speciality;
				$SmoServiceRequestmodel->doctor = $request->doctor;
				if($SmoServiceRequestmodel->save()){
					$files=array();
					if($files=$request->hasFile('files')){
						$files = $request->file('files');
						foreach($files as $file){
							$ServiceRequestFilesModel = new ServiceRequestFiles();
							$name=$file->getClientOriginalName();
							$destinationPath = public_path('services/telemedicine');
							$fileName = 'TM-'.$model->id.'-'.time().'_'.$name; 
							$file->move($destinationPath,$fileName);
							$ServiceRequestFilesModel->service_request_id = $model->id;
							$ServiceRequestFilesModel->files = 'services/telemedicine/'.$fileName;
							$ServiceRequestFilesModel->save();
						}
					}

					$patientInformation = Patients::find($request->patient_id);
					$subject = 'TM-'.$model->id.' Telemedicine REQUEST FOR '.$patientInformation->firstName.' '.$patientInformation->lastName.'[ C-0000'.$patientInformation->id.' ]';
					$hospitalinfo = Hospital::find($request->hospital)->email_for_a_t;
				

					
					$success['REQ_ID'] =  'TM-'.$model->id;
					$data['ID'] = $model->id;
					$data['TYPE'] = 'TM';
					$data['patientInformation'] = $patientInformation;
					$data['subject'] = $subject;
					$data['hospitalinfo'] = $hospitalinfo;
                    SendEmail::dispatch($data);
			        if($request->has('pk')){
    			        $success['request_info'] = app('App\Http\Controllers\API\AjaxController')->getServiceWiseData($model->service_id, $model->id);
    			    }
					return $this->sendResponse($success, 'Telemedicine Request Submitted Successfully.');
				}
			}
	}
	
	
	
	public function submitDoctorBookingRequest(Request $request){
		$this->validate($request, [
			'prefered_date_time' => 'required|max:255',         
			'hospital_id' => 'required|max:255', 
			//'files.*' => 'required|file|mimes:jpg,jpeg,pdf|max:204800',
		]);
        
        if($request->has('pk')){
            $model = ServiceRequest::find($request->pk);
        }else{
            $model = new ServiceRequest();
            $model->service_id = '1';
        }
		
		$model->agent_id = $request->agent_id;
		$model->patient_id = $request->patient_id;
		$model->hospital_id = $request->hospital_id;
		$model->doctor_id = $request->doctor_id;
		if($request->has('pk')){
		    $model->reason_for_edit = $request->reason_for_edit;
		}
		if($model->save()){
		    if($request->has('pk')){
		        $DoctorAptModel = DoctorApmtServiceRequest::where('service_request_id',$request->pk)->first();
		    }else{
		        $DoctorAptModel = new DoctorApmtServiceRequest();
		        $DoctorAptModel->service_request_id = $model->id;
		    }
			
			
			$DoctorAptModel->prefered_date_time = date("Y-m-d H:i:s",strtotime($request->prefered_date_time));
			$DoctorAptModel->additional_info_agent = $request->additional_info_agent;
			$DoctorAptModel->referral_info = $request->referral_info;
			$DoctorAptModel->department = $request->speciality;
			$DoctorAptModel->doctor = $request->doctor_id;
			if($DoctorAptModel->save()){
				$files=array();
				if($files=$request->hasFile('files')){
					$files = $request->file('files');
					foreach($files as $file){
						$ServiceRequestFilesModel = new ServiceRequestFiles();
						$name=$file->getClientOriginalName();
						$destinationPath = public_path('services/doctorsAppointment');
						$fileName = 'DA-'.$model->id.'-'.time().'_'.$name; 
						$file->move($destinationPath,$fileName);
						$ServiceRequestFilesModel->service_request_id = $model->id;
						$ServiceRequestFilesModel->files = 'services/doctorsAppointment/'.$fileName;
						$ServiceRequestFilesModel->save();
					}
				}

				$patientInformation = Patients::find($request->patient_id);
				$subject = 'DA-'.$model->id.' DOCTOR APPOINTMENT REQUEST FOR '.$patientInformation->firstName.' '.$patientInformation->lastName.'[ C-0000'.$patientInformation->id.' ]';
				$hospitalinfo = Hospital::find($request->hospital_id)->email_for_other;
			

				
				$success['REQ_ID'] =  'DA-'.$model->id;
				$data['ID'] = $model->id;
				$data['TYPE'] = 'DA';
				$data['patientInformation'] = $patientInformation;
				$data['subject'] = $subject;
				$data['hospitalinfo'] = $hospitalinfo;
                SendEmail::dispatch($data);
			    if($request->has('pk')){
			        $success['request_info'] = app('App\Http\Controllers\API\AjaxController')->getServiceWiseData($model->service_id, $model->id);
			    }
				return $this->sendResponse($success, 'Doctor Appointment Request Submitted Successfully.');
			}
		}
	}

	
	public function visa_request(Request $request)
	{
		$this->validate($request, [
			'expectedDate' => 'required|max:255',         
			'hospital_id' => 'required|max:255', 
			//'visa_att_files.*' => 'required|file|mimes:jpg,jpeg,pdf|max:204800',
		//	'passport_attch.*' => 'required|file|mimes:jpg,jpeg,pdf|max:204800',
		]);
		
		if($request->has('pk')){
            $model = ServiceRequest::find($request->pk);
        }else{
            $model = new ServiceRequest();
            $model->service_id = '5';
        }
		
		$model->agent_id = $request->agent_id;
		$model->patient_id = $request->patient_id;
		$model->hospital_id = $request->hospital_id;
		$model->doctor_id = $request->doctor;
		if($request->has('pk')){
		    $model->reason_for_edit = $request->reason_for_edit;
		}
		if($model->save())
		{
			$visaReqModel = new VisaRequest();
			
			if($request->has('pk')){
		        $visaReqModel = VisaRequest::where('service_request_id',$request->pk)->first();
		    }else{
		        $visaReqModel = new VisaRequest();
		        $visaReqModel->service_request_id = $model->id;
		    }
		    
			$visaReqModel->travel_date_time = date("Y-m-d H:i:s",strtotime($request->expectedDate));
			$visaReqModel->intended_treatment_request = $request->intended_treatment_req;
			$visaReqModel->additional_info_agent = $request->additional_info;
			$visaReqModel->referral_info = $request->referral_info;
			$visaReqModel->department = $request->department;
			$visaReqModel->doctor = $request->doctor;
			$visaReqModel->hospital = $request->hospital_id;
			if($visaReqModel->save())
			{
				$files=array();
				if($files=$request->file('visa_att_files')){
					foreach($files as $file){
						$ServiceRequestFilesModel = new ServiceRequestFiles();
						$name=$file->getClientOriginalName();
						$destinationPath = public_path('services/visaRequest');
						$fileName = 'VR-'.$model->id.'-'.time().'_'.$name; 
						$file->move($destinationPath,$fileName);
						$ServiceRequestFilesModel->service_request_id = $model->id;
						$ServiceRequestFilesModel->files = 'services/visaRequest/'.$fileName;
						$ServiceRequestFilesModel->save();
					}
				}
				
				if($request->has('guest_type')){
				    foreach($request->guest_type as $key=>$value)
    				{
    					$travellerModel = new Travellers();
    					$travellerModel->sr_id      = $model->id;
    					$travellerModel->guest_type = $value;
    					$travellerModel->first_name = $request->first_name[$key];
    					$travellerModel->last_name  = $request->last_name[$key];
    					$travellerModel->passport_no= $request->passport_no[$key];
    					$files=$request->file('passport_attch');
    					if(!empty($files))
    					{
    						$name=$files[$key]->getClientOriginalName();
    						$destinationPath = public_path('service/visaRequest');
    						$fileName = time().'_'.$name; 
    						$files[$key]->move($destinationPath,$fileName);
    						$travellerModel->passport_attch = 'service/visaRequest/'.$fileName;
    					}
    					$travellerModel->save();
    				}
				}

				$patientInformation = Patients::find($request->patient_id);
				$subject = 'VR-'.$model->id.' VISA REQUEST FOR '.$patientInformation->firstName.' '.$patientInformation->lastName.'[ C-0000'.$patientInformation->id.' ]';
				$hospitalinfo = Hospital::find($request->hospital_id)->email_for_other;

				$success['REQ_ID'] =  'VR-'.$model->id;
				$data['ID'] = $model->id;
				$data['TYPE'] = 'VR';
				$data['patientInformation'] = $patientInformation;
				$data['subject'] = $subject;
				$data['hospitalinfo'] = $hospitalinfo;
                SendEmail::dispatch($data);
			    if($request->has('pk')){
			        $success['request_info'] = app('App\Http\Controllers\API\AjaxController')->getServiceWiseData($model->service_id, $model->id);
			        $success['travellerInfo'] = Travellers::where('sr_id',$model->id)->get();
			    }
			    
				return $this->sendResponse($success, 'Visa Request Submitted Successfully.');
				
			}
		}
	}
	
	public function storeTravellers(Request $request)
	{
        if($request->has('guest_type')){
            foreach($request->guest_type as $key=>$value)
        	{
        		$travellerModel = new Travellers();
        		$travellerModel->sr_id      = $request->sr_id;
        		$travellerModel->guest_type = $value;
        		$travellerModel->first_name = $request->first_name[$key];
        		$travellerModel->last_name  = $request->last_name[$key];
        		$travellerModel->passport_no= $request->passport_no[$key];
        		$files=$request->file('passport_attch');
        		if(!empty($files))
        		{
        			$name=$files[$key]->getClientOriginalName();
        			$destinationPath = public_path('service/visaRequest');
        			$fileName = time().'_'.$name; 
        			$files[$key]->move($destinationPath,$fileName);
        			$travellerModel->passport_attch = 'service/visaRequest/'.$fileName;
        		}
        		$travellerModel->save();
        	}
        	
        	$success['data'] = Travellers::where('sr_id',$request->sr_id)->get();
        	$success['msg']  = 'success';
        	
        	return $this->sendResponse($success, 'Travellers Information stored successfully');
        }
	}
	
	
	
	// public function edit($id)
    // { 
	// 	$specialistList =  Specialist::all();
	// 	$data = Specialist::find($id);
	//   	return view('specialist.index',compact('data','specialistList'));
    // }
	
	// public function update(Request $request, $id){
	// 	$model = Specialist::find($id);
		
	// 	$this->validate($request, [
	// 		'specialization' => 'required|max:255',         
	// 		'detail' => 'required|max:255',       
    //     ]);
		
	// 	$model->specialization = $request->specialization;
	// 	$model->detail = $request->detail;
	
	// 	if($model->save())
	// 	{
	// 		return redirect('specialist')->with('success', 'Specialist Updated successfully.');
	// 	}
	// }
	
	// public function destroy($id)
	// {
		
	// }

	
}
