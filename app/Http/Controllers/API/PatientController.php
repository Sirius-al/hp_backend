<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patients;
use DB; use Auth; use Mail;
use File;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Jobs\SendEmail;

class PatientController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function add(Request $request)
    {
		$model = new Patients();

		$this->validate($request, [        
			'firstName' => 'required|max:255', 
			'lastName' => 'required|max:255',
			'sex' => 'required|max:255',
			'dob' => 'required|max:255',
			'nationality' => 'required|max:255',
			'country' => 'required|max:255',
			'state' => 'required|max:255',
			'city' => 'required|max:255',
			'address' => 'required|max:255',
			'email' => 'required|email|unique:patients',
			'contact' => 'required|max:255',
			'passportNumber' => 'required|max:255',
			'nid' => 'required|max:255',
			'agent_id'=>'required|max:255',
		]);

		if(Patients::create($request->all())){
			$success['data'] = $request->all();
			return $this->sendResponse($success, 'Patient Successfully added.');
		}
    }
	
}
