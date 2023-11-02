<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Hospital;
use App\Models\Country;
use App\Models\Patientmeta;
use App\Models\Doctors;
use App\Models\Patients;
use App\Models\User;
use Auth;
use App\Models\ServiceRequest; 
use App\Models\DoctorApmtServiceRequest; 
use App\Models\HotelServiceRequest; 
use App\Models\PickupServiceRequest; 
use App\Models\SmoServiceRequest; 
use App\Models\ServiceRequestComments; 
use App\Models\ServiceRequestFiles; 
use App\Models\Travellers; 
use App\Models\Patientshospitalid; 
use App\Models\Doctorsspecialization; 
use Mail;
use DB;
use Illuminate\Support\Arr;
use App\Http\Controllers\API\BaseController as BaseController;

class AjaxController extends BaseController
{
    public function getGroupList(Request $request)
    {
		$HospitalID = $request->hospital_id; 
		
		$hospitalGroup = Hospitalgroup::where('hospital_id', $HospitalID)->get();
		$drop = "<option value=''>SELECT</option>";
		if(!empty($hospitalGroup)){
			foreach($hospitalGroup as $value){
				$drop .= "<option value='".$value->id."'>".$value->groupName."</option>";
			}
		}
		
		$data = array(
			'msg' =>'SUCCESS',
			'data'=>$drop
		);
		
		echo json_encode($data);
	}

	public function getHospitalandDoctor(Request $request)
    {
        $data = Hospital::all();
    	$success['data'] = $data;
    		
    	if($request->has('hospital_id')){
    		$data = Hospital::select('departments')->find($request->hospital_id)->first();
    		$departments = Doctorsspecialization::whereIn('id', explode(',',$data->departments))->get();
    		$success['data'] = $departments;
    	}
    	
    	if($request->has('department_id')){
    		$ids = explode(',',$request->department_id);
    		$doctors = Doctors::query();
    		foreach($ids as $id){
    			$doctors->orWhere('speciality_id','like','%'.$id.'%');
    			//$doctors->orwhereIntegerInRaw('speciality_id',$ids);
    		}
    		$doctors = $doctors->get();
    		//dd(\DB::getQueryLog()); 
    		$success['data'] = $doctors;
    	}
    	
    	if($request->has('hospital_id') && $request->has('department_id')){
    	    $ids = explode(',',$request->department_id);
    		$doctors = Doctors::query();
    		$doctors->Where('hospital_id',$request->hospital_id);
    		foreach($ids as $id){
    			$doctors->orWhere('speciality_id','like','%'.$id.'%');
    		}
    		$doctors = $doctors->get();
    		$success['data'] = $doctors;
    	}
    	
		return $this->sendResponse($success,'');

	}
	
	public function submitServiceFromPatient(Request $request)
	{
		$model = new ServiceRequest();
		$model->service_id = $request->serviceid;
		$model->patient_id = $request->patientid;
		$model->agent_id = Auth::user()->id;
		$model->patient = 1;
		$model->save();
	}
	
