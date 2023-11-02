<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctors;
use DB; use Auth; use Mail;
use File;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Jobs\SendEmail;

class DoctorController extends BaseController
{
    public function __construct()
    {
        //$this->middleware('auth');
    }
	
	public function add(Request $request)
    {
        
            		
		$model = new Doctors();

		$this->validate($request, [        
			'name' => 'required|max:255', 
			'hospital_id' => 'required|max:255',
			'phone' => 'required|max:255',
			'email' => 'required|max:255',
			'speciality_id' => 'required|max:255',
			'designation' => 'required|max:255',
			'shortDescription' => 'required|max:255',
			'areaInterested' => 'required|max:255',
			'status' => 'required'
		]);
		
		
        
        if($request->has('pk')){
            $id = $request->pk;
            $request->request->remove('pk');
            Doctors::where('id',$id)->update($request->all());
        }else{
            $id = Doctors::create($request->all())->id;
        }
        
		if($id){
            $files=array();
            if($request->hasFile('image')){
            	    $files = $request->file('image');
            	    $name=$files->getClientOriginalName();
            		$destinationPath = public_path('doctors/');
            		$fileName = 'DCT-'.$id.'-'.time().'_'.$name; 
            		$files->move($destinationPath,$fileName);
            		Doctors::where(['id' => $id])->update(['image' => 'doctors/'.$fileName]);
            }
			$success['data'] = $request->all();
			return $this->sendResponse($success, 'Doctor Successfully added.');
		}
    }
    
    public function list(Request $request)
    {
        if($request->has('id')){
            $doctors = Doctors::select('doctors.*','hospital.hospitalName',\DB::raw("GROUP_CONCAT(`departments`.`specialization`) as `specialization`"))
                        ->join('hospital', 'hospital.id','=','doctors.hospital_id')
                        ->leftjoin("departments",\DB::raw("FIND_IN_SET(`departments`.`id`,`doctors`.`speciality_id`)"),">",\DB::raw("'0'"))
                        ->where('doctors.id',$request->id)
                        ->groupBy("doctors.id")
                        ->paginate(5);
        }else{
            $doctors = Doctors::select('doctors.*','hospital.hospitalName',\DB::raw("GROUP_CONCAT(`departments`.`specialization`) as `specialization`"))
                        ->join('hospital', 'hospital.id','=','doctors.hospital_id')
                        //->join('departments', 'departments.id','=','doctors.speciality_id')
                        ->leftjoin("departments",\DB::raw("FIND_IN_SET(`departments`.`id`,`doctors`.`speciality_id`)"),">",\DB::raw("'0'"))
                        ->paginate(5);
        }
        
        $success['data'] = $doctors;
		return $this->sendResponse($success, 'Doctor list.');
    }
	
}
