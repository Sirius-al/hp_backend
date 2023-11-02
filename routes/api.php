<?php
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\HospitalController;
use App\Http\Controllers\AccessTokenController;
use App\Http\Controllers\API\AjaxController;
use App\Http\Controllers\RefreshTokenController;
use App\Http\Controllers\API\ServiceComments;
  
Route::post('register', [RegisterController::class, 'register']);
Route::get('test/mail', [RegisterController::class, 'basic_email']);
Route::post('login', [AccessTokenController::class, 'issueToken']);
Route::post('refresh', [RefreshTokenController::class, 'issueRefreshToken']);
Route::get('departments', [AjaxController::class, 'getDepartment']);

Route::any('hospital/list', [HospitalController::class, 'list']);
Route::any('doctors/list', [DoctorController::class, 'list']);
Route::get('getHospitalandDoctor', [AjaxController::class, 'getHospitalandDoctor']);

Route::middleware('auth:api')->group( function () {
    Route::post('request/book/doctor', [ServiceController::class, 'submitDoctorBookingRequest']);
    
    Route::post('request/send/comment', [ServiceComments::class, 'sendComments']);
    Route::get('request/comments', [ServiceComments::class, 'comments']);
    Route::post('request/reply', [ServiceController::class, 'requestConfirm']);
    Route::post('request/store/uhid', [AjaxController::class, 'saveUsID']);

    Route::post('request/store/department', [AjaxController::class, 'storeDepartment']);
    
    Route::post('request/smo', [ServiceController::class, 'smoRequest']);
    Route::post('request/airport/pickup', [ServiceController::class, 'airport_pickup_request']);
    Route::post('request/book/hotel', [ServiceController::class, 'hotel_booking_request']);
    Route::post('request/visa', [ServiceController::class, 'visa_request']);
    Route::post('request/telemedicine', [ServiceController::class, 'telemedicine']);
    
    Route::post('request/travellers/store', [ServiceController::class, 'storeTravellers']);
    
    Route::get('request/list', [ServiceController::class, 'servicerequests_of_agent']);
    
    Route::post('patient/add', [PatientController::class, 'add']);
    
    Route::post('doctors/add', [DoctorController::class, 'add']);
   
    
    Route::post('hospital/add', [HospitalController::class, 'add']);
    
    Route::get('country', [AjaxController::class, 'getCountry']);
    Route::get('country/state', [AjaxController::class, 'getState']);
    Route::get('country/city', [AjaxController::class, 'getCity']);
    Route::get('patient/info', [AjaxController::class, 'getPatientsInfo']);
    Route::get('patient/list', [AjaxController::class, 'getPatientsList']);
    
    Route::get('/request/view', [AjaxController::class, 'getRequestsView']);
    Route::delete('/delete/media/{id}', [AjaxController::class, 'destroyMedia']);
    Route::delete('/delete/travellars/{id}', [AjaxController::class, 'deleteTrafromServer']);
    Route::get('/user/list/', [AjaxController::class, 'getUsers']);
    
});