	public function getRequestsView(Request $request)
	{
		$request_info= $this->getServiceWiseData($request->service_id,$request->request_id);
		$patientInfo = Patients::find($request_info[0]->patient_id);
		$travellerInfo = Travellers::where('sr_id',$request->request_id)->get();
		$data = array(
			'request_info'=>$request_info,
			'patientInfo'=>$patientInfo,
			'travellerInfo'=>$travellerInfo
		);
		return $this->sendResponse($data,'');
					
		
		// $reqInfodiv  = '<div id="print">';
		// $reqInfodiv .= '<div class="popup-block">';
		// $reqInfodiv .= '<div class="row">';
		// $reqInfodiv .= '<div class="col-12">';

	
		
		// $reqInfodiv .= '<h4><span class="line-border">Request Information</span><span class="edit-popup">'.$edit.'</span></h4>';
		// $reqInfodiv .= '<div class="request-info-block">';
		
		// $reqInfodiv.='<div class="btn-sec-width"><b>Guest ID:</b> C-0000'.$request_info->patient_id.'</div><div class="btn-sec-width"><span class="status-btn">'.$service_name[0].'</span></div>';
		
		
		
		// $reqInfodiv.='<div><b>Request ID:</b> R-'.$service_name[1].'-'.$request_id.'</div>';
		
		// $reqInfodiv.='<div><b>Current Status:</b> '.Patients::getStatus($request_info->status).'</div>';
		
		// $reqInfodiv.='<div class="btn-sec-complete"><a href="'.url('close-ticket/'.$request_id).'" class="btn-complete" onclick="return confirm(\'Are you sure you want to Close\?\')">Complete</a></div>';
		
		// $reqInfodiv.='<div class="request-info-des"><b>Description:</b> '.$request_info->description.'</div>';
		
		
		// $reqInfodiv.= '</div>';
	
		// $reqInfodiv.= '</div>';
		// $reqInfodiv.= '</div>';
		// $reqInfodiv.= '</div>';
		
		// $patientInfodiv = '<div class="popup-block">';
		// $patientInfodiv.= '<div class="row">';
		// $patientInfodiv.= '<div class="col-12 col-lg-7">';
		// $patientInfodiv.= '<h4><span class="line-border">Patient (Primary Information)</span></h4>';
		// $patientInfodiv.='<div class="popup-block-holder">
		//    <ul>
		// 	 <li><span>First Name</span><span>: '.$patientInfo->firstName.'</span></li>
		// 	 <li><span>Last Name</span><span>: '.$patientInfo->lastName.'</span></li>
		// 	 <li><span>Sex</span><span>: '.$patientInfo->sex.'</span></li>
		// 	 <li><span>DOB</span><span>: '.date('d-m-Y',strtotime($patientInfo->dob)).'</span></li>
		// 	 <li><span>Age</span><span>: '.Patients::findAge($patientInfo->dob).'</span></li>
		// 	 <li><span>National Per Passport</span><span>: '.$patientInfo->nationality.'</span></li>
		// 	 <li><span>Country of Recidence</span><span>: '.$patientInfo->country.'</span></li>
		//    </ul>
		// </div>'; 
		// $patientInfodiv.='</div>';
		
		// if(Auth::user()->role_id != 2){
		// 	$patientInfodiv .= '<div class="col-12 col-lg-5 pl50">
        //     <h4><span class="line-border">Patient (Primary Information)</span></h4>
		// 		<div class="popup-block-holder small-first-span">
		// 		   <ul>
		// 			 <li><span>Email</span><span>: '.$patientInfo->email.'</span></li>
		// 			 <li><span>Contact</span><span>: '.$patientInfo->contact.'</span></li>
		// 			 <li><span>Alternative Contact</span><span>: '.$patientInfo->altcontact.'</span></li>
		// 		   </ul>
		// 		</div> 
		// 	</div>';
		// }else{
			 
		// 	$usid = Patientshospitalid::where('user_id',$request_info->patient_id)->where('hospital_id',$request_info->hospital_id)->get();
		// 	if(!empty($usid[0]))
		// 	{
		// 		$patientInfodiv .= '<div>';
		// 		$patientInfodiv .= '<p>UHID : '.$usid[0]->us_id.'</p>';
		// 		$patientInfodiv .= '</div>';
		// 	}else{
		// 		$patientInfodiv .= '<div>';
		// 		$patientInfodiv .= '<button class="btn btn-primary btn-sm" id="addusid"  onclick="clickus()">ADD UHID</button>';
		// 		$patientInfodiv .= '<br><br>
		// 		<div style="display:none" id="us_id">
		// 			<input type="text" style="width:70%;float:left" id="usid" class="form-control"  />
		// 			<button type="button" style="margin-left:10px;padding:9px" onclick="saveusid('.$patientInfo->id.')" class="btn btn-primary btn-sm">Submit</button>
		// 		</div>
		// 		';
		// 		$patientInfodiv .= '</div>';
		// 	}
		// 	($request_info->service_id == 1 ?$target = 'da':($request_info->service_id == 2?$target = 'sm':($request_info->service_id == 3?$target = 'ap':($request_info->service_id == 4?$target = 'hb':($request_info->service_id == 5?$target = 'vr':''))))); 
		// 	if(Auth::user()->role_id == 2)
		// 		{
		// 			if($request_info->reply == 0)
		// 			{
		// 				$patientInfodiv .= '<div style="float:left">';
		// 				$patientInfodiv.= '<a href="#" class="btn btn-danger views" data-toggle="modal" data-target="#'.$target.'" data-id="'.$request_info->id.'" style="font-size: 13px;margin-left:225px" id="a'.$target.'" >Reply</a>'; 
		// 				$patientInfodiv .= '</div>';
		// 			}
		// 		}
		// }
		// $patientInfodiv.='</div>';
	
		// $data = DB::select(DB::raw("SELECT `hospital`.`hospitalTeam`,`category`.`title` as `team`,`category`.`parent` as `parentid`,(SELECT `category`.`title` FROM `category` WHERE `category`.`id` = `parentid`) as `group` FROM `hospital`,`category` WHERE `hospital`.`id` = '".$request_info->hospital_id."' AND `hospital`.`hospitalTeam` = `category`.`id`"));
		// //echo "<pre>"; print_r($data);exit;
		// $serviceWiseData = $this->getServiceWiseData($request_info->service_id,$request_info->id);
		
		// if($request_info->service_id == '2')
		// {
		// 	$assignedInfoDiv ='<div class="popup-block">
		// 	   <div class="row">
		// 		 <div class="col-12">
		// 			<h4><span class="line-border">Referral Information</span></h4>
		// 			<div class="popup-block-holder">
		// 			   <ul>
		// 				 <li><span>Hospital Group</span><span>: '.@$data[0]->group.'</span></li>
		// 				 <li><span>Hospital Team</span><span>: '.@$data[0]->team.'</span></li>
		// 				</ul>
		// 			</div> 
		// 		 </div>
		// 	   </div>
		// 	</div>';
		// }else
		// {
		// 	$assignedInfoDiv ='<div class="popup-block">
		// 	   <div class="row">
		// 		 <div class="col-12">
		// 			<h4><span class="line-border">Referral Information</span></h4>
		// 			<div class="popup-block-holder">
		// 			   <ul>
		// 				 <li><span>Hospital Group</span><span>: '.@$data[0]->group.'</span></li>
		// 				 <li><span>Hospital Team</span><span>: '.@$data[0]->team.'</span></li>
		// 				 <li><span>Created By</span><span>: '.User::find($request_info->agent_id)->name.'</span></li>
		// 				 <li><span>Created On</span><span>: '.date('jS F Y g:ia',strtotime($request_info->created_at)).'</span></li>
		// 				 <li><span>Referral infromation</span><span>: '.@$serviceWiseData[0]->referral_info.'</span></li>
		// 			   </ul>
		// 			</div> 
		// 		 </div>
		// 	   </div>
		// 	</div>';
		// }
		// $submittedInquiry = '';
		// $consultentFeedback = '';
		
		// if($request_info->service_id == '2')
		// {
		// 	$submittedInquiry.='<div class="popup-block">
		// 	   <div class="row">
		// 		 <div class="col-12">
		// 			<h4><span class="line-border">SMO Enquiry Submitted</span></h4>
		// 			<div class="popup-block-holder big-first-span">
		// 			   <ul>
		// 				 <li><span>Enquiry Details : </span><span>: '.@$serviceWiseData[0]->enquiryDetails.'</span></li>
		// 				 <li><span>Additional Information </span><span>: '.@$serviceWiseData[0]->additionalInfo.'</span></li>
		// 				 <li><span>Attachments</span> ';
		// 				 $filesArray = explode(',',$serviceWiseData[0]->files);
		// 				 $i=1;
		// 				 if(isset($filesArray))
		// 				 {
		// 					 $submittedInquiry.= '<span>';
		// 					 foreach($filesArray as $file){
		// 						$submittedInquiry.= '<a target="_blank" href="'.url('/').'/public/'.$file.'">'.last(explode("/",$file)).'</a><br>';
		// 						$i++;
		// 					}
		// 					$submittedInquiry.= '</span>';
		// 				 }
						
							
		// 				$submittedInquiry .='</li>';
		// 			$submittedInquiry .='</ul>
		// 			</div> 
		// 			<table class="table popup-table">
		// 			 <thead>
		// 			  <tr>
		// 				<th>Hospital</th>
		// 				<th>Speciality</th>
		// 				<th>Doctor</th>
		// 			  </tr>
		// 			</thead>
		// 			<tbody>  
		// 			  <tr>
		// 				<td>'.$serviceWiseData[0]->hospitalName.'</td>
		// 				<td>'.$serviceWiseData[0]->speciality.'</td>
		// 				<td>'.getDoctorName(@$serviceWiseData[0]->doctor).'</td>
		// 			  </tr>
					 
		// 			</tbody>  
		// 		  </table>
		// 		 </div>
		// 	   </div>
		// 	</div>';
			
			
		// 	$consultentFeedback = '';
		// 	if($serviceWiseData[0]->consultents_feedback != '')
		// 	{
			
		// 		$consultentFeedback.='<div class="row">';
		// 		$consultentFeedback.='<div class="col-12">';
		// 		$consultentFeedback.='<h4><span class="line-border">SMO FeedBack From Consultant</span></h4>';
		// 		$consultentFeedback.='<div class="popup-block-holder big-first-span">';
		// 		$consultentFeedback.='<ul>
		// 				 <li><span>Fecility : </span><span>: '.$serviceWiseData[0]->hospitalName.'</span></li>
		// 				 <li><span>Speciality </span><span>: '.$serviceWiseData[0]->speciality.'</span></li>
		// 				 <li><span>Consultant Name </span><span>: '.$serviceWiseData[0]->doctor.'</span></li>
		// 				 <li><span>Cost Quotation </span><span>: '.$serviceWiseData[0]->costQuot.'</span></li>
		// 				 <li><span>Duration of Treatment/Stay </span><span>: '.$serviceWiseData[0]->duration_of_treatment.'</span></li>
		// 				 <li><span>Consultants FeedBack </span><span>: '.$serviceWiseData[0]->consultents_feedback.'</span></li>
		// 				 <li><span>General Directives </span><span>: '.$serviceWiseData[0]->generalDirectives.'</span></li>';
		// 		$consultentFeedback .='</li>';
		// 		$consultentFeedback .='</ul></div></div></div>';
					
		// 	}
		// }
		// else if($request_info->service_id == '1')
		// {
		// 	if($request_info->doctor_id)
		// 	{
		// 		$doctors = Doctors::find($request_info->doctor_id);
		// 		$doctorName = $doctors->name;
		// 	}else{
		// 		$doctorName = '';
		// 	}
			
		// 	$submittedInquiry ='<div class="popup-block">
		// 	   <div class="row">
		// 		 <div class="col-12">
		// 			<p>Doctor Appointment Request Submitted</p>
		// 			<div class="popup-block-holder big-first-span">
		// 			   <ul>
		// 				 <li><span>Doctor</span><span>: '.$doctorName.'</span></li>
		// 				 <li><span>Speciality</span><span>: '.@$serviceWiseData[0]->speciality.'</span></li>
		// 				 <li><span>Preferred Date & Time</span><span>: '.date('jS F Y g:ia',strtotime(@$serviceWiseData[0]->prefered_date_time)).'</span></li>
		// 				 <li><span>Additional Information</span><span>: '.@$serviceWiseData[0]->additional_info_agent.'</span></li>
		// 				 <li><span>Attachments</span> ';
		// 				 $i=1;
		// 				 if(isset($filesArray))
		// 				 {
		// 					 $submittedInquiry.= '<span>';
		// 					 foreach($filesArray as $file){
		// 						$submittedInquiry.= '<a target="_blank" href="'.url('/').'/public/'.$file.'">'.last(explode("/",$file)).'</a>';
		// 						$i++;
		// 					}
		// 					$submittedInquiry.= '</span>';
		// 				 }
						
							
		// 	$submittedInquiry .='</li>';
			
		// 	$submittedInquiry .='</ul></div></div></div></div>';
			
		// 	if($request_info->reply == 1)
		// 	{
		// 		$consultentFeedback = '<div class="popup-block">
		// 		   <div class="row">
		// 			 <div class="col-12">
		// 				<p>Doctor Appointment Confirmed</p>
		// 				<div class="popup-block-holder big-first-span">
		// 				   <ul>
		// 					 <li><span>Doctor</span><span>: '.$doctorName.'</span></li>
		// 					 <li><span>Speciality</span><span>: '.@$serviceWiseData[0]->speciality.'</span></li>
		// 					 <li><span>Booked Date & Time </span><span>: '.date('jS F Y g:ia',strtotime(@$serviceWiseData[0]->booked_date_time)).'</span></li>
		// 					 <li><span>Additional Information</span><span>: '.@$serviceWiseData[0]->additional_info_hospital.'</span></li>
		// 				   </ul>
		// 				</div> 
		// 			 </div>
		// 		   </div>
		// 		</div>';
		// 	}
			
		// }
		// else if($request_info->service_id == '3')
		// {
			
		// 	$submittedInquiry.='<div class="popup-block">
		// 	   <div class="row">
		// 		 <div class="col-12">
		// 			<h4><span class="line-border">Arrival Pickup Details Submitted</span></h4>
		// 			<div class="popup-block-holder big-first-span">
		// 			   <ul>
		// 				 <li><span>Travel Mode : </span><span>: '.@$serviceWiseData[0]->travelMode.'</span></li>
		// 				 <li><span>Flight Number </span><span>: '.@$serviceWiseData[0]->flightNumber.'</span></li>
		// 				 <li><span>Arrival Destination </span><span>: '.@$serviceWiseData[0]->arrivalDestination.'</span></li>
		// 				 <li><span>Number of Patients </span><span>: '.@$serviceWiseData[0]->no_patients.'</span></li>
		// 				 <li><span>Number of Additional Passengers </span><span>: '.@$serviceWiseData[0]->no_guest.'</span></li>
		// 				 <li><span>Total Number of Passengers </span><span>: '.(@$serviceWiseData[0]->no_patients + @$serviceWiseData[0]->no_guest).'</span></li>
		// 				 <li><span>Attachments</span> ';
		// 				$filesArray = explode(',',$serviceWiseData[0]->files);
		// 				$i=1;
		// 				 if(isset($filesArray))
		// 				 {
		// 					 $submittedInquiry.= '<span>';
		// 					 foreach($filesArray as $file){
		// 						$submittedInquiry.= '<a target="_blank" href="'.url('/').'/public/'.$file.'">'.last(explode("/",$file)).'</a><br>';
		// 						$i++;
		// 					}
		// 					$submittedInquiry.= '</span>';
		// 				 }
						
							
		// 				$submittedInquiry .='</li>';
		// 				$submittedInquiry.= ' <li><span>Additional Information</span> : '.@$serviceWiseData[0]->additional_information.'</li>';
		// 				$submittedInquiry .='</ul>';
		// 				$submittedInquiry .='</div></div></div></div>';
						
		// 	$consultentFeedback.='<div class="popup-block">
		// 	   <div class="row">
		// 		 <div class="col-12">
		// 			<h4><span class="line-border">Pickup Confirmed</span></h4>
		// 			<div class="popup-block-holder big-first-span">
		// 			   <ul>
		// 				 <li><span>Confirmation Details : </span><span>: '.@$serviceWiseData[0]->confirmationDetails.'</span></li>
		// 				 <li><span>Attachments</span> ';
		// 				$filesArray = explode(',',$serviceWiseData[0]->files);
		// 				$i=1;
		// 				 if(isset($filesArray))
		// 				 {
		// 					 $submittedInquiry.= '<span>';
		// 					 foreach($filesArray as $file){
		// 						$consultentFeedback.= '<a target="_blank" href="'.url('/').'/public/'.$file.'">'.last(explode("/",$file)).'</a><br>';
		// 						$i++;
		// 					}
		// 					$submittedInquiry.= '</span>';
		// 				 }
						
							
		// 				$consultentFeedback .='</li>';
		// 				$consultentFeedback .='</ul>';
		// 				$submittedInquiry .='</div></div></div></div>';
		
			
		// }
		// else if($request_info->service_id == '4')
		// {
		// 	$guests = explode(',',$serviceWiseData[0]->guestNames);
		// 	$submittedInquiry.='<div class="popup-block">
		// 	   <div class="row">
		// 		 <div class="col-12">
		// 			<h4><span class="line-border">Hotel Booking Request Submitted</span></h4>
		// 			<div class="popup-block-holder big-first-span">
		// 			   <ul>
		// 				 <li><span>Intended Checkin Date : </span><span>: '.date('jS F Y g:ia',strtotime($serviceWiseData[0]->checkin_date)).'</span></li>
		// 				 <li><span>Accomodation Type </span><span>: '.$serviceWiseData[0]->acmdtionType.'</span></li>
		// 				 <li><span>Additional Information </span><span>: '.$serviceWiseData[0]->additional_information.'</span></li>
		// 				 <li><span>Total Number of Guests </span><span>: '.(COUNT(explode(',',$serviceWiseData[0]->guestNames)) +1).'</span></li>
		// 				 <li><span>Number of Patients </span><span>: 1</span></li>
		// 				 <li><span>Number of Guests</span><span>: '.COUNT($guests).'</span></li></ul>';
						 
						 
						 
		// 			$submittedInquiry.= '<table class="table popup-table"><tr><td>Guest  Type</td><td>First Name</td><td>Last Name</td><td>Age</td></tr>';
		// 			$submittedInquiry.= '<tr><td>Patient</td><td>'.$patientInfo->firstName.'</td><td>'.$patientInfo->lastName.'</td><td>'.Patients::findAge($patientInfo->dob).'</td></tr>';
		// 			foreach($guests as $guest){
		// 				if(strpos($guest, ' ') !== false)
		// 				{
		// 					$name = explode(' ', $guest);
		// 					$submittedInquiry.= '<tr><td>Additional Guest</td><td>'.$name[0].'</td><td>'.$name[1].'</td><td></td></tr>';
		// 				}else
		// 				{
		// 					$submittedInquiry.= '<tr><td>Additional Guest</td><td>'.$guest.'</td><td></td><td></td></tr>';
		// 				}
				
		// 			}
		// 			$submittedInquiry.= '</table>';
		// 				$submittedInquiry.= '<ul><li><span>Attachments</span> '; 
		// 				$filesArray = explode(',',$serviceWiseData[0]->files);
		// 				$i=1;
		// 				 if(isset($filesArray))
		// 				 {
		// 					 $submittedInquiry.= '<span>';
		// 					 foreach($filesArray as $file){
		// 						$submittedInquiry.= '<a target="_blank" href="'.url('/').'/public/'.$file.'">'.last(explode("/",$file)).'</a><br>';
		// 						$i++;
		// 					}
		// 					$submittedInquiry.= '</span>';
		// 				 }
						
							
		// 				$submittedInquiry .='</li>';
		// 				$submittedInquiry.= ' <li><span>Additional Information</span> : '.$serviceWiseData[0]->additional_information.'</li>';
		// 				$submittedInquiry .='</ul>';
		// 				$submittedInquiry .='</div></div></div></div>';
			
		// 	$consultentFeedback.='<div class="popup-block">
		// 	   <div class="row">
		// 		 <div class="col-12">
		// 			<h4><span class="line-border">Hotel Booking Confirmed</span></h4>
		// 			<div class="popup-block-holder big-first-span">
		// 			   <ul>
		// 				 <li><span>Confirmation Details </span><span>: '.$serviceWiseData[0]->confirmationDetails.'</span></li>
		// 				 <li><span>Attachments</span>'; 
		// 				$filesArray = explode(',',$serviceWiseData[0]->files);
		// 				$i=1;
		// 				 if(isset($filesArray))
		// 				 {
		// 					 $submittedInquiry.= '<span>';
		// 					 foreach($filesArray as $file){
		// 						$consultentFeedback.= '<a target="_blank" href="'.url('/').'/public/'.$file.'">'.last(explode("/",$file)).'</a><br>';
		// 						$i++;
		// 					}
		// 					$submittedInquiry.= '</span>';
		// 				 }
		// 	$consultentFeedback .='</li></ul></div></div></div></div>';			 
		
		// }else if($request_info->service_id == '5')
		// {
		// 	if(Auth::user()->role_id == 1)
		// 	{
		// 		$submittedInquiry.='<div class="text-right pr0"><a href="" title="" class="btn-complete">forward to embassy </a></div>';
		// 	}
		// 	$submittedInquiry.='<div class="popup-block">
		// 	   <div class="row">
		// 		 <div class="col-12">
		// 			<h4><span class="line-border">Visa Request Details Submitted</span></h4>
		// 			<div class="popup-block-holder big-first-span">
		// 			   <ul>';
		// 			if(Auth::user()->role_id == 2)
		// 			{
		// 				$submittedInquiry.=	'<li><span>Doctor Name</span><span>: '.getDoctorName($serviceWiseData[0]->doctor).'</span></li>
		// 				 <li><span>Department</span><span>: '.$serviceWiseData[0]->speciality.'</span></li>';
		// 			}
		// 			$submittedInquiry.=	'<li><span>Expected Date of Travels</span><span>: '.date('jS F Y g:ia',strtotime($serviceWiseData[0]->prefered_date_time)).'</span></li>
		// 				 <li><span>Intended Treatment Required</span><span>: '.$serviceWiseData[0]->intended_treatment.'</span></li>
		// 				 <li><span>Additional Information</span><span>: '.$serviceWiseData[0]->additional_info_agent.'</span></li>';
		// 				if(!empty($serviceWiseData[0]->files))
		// 				{
		// 					$filesArray = explode(',',$serviceWiseData[0]->files);
						
		// 					$submittedInquiry.= ' <li><span>Attachments</span>';
		// 					$i=1;
		// 					if(count($filesArray) > 0)
		// 					{
		// 						$submittedInquiry.= '<span>';
		// 						foreach($filesArray as $file){
		// 						$submittedInquiry.= '<a target="_blank" target="_blank" href="'.url('/').'/public/'.$file.'">'.last(explode("/",$file)).'</a><br>';
		// 						$i++;
		// 						}
		// 						$submittedInquiry.= '<span>';
		// 					}
							
		// 				}
						
		// 				$submittedInquiry.= '</li>';
		// 				$countTraveller = DB::select(DB::raw("SELECT (SELECT COUNT(`guest_type`)  FROM `travellers` WHERE `guest_type`='Patient' AND `sc_id` = '$request_id') as `tguest_type`,(SELECT COUNT(`guest_type`)  FROM `travellers` WHERE `guest_type`='Attender' AND `sc_id` = '$request_id') as `tattender` FROM `travellers` GROUP BY `sc_id` "));
		// 				$submittedInquiry.='<li><span>Number Of Travellers</span><span>: '.($countTraveller[0]->tguest_type + $countTraveller[0]->tattender).'</span></li>';
		// 				$submittedInquiry.='<li><span>Number of Patients</span><span>: '.$countTraveller[0]->tguest_type.'</span></li>';
		// 				$submittedInquiry.='<li><span>Number of Attenders</span><span>: '.$countTraveller[0]->tattender.'</span></li>';
		// 				$submittedInquiry.='</ul>
		// 			</div> 
		// 		</div> 
		// 	</div>';
			
		// 	$submittedInquiry.= '<table class="table popup-table">';
		// 	$submittedInquiry.= '<tr><td>GUEST TYPE</td><td>FIRST NAME</td><td>LAST NAME</td><td>PASSPORT NUMBER</td><td>PASSPORT COPY</td></tr>';
			
		// 	$travellerInfo = Travellers::where('sc_id',$serviceWiseData[0]->service_request_id)->get();
			
		// 	foreach($travellerInfo as $traveller)
		// 	{
		// 		$submittedInquiry.= '<tr><td>'.$traveller->guest_type.'</td><td>'.$traveller->first_name.'</td><td>'.$traveller->last_name.'</td><td>'.$traveller->passport_no.'</td><td><a target="_blank" target="_blank" href="'.url('/').'/public/'.$traveller->passport_attch.'">Passport</td></tr>';
		// 	}
		// 	$submittedInquiry.= '</table>';
			
			
		// 	$consultentFeedback.='<div class="popup-block">
		// 	   <div class="row">
		// 		 <div class="col-12">
		// 			<h4><span class="line-border">Visa Request Details Submitted</span></h4>
		// 			<div class="popup-block-holder big-first-span">
		// 			   <ul>';
		// 			   if($serviceWiseData[0]->existing_visa_letter == '')
		// 				{
		// 					$consultentFeedback.= '<li><span>Existing Visa Letter : </span></li>';
		// 				}else
		// 				{
		// 						$consultentFeedback.= '<input type="button" class="btn btn-primary btn-sm" onclick="openUploadbox('.$serviceWiseData[0]->id.')" data-toggle="modal" data-target="#exampleModal" value="Upload">';
		// 						if( strpos($serviceWiseData[0]->existing_visa_letter,',') !== false ) 
		// 						{
		// 							$uparray = explode(',',$serviceWiseData[0]->existing_visa_letter);
		// 							$i=1;
		// 							foreach(array_reverse($uparray) as $value)
		// 							{
		// 								$consultentFeedback.= '<li><span>Existing Visa Letter : <a target="_blank" href="'.url('/').'/public/'.$value.'">VISA LETTER</a></span>'.(($i==1)?'':'OLD').'</li>';
		// 								$i++;
		// 							}
		// 						}else
		// 						{
									
		// 							$consultentFeedback.= '<li><span>Existing Visa Letter : <a target="_blank" href="'.url('/').'/public/'.$serviceWiseData[0]->existing_visa_letter.'">VISA LETTER</a></span></li>';
		// 						}
		// 				}
		// 				 $consultentFeedback.='<li><span>Original Email Which Included the Attachments :</span><span>: '.$serviceWiseData[0]->additional_info_hospital.'</span></li>
		// 				 <li><span>Additional Info for Attachments</span><span>: '.($serviceWiseData[0]->additional_info_attachment == '')?'':$serviceWiseData[0]->additional_info_attachment.'</span></li>
		// 			  </ul>
		// 			</div> 
		// 		</div> 
		// 	</div> ';
			
		// }
		// $reqInfodiv .= '</div>';
		
		
	}
	
// 	public function saveUsID(Request $request)
// 	{
// 		$patient_id = $request->userid;
// 		$patientInfo = Patients::find($request->userid);
// 		$usid = $request->usid;
// 		$hospital_id = Auth::user()->hospital;
// 		$model = new Patientshospitalid();
// 		$model->user_id = $patient_id;
// 		$model->name = $patientInfo->firstName.' '.$patientInfo->lastName;
// 		$model->hospital_id = $hospital_id;
// 		$model->us_id = $usid;
// 		$model->save();
// 		$data = array(
// 			'msg' =>'SUCCESS',
// 			'usid'=>$usid,
// 		);
// 		echo json_encode($data);
// 	}

