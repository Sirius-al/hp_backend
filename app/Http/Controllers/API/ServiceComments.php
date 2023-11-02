<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\ServiceReComments;
use App\Models\Hospital;
use DB; use Auth; use Mail;
use Illuminate\Pagination\LengthAwarePaginator;
use File;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Jobs\SendEmail;

class ServiceComments extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	
	public function sendComments(Request $request){
	    $success = array();
	    if($request->has('service_request_id') && $request->has('from') && $request->has('to')){
	        $model = new ServiceReComments();
	        $model->service_request_id = $request->service_request_id;
	        $model->from_reply = $request->from;
	        $model->to_reply = $request->to;
	        $model->comment = $request->comment;
	        
            $files=array(); 
            $fileNameArray = array();
            
            if($files=$request->hasFile('files')){
            	$files = $request->file('files');
            	foreach($files as $file){
            		$name=$file->getClientOriginalName();
            		$destinationPath = public_path('services/service_comment');
            		$fileName = 'SR-'.$request->service_request_id.'-'.time().'_'.$name; 
            		$file->move($destinationPath,$fileName);
            		$fileNameArray[] = 'services/service_comment/'.$fileName;
            	}
            }
            
            $model->files = json_encode($fileNameArray);
            
            if($model->save()){
                $success['comments'] = ServiceReComments::where('service_request_id',$request->service_request_id)->orderby('id','ASC')->get();
            }
            
            return $this->sendResponse($success, 'Comment added SuccessFully');
	    }
	}
	
	public function comments(Request $request){
	    $success = array();
	    if($request->has('service_request_id')){
                $success['comments'] = ServiceReComments::where('service_request_id',$request->service_request_id)->orderby('id','ASC')->get();
        }
        return $this->sendResponse($success, 'Comments list');
	}
}
