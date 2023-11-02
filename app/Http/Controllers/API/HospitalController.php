<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hospital;
use DB; use Auth; use Mail;
use File;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Jobs\SendEmail;
use App\Models\Doctorsspecialization; 

class HospitalController extends BaseController
{
    public function __construct()
    {
       // $this->middleware('auth');list
    }
	
	public function add(Request $request)
    {
        
            		
		$model = new Hospital();

		$this->validate($request, [        
			'hospitalName' => 'required|max:255', 
			'hospitalAddress' => 'required|max:255',
			'email_for_a_t' => 'required|max:255',
			'email_for_other' => 'required|max:255',
			'departments' => 'required|max:255',
			'url' => 'required|max:255',
			'contact' => 'required|max:255',
			'status' => 'required'
		]);

        $emailsArray = explode(',', $request->email_for_a_t);
        $emailsArrayTwo = explode(',', $request->email_for_other);

        foreach ($emailsArray as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->sendError('Email is not valid', array());
            }
        }

        foreach ($emailsArrayTwo as $emailTwo) {
            if (!filter_var($emailTwo, FILTER_VALIDATE_EMAIL)) {
                return $this->sendError('Email is not valid', array());
            }
        }
        
        if($request->has('pk')){
            $id = $request->pk;
            $request->request->remove('pk');
            Hospital::where('id',$id)->update($request->all());
        }else{
            $id = Hospital::create($request->all())->id;
        }
        
		if($id){
            $files=array();
            if($request->hasFile('image')){
            	    $files = $request->file('image');
            	    $name=$files->getClientOriginalName();
            		$destinationPath = public_path('hospitals/');
            		$fileName = 'H-'.$id.'-'.time().'_'.$name; 
            		$files->move($destinationPath,$fileName);
            		Hospital::where(['id' => $id])->update(['image' => 'hospitals/'.$fileName]);
            }
			$success['data'] = $request->all();
			return $this->sendResponse($success, 'Hospital Successfully added.');
		}
    }
    
    public function list(Request $request)
    {
        if($request->has('id')){
            $Hospital = Hospital::find($request->id);
        }else{
            $Hospital = Hospital::orderby('id','DESC')->paginate(10);
        }
        
        $success['data'] = $Hospital;
        $success['departments'] = Doctorsspecialization::select('id','specialization')->get();
		return $this->sendResponse($success, 'Hospital list.');
    }
    
	
}