    public function saveUsID(Request $request)
 	{
 		$patient_id = $request->patient_id;
 		$patientInfo = Patients::find($patient_id);
 	//	$hospital_id = Auth::user()->hospital;
//  		$model = new Patientshospitalid();
//  		$model->user_id = $patient_id;
//  		$model->name = $patientInfo->firstName.' '.$patientInfo->lastName;
//  		$model->hospital_id = $hospital_id;
 		$patientInfo->uhID = $request->uhid;
 		$patientInfo->save();
 		$patientInfo = Patients::find($patient_id);
 		
        $data = array(
			'patientInfo'=>$patientInfo,
		);
		return $this->sendResponse($data,'');
 	}
	
	public function getPatientsInfo(Request $request)
	{
		if($request->has('request_id')){
			$patientInfo = Patients::find($request->request_id);
		}else if($request->has('agent_id')){
			$patientInfo = Patients::where('agent_id',$request->agent_id)->get();
		}else if($request->has('agent_id') && $request->has('type')){
			$patientInfo = Patients::where('agent_id',$request->agent_id)->get();
		}else{
		    $patientInfo = Patients::all();
		}
		
		$success['data'] = $patientInfo;
		return $this->sendResponse($success,'');
	}
	
	public function getPatientWiseServiceStatus(Request $request)
	{
		
		$patient_id = $request->patient_id;
		$service_id = $request->service_id;
		$serviceRequest = ServiceRequest::where('patient_id', $patient_id)->where('service_id', $service_id)->where('status', 0)->get();
		
		if(count($serviceRequest) > 0)
		{
			$message = '<div class="col-md-12">
							<div class="alert alert-warning">
							<strong>One Service Request already Open.</strong>
						</div></div>';
			$data = array(
				'msg' =>'SUCCESS',
				'message'=>$message,
			);
			
			
		}else
		{
			$data = array(
				'msg' =>'FAILED',
			);
		}
		
		echo json_encode($data);
	}
	
