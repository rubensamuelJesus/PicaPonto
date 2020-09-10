<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'LoginControllerAPI@login')->name('login'); 
Route::middleware('auth:api')->post('logout', 'LoginControllerAPI@logout');
Route::middleware('auth:api')->get('users/me', 'UserControllerAPI@myProfile');
Route::middleware('auth:api')->put('users/{id}', 'UserControllerAPI@update');
	Route::post('resetPassword', 'ResetPasswordControllerAPI@resetPassword')->name('resetPassword');

Route::patch('updateAccessId/{id}', 'AccessControllerAPI@updateAccessId'); 
Route::post('updateFingerPrintAndRFID/{id}', 'AccessControllerAPI@updateFingerPrintAndRFID'); 

Route::post('check_rfid/{id}', 'AccessControllerAPI@check_rfid');

Route::post('check_finger/{id}', 'AccessControllerAPI@check_finger'); 

Route::post('createPermission', 'AccessControllerAPI@createPermission'); 
Route::get('accessPermissions', 'AccessControllerAPI@accessPermissions');
Route::get('allUsers', 'AccessControllerAPI@allUsers');




Route::get('checkAllFingerID', 'AccessControllerAPI@checkAllFingerID');


Route::get('accessHistory', 'AccessControllerAPI@accessHistory');




Route::delete('deletePermission/{id}', 'AccessControllerAPI@deletePermission');

Route::post('checkSensor/{id}', 'SensorControllerAPI@checkSensorValue');
Route::post('updateSensor/{id}', 'SensorControllerAPI@updateSensorValue');


Route::get('checkOccupation/{id}', 'AccessControllerAPI@roomOccupation');

Route::get('checkOPermissionToShow', 'AccessControllerAPI@checkOPermissionToShow');

Route::group(['middleware' => ['auth:api', 'verified']], function () {
	
	Route::get('users', 'UserControllerAPI@index'); 

	Route::post('createUser', 'UserControllerAPI@createUser');
	Route::get('rooms', 'RoomControllerAPI@index'); 
	Route::get('test', 'SensorControllerAPI@test');
	Route::get('mySensors/{type}', 'SensorControllerAPI@mySensors');
	Route::get('myActuators/{type}', 'SensorControllerAPI@myActuators');
	
	Route::get('sensorHistory/{id}', 'SensorControllerAPI@sensorHistory');

	Route::get('myHistory', 'RoomControllerAPI@myHistory');
	Route::get('getTimeWorking', 'UserControllerAPI@getTimeWorking');

	Route::get('getMyTimeWorking', 'UserControllerAPI@getMyTimeWorking');

	Route::get('getAllTimeWorking/{id}', 'UserControllerAPI@getAllTimeWorkingWhitID');
	Route::get('getAllTimeWorking', 'UserControllerAPI@getAllTimeWorking');
});
Route::patch('updateRoom/{room_id}', 'RoomControllerAPI@updateRoom');
Route::post('addRoom', 'RoomControllerAPI@addRoom');
Route::get('roomHistory/{room_id}', 'RoomControllerAPI@roomHistory');
Route::get('roomUsers/{room_id}', 'RoomControllerAPI@roomUsers');
Route::get('checkEntrance/{user_id}', 'AccessControllerAPI@checkEntrance');

Route::get('getNodes', 'RoomControllerAPI@getNodes');


