<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Mail;

class RegisterController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->all();
        
        $input['role'] = 'User';

        if ($request->has('role') && $request->role == 'agent') {
            $input['parent_id'] = $request->parent_id;
            $input['role'] = 'Agent';
        }

        if ($request->has('role') && $request->role == 'hospital') {
            $input['parent_id'] = $request->hospital_id;
            $input['role'] = 'Hospital';
        }

        if ($request->has('role') && $request->role == 'admin') {
            $input['parent_id'] = 0;
            $input['role'] = 'Admin';
        }

        if ($request->has('role') && $request->role == 'user') {
            $input['parent_id'] = $request->parent_id;
            $input['role'] = 'User';
        }
        
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        //$success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }
    
    public function basic_email() {
    
      //echo asset('front/h-plus.svg');exit;
      $data = array('name'=>"Joy");
      Mail::send(['text'=>'mail'], $data, function($message) {
         $message->to('prokash93@gmail.com', 'Mail Test')->subject
            ('Laravel Basic Testing Mail');
         $message->from('support@hplus-bd.com','Hplus SUpport');
      });
      echo "Basic Email Sent. Check your inbox.";
   }
}