	public function getServiceWiseData($id,$serviceRequestID)
	{
	    if($id == '')
	    {
	        $id = ServiceRequest::find($serviceRequestID)->service_id;
	    }
	    
		if($id == '1')
			{
				$data = DB::table('sr_doctor_apmt')
						->leftJoin('service_request', 'service_request.id', '=', 'sr_doctor_apmt.service_request_id')
						->select('sr_doctor_apmt.*','service_request.*')
						->selectRaw('(SELECT GROUP_CONCAT(`files`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_doctor_apmt`.`service_request_id`) as files')
						->selectRaw('(SELECT `specialization` FROM `departments` WHERE `departments`.`id`= `sr_doctor_apmt`.`department`) as departmentName')
						->selectRaw('(SELECT GROUP_CONCAT(`id`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_doctor_apmt`.`service_request_id`) as fileIds')
						->selectRaw('(SELECT `hospitalName` FROM `hospital` WHERE `hospital`.`id`= `service_request`.`hospital_id`) as hospitalName')
						->selectRaw('(SELECT `name` FROM `doctors` WHERE `doctors`.`id`= `service_request`.`doctor_id`) as doctorName')
						->where(['sr_doctor_apmt.service_request_id' => $serviceRequestID])
						->get();
			}
		else if($id == '2')
			{
				$data = DB::table('sr_smo')
						->leftJoin('service_request', 'service_request.id', '=', 'sr_smo.service_request_id')
						->select('sr_smo.*','service_request.*')
						->selectRaw('(SELECT GROUP_CONCAT(`files`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_smo`.`service_request_id`) as files')
						->selectRaw('(SELECT GROUP_CONCAT(`id`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_smo`.`service_request_id`) as fileIds')
						->selectRaw('(SELECT `hospitalName` FROM `hospital` WHERE `hospital`.`id`= `sr_smo`.`hospital`) as hospitalName')
						->selectRaw('(SELECT `specialization` FROM `departments` WHERE `departments`.`id`= `sr_smo`.`speciality`) as departmentName')
						->selectRaw('(SELECT `specialization` FROM `departments` WHERE `departments`.`id`= `sr_smo`.`speciality`) as specality')
						->selectRaw('(SELECT `name` FROM `doctors` WHERE `doctors`.`id`= `service_request`.`doctor_id`) as doctorName')
						->where(['sr_smo.service_request_id' => $serviceRequestID])
						->get();
			}
		else if($id == '3')
			{
				$data = DB::table('sr_pickup')
						->leftJoin('service_request', 'service_request.id', '=', 'sr_pickup.service_request_id')
						->select('sr_pickup.*','service_request.*')
						->selectRaw('(SELECT GROUP_CONCAT(`files`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_pickup`.`service_request_id`) as files')
						->selectRaw('(SELECT GROUP_CONCAT(`id`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_pickup`.`service_request_id`) as fileIds')
						->selectRaw('(SELECT `hospitalName` FROM `hospital` WHERE `hospital`.`id`= `service_request`.`hospital_id`) as hospitalName')
						->selectRaw('(SELECT `firstName` FROM `patients` WHERE `patients`.`id`= `service_request`.`patient_id`) as patientName')
						->where(['sr_pickup.service_request_id' => $serviceRequestID])
						->get();
			}
		else if($id == '4')
			{
				$data = DB::table('sr_hotel_booking')
						->leftJoin('service_request', 'service_request.id', '=', 'sr_hotel_booking.service_request_id')
						->select('sr_hotel_booking.*','service_request.*')
						->selectRaw('(SELECT GROUP_CONCAT(`files`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_hotel_booking`.`service_request_id`) as files')
						->selectRaw('(SELECT GROUP_CONCAT(`id`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_hotel_booking`.`service_request_id`) as fileIds')
						->selectRaw('(SELECT `hospitalName` FROM `hospital` WHERE `hospital`.`id`= `service_request`.`hospital_id`) as hospitalName')
						->where(['sr_hotel_booking.service_request_id' => $serviceRequestID])
						->get();
			}
		else if($id == '5')
			{
				$data = DB::table('sr_visa_request')
						->leftJoin('service_request', 'service_request.id', '=', 'sr_visa_request.service_request_id')
						->select('sr_visa_request.*','service_request.*')
						->selectRaw('(SELECT GROUP_CONCAT(`files`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_visa_request`.`service_request_id`) as files')
						->selectRaw('(SELECT GROUP_CONCAT(`id`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_visa_request`.`service_request_id`) as fileIds')
						->selectRaw('(SELECT `hospitalName` FROM `hospital` WHERE `hospital`.`id`= `service_request`.`hospital_id`) as hospitalName')
						->selectRaw('(SELECT `name` FROM `doctors` WHERE `doctors`.`id`= `service_request`.`doctor_id`) as doctorName')
						->selectRaw('(SELECT `specialization` FROM `departments` WHERE `departments`.`id`= `sr_visa_request`.`department`) as departmentName')
						->where(['sr_visa_request.service_request_id' => $serviceRequestID])
						->get();
			}
		else if($id == '6')
			{
				$data = DB::table('sr_telemedicine')
						->leftJoin('service_request', 'service_request.id', '=', 'sr_telemedicine.service_request_id')
						->select('sr_telemedicine.*','service_request.*')
						->selectRaw('(SELECT GROUP_CONCAT(`files`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_telemedicine`.`service_request_id`) as files')
						->selectRaw('(SELECT GROUP_CONCAT(`id`) FROM `service_request_files` WHERE `service_request_files`.`service_request_id`= `sr_telemedicine`.`service_request_id`) as fileIds')
						->selectRaw('(SELECT `hospitalName` FROM `hospital` WHERE `hospital`.`id`= `sr_telemedicine`.`hospital`) as hospitalName')
						->selectRaw('(SELECT `specialization` FROM `departments` WHERE `departments`.`id`= `sr_telemedicine`.`speciality`) as specality')
						->selectRaw('(SELECT `specialization` FROM `departments` WHERE `departments`.`id`= `sr_telemedicine`.`speciality`) as departmentName')
						->selectRaw('(SELECT `name` FROM `doctors` WHERE `doctors`.`id`= `service_request`.`doctor_id`) as doctorName')
						->where(['sr_telemedicine.service_request_id' => $serviceRequestID])
						->get();
			}
		return $data;
	}
	
	public function addPatientMeta(Request $request){
		$model             = new Patientmeta();
		$model->patient_id =  $request->patient_id;
		$model->arrivalDt  =  date("Y-m-d H:i:s", strtotime($request->arrivalDateTime));
		$model->leavingDt  =  date("Y-m-d H:i:s", strtotime($request->leavingDateTime));
		$model->note       =  $request->note;
		$model->save();
		$data = array(
			'msg' =>'Patient Meta Added Successfully'
		);
		echo json_encode($data);
	}

	public function getDoctorsList(Request $request){

		$doctors = Doctors::where('hospital_id',$request->hospital_id)->where('GroupID',$request->GroupID)->where('teamID',$request->teamID)->orderBy('id', 'DESC')->get();
		
		$drop = "<option value=''>SELECT</option>";
		
		if(!empty($doctors)){
			foreach($doctors as $value){
				$drop .= "<option value='".$value->id."'>".$value->name."</option>";
			}
		}
		
		$data = array(
			'msg' =>'SUCCESS',
			'data'=>$drop
		);
		
		echo json_encode($data);
	}
	
	public function getDoctorsListForSmo(Request $request){
		if(isset($request->hospital_id) && !isset($request->specialist_id)){
			$doctors = Doctors::where('hospital_id',$request->hospital_id)->orderBy('id', 'DESC')->get();
		}else if(isset($request->hospital_id) && isset($request->specialist_id))
		{
			$doctors = Doctors::where('hospital_id',$request->hospital_id)->where('speciality',$request->specialist_id)->orderBy('id', 'DESC')->get();
		}
		
		
		$drop = "<option value=''>SELECT</option>";
		
		if(!empty($doctors)){
			foreach($doctors as $value){
				$drop .= "<option value='".$value->id."'>".$value->name."</option>";
			}
		}
		
		$data = array(
			'msg' =>'SUCCESS',
			'data'=>$drop
		);
		
		echo json_encode($data);
	}
	
	public function getPatientMetaInfo(Request $request){
		
		$Patientmeta = Patientmeta::where('patient_id',$request->patient_id)->orderBy('id', 'DESC')->get();
		
		$table = '<table class="table table-striped table-bordered">';
		$table.='<thead><tr><th>SN</th><th>Arrival Date</th><th>Leaving Date</th><th>Notes</th></tr></thead>';
		$table.='<tbody>';
		$i=1;
		foreach($Patientmeta as $value){	
			$table.='<tr><td>'.$i.'</td><td>'.$value->arrivalDt.'</td><td>'.$value->leavingDt.'</td><td>'.$value->note.'</td></tr>';
			$i++;
		}
		$table.='</tbody>';
		$table.='</table>';
		
		$data = array(
			'msg' =>'SUCCESS',
			'table'=>$table
		);
		
		echo json_encode($data);
	}
	
	public function getTeamList(Request $request){
		$GroupID = $request->GroupID; 
		
		$team = Category::where('parent',$GroupID)->get();
		
		$drop = "<option value=''>SELECT</option>";
		
		if(!empty($team)){
			foreach($team as $value){
				$drop .= "<option value='".$value->id."'>".$value->title."</option>";
			}
		}
		
		$data = array(
			'msg' =>'SUCCESS',
			'data'=>$drop
		);
		
		echo json_encode($data);
	} 
	
	public function getFecilityList(Request $request)
	{
		$teamID = $request->teamID; 
		$fecility = Hospital::where('hospitalTeam',$teamID)->get();
		
		$drop = "<option value=''>SELECT</option>";
		if(!empty($fecility)){
			foreach($fecility as $value){
				$drop .= "<option value='".$value->id."'>".$value->hospitalName."</option>";
			}
		}
		
		$data = array(
			'msg' =>'SUCCESS',
			'data'=>$drop
		);
		
		echo json_encode($data);
	}

	public function getCountry(Request $request){
		$countryList  = Country::Select('country')->groupBy('country')->get();
		$success['data'] = $countryList;
		return $this->sendResponse($success,'');
	}
	
	public function getState(Request $request){
		$country_id = $request->country_id;
		$stateList  = Country::Select('state')->where('country', $country_id)->groupBy('state')->get();
		$success['data'] = $stateList;
		return $this->sendResponse($success,'');
	}
	
	public function getCity(Request $request){
		$state_id = $request->state_id;
		$cityList = Country::where('state', $state_id)->get();
		return $this->sendResponse($cityList,'');
	}

	public function submitComments(Request $request)
	{
		$model = new ServiceRequestComments();
		$model->service_request_id = $request->request_id;
		$model->comment = $request->text;
		$model->repliedBy = Auth::user()->id;
		$this->sendCommentsMail($request->request_id,$model->comment);
		if($model->save())
		{
			$data = array(
				'msg' =>'SUCCESS'
			);
		}
		echo json_encode($data);
	}
	
	public function sendCommentsMail($sid , $comment)
	{
		$service_request = ServiceRequest::find($sid);
		if(Auth::user()->role_id == 3)
		{
			$userinfo = User::find($service_request->agent_id);
			$subject = 'New Comments from Agent '.$userinfo->name;
			$hospital_id = $service_request->hospital_id;
			$hinfo       = Hospital::find($hospital_id);
			$to = $hinfo->email;
			$from = 'admin@hplus.world';
		}
		else
		{
			$hinfo   = Hospital::find($service_request->hospital_id);
			$subject = 'New Comments from hospital '.$hinfo->name;
			$agent_id = $service_request->agent_id;
			$userinfo = User::find($agent_id);
			$to = $userinfo->email;
			$from = $hinfo->email;
		}
		$message ='<br>Please click <a href="'.url('home#'.$sid).'">here</a> to view comments.';
		$message.='<br><p>Best Regards</p>';
		$message.='<p>Hplus Support Team<p>';
		/* Mail::send('mail.htmlmail',['html' => $mailbody], function ($message) use($to,$subject) {
			$message->from($from, Auth::user()->name);
			$message->to($to)->cc('hplus.dhaka@gmail.com');
			$message->subject($subject);
		}); */
		Mail::send('mail.htmlmail',['html' => $message], function ($message) use($to,$from,$subject) {
			$message->from($from, Auth::user()->name);
			$message->to($to)->cc('hplus.dhaka@gmail.com');
			$message->subject($subject);
		});
	}

	public function getComments(Request $request)
	{
		$comments = ServiceRequestComments::where('service_request_id',$request->request_id)->orderBy('id', 'DESC')->get();
		
		$div = '';
		foreach($comments as $comment)
		{
			$name = User::find($comment->repliedBy);
			$div .='<div class="user-comment">';
			$div .='<div class="c-user-name">'.$name->name.':</div>';
			$div .='<div class="c-user-details"><p>'.$comment->comment.'</p></div>'; 
			$div .='</div>';
	
		}
	
		$data = array(
			'msg' =>'SUCCESS',
			'div' => $div,
		);

		echo json_encode($data);
	}
	
	public function deletefilefromServer(Request $request)
	{
		$file_id = $request->fileid;
		$del = ServiceRequestFiles::destroy($file_id);
		$data = array('msg' =>'SUCCESS');
		echo json_encode($data);
	}

	
	public function deleteTrafromServer($id)
	{
	    $sr_id = Travellers::find($id)->sr_id;
		$del = Travellers::destroy($id);
		$success['travellerInfo'] = Travellers::where('sr_id', $sr_id)->get();
		$success['msg']  = 'success';
		return $this->sendResponse($success,'');
	}
	
	public function addPayment(Request $request)
	{
		if(is_null(Auth::user()->hospital))
		{
			$UHID = $request->patient_id;
			$pinfo = Patientshospitalid::where('us_id' , $UHID)->first();
			//print_r($pinfo);exit;
			$hid = $pinfo->hospital_id;
		}
		else
		{
			$hid = Auth::user()->hospital;
		}
		$hospitalInformation = Hospital::find($hid);
		$comission = $hospitalInformation->comission;
		$date = $request->month;
		$patient_id = $request->patient_id;
		$amount = $request->amount;
		$type = $request->type;
		$month = Date('m',strtotime($date));
		$year = Date('Y',strtotime($date));
		
		$patient_info = DB::table('patients_us_id')
						->select('patients_us_id.*')
						->selectRaw('(SELECT CONCAT(patients.firstName," ",patients.lastName) FROM `patients` WHERE `patients`.`id`= `patients_us_id`.`user_id`) as name')
						->selectRaw('(SELECT `service_request`.`agent_id` FROM `service_request` WHERE `service_request`.`patient_id`= `patients_us_id`.`user_id` AND MONTH(`created_at`) = '.$month.' AND YEAR(`created_at`) = '.$year.' GROUP BY `patient_id`) as agentid')
						->where(['patients_us_id.us_id' =>$patient_id])
						->get();
		
		if($patient_info->isEmpty())
		{
			$data = array(
				'msg'=>'error'
			);
		}else
		{
			if(empty($patient_info[0]->agentid))
			{
				$data = array(
					'msg'=>'No service Request In this Month'
				);
			}else{
				$tr = '';
				$patient_info = $patient_info[0];
				$tr .= '<tr>	
							<td></td>
							<td>'.$hospitalInformation->hospitalName.'</td>
							<input type="hidden" name="agent_id[]" value="'.$patient_info->agentid.'">
							<td><input type="hidden" name="name[]" value="'.$patient_info->name.'">'.$patient_info->name.'</td>
							<td><input type="hidden" name="patient_id[]" value="'.$patient_id.'">'.$patient_id.'</td>
							<td><input type="hidden" name="type[]" value="'.$type.'">'.$type.'</td>
							<td><input type="hidden" name="amount[]" value="'.$amount.'">'.$amount.'</td>
							<td><input type="hidden" name="commission[]" value="'.$comission.'">'.$comission.'%</td>
							<td><input type="hidden" name="total[]" value="'.(($amount*$comission)/100).'">'.(($amount*$comission)/100).'</td>
							<input type="hidden" name="tax[]" value="'.$hospitalInformation->tax.'">
							<td><a href="javascript:void(0)" class="btnDelete"><i class="fa fa-minus-circle" style="font-size:20px;color:red" aria-hidden="true"></i></a></td>
					 </tr>';
					 
				$data = array(
					'msg'=>'success',
					'tr'=> $tr
				);
			}
			
		}
		
		
		echo json_encode($data);
	}
	
	public function getDoctorList(Request $request)
	{
		$department = $request->department; $drop = '';
		$hospital_id = $request->hospital_id;
		$doctor = Doctors::where('speciality',$department)->get();
		$drop = "<option value=''>SELECT</option>";
		foreach($doctor as $val)
		{
			$drop.='<option value="'.$val->id.'">'.$val->name.'</option>';
		}
		$data = array(
				'msg'=>'SUCCESS',
				'drop'=> $drop
			);
		echo json_encode($data);
	}
	
	public function getSelectedField(Request $request)
	{
		$sid = $request->service_id;
		$service_request = ServiceRequest::find($sid);
		$data = $this->getServiceWiseData($service_request->service_id, $sid);
		$department = Specialist::all();
		$Doctors = Doctors::where('speciality', $data[0]->speciality)->where('hospital_id',$service_request->hospital_id)->get();
		$departmentsDropdown = '<option value="">SELECT</option>';
		foreach($department as $value)
		{
			$selected = (($value->specialization == @$data[0]->speciality)?'selected="selected"':'');
			$departmentsDropdown.='<option value="'.$value->specialization.'" '.$selected.'>'.$value->specialization.'</option>';
		}
		
		$doctorDropdown = '<option value="">SELECT</option>';
		foreach($Doctors as $value)
		{
			$selected = (($value->id == $service_request->doctor_id)?'selected="selected"':'');
			$doctorDropdown.='<option value="'.$value->id.'" '.$selected.'>'.$value->name.'</option>';
		}
		
		$data = array(
				'msg'=>'SUCCESS',
				'doctorDropdown'=> $doctorDropdown,
				'departmentsDropdown'=> $departmentsDropdown,
			);
		echo json_encode($data);
		
	}
	
	public function getDepartment(Request $request){
	    if($request->has('hospital_id')){
	        $hospital_departments = Hospital::find($request->hospital_id)->departments;
	        $departments = Doctorsspecialization::whereIn('id',explode(',',$hospital_departments))->orderby('id','DESC')->paginate(10);
	        
	    }else{
	        $departments = Doctorsspecialization::where('status',1)->orderby('id','DESC')->paginate(10);
	    }
	    
		$success['departments'] = $departments;
		return $this->sendResponse($success,'');
	}
	
	public function destroyMedia($id){
		$fileDetails = ServiceRequestFiles::find($id);
		$service_id = $fileDetails->service_request_id;
		$del = $fileDetails->delete();
		$success['msg'] = 'success';
		$success['request_info'] = $this->getServiceWiseData('', $service_id);
		return $this->sendResponse($success,'');
	}

	public function getUsers(Request $request){
	    if($request->has('parent_id')){
			$users = User::where('parent_id', $request->parent_id)->get();
	    }else{
	        $users = User::all();
	    }
	    
		$success['users'] = $users;
		return $this->sendResponse($success,'');
	}

	public function storeDepartment(Request $request)
	{
		$model = new Doctorsspecialization();
		$this->validate($request, [        
			'name' => 'required', 
			'detail' => 'required',
		]);
		$model->specialization = $request->name;
		$model->detail = $request->detail;
		$model->save();
		$success['department'] = $request->all();
		return $this->sendResponse($success,'Department Added Successfully');
	}
}

?>