<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;
/*
use App\Http\Resources\Sensor as SensorResource;*/
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
/*
use App\Sensor;*/
use App\User;
use Hash;
use Datetime;

use Illuminate\Support\Facades\Auth;

/*use Lzq\Mqtt\SamMessage;
use Lzq\Mqtt\SamConnection;
*/
use Workerman\Worker;

class SensorControllerAPI extends Controller
{
     
    
    public function mySensors($type)
    {
        $type=\Str::lower($type);
    	$room=DB::table('room_occupation')->where('user_id','=',(string)Auth::id())->select('room_id')->first();
        //dd($room);
        //return response()->json($room[0]['room_id'], 200);
    	if ($room) {
	        $mySensors=DB::table('sensors')->where('room_id','=',$room->room_id)->where('type','=',$type)->get();
        	return response()->json($mySensors, 200);
    	}
    	return response()->json('Access a room to see sensors',404);

        /*$mySensors=DB::table('user_sensors')
        ->join('sensors','user_sensors.sensor_id','=','sensors.id')
            ->where('user_sensors.user_id','=',$id)
        ->select('user_sensors.sensor_id','sensors.name','sensors.value')
        ->get();
        return response()->json($mySensors, 200);*/

    }

    public function myActuators($type)
    {
        $type=\Str::lower($type);
        $room=DB::table('room_occupation')->where('user_id','=',(string)Auth::id())->select('room_id')->first();
        //dd($room);
        //return response()->json($room[0]['room_id'], 200);
        if ($room) {
            $myActuators=DB::table('sensors')->where('room_id','=',$room->room_id)->where('type','=',$type)->get();
            return response()->json($myActuators, 200);
        }
        return response()->json('Access a room to see sensors',404);

        /*$mySensors=DB::table('user_sensors')
        ->join('sensors','user_sensors.sensor_id','=','sensors.id')
            ->where('user_sensors.user_id','=',$id)
        ->select('user_sensors.sensor_id','sensors.name','sensors.value')
        ->get();
        return response()->json($mySensors, 200);*/

    }
    
    public function deletePermission(Request $request,$id)
    {
        $result = DB::table('Sensor_id')->where('id', '=',$id)->delete();
        return response()->json($result, 202); 
    }
    
    public function checkSensorValue(Request $request,$id)
	{
        $sensor = DB::table('sensors')->where('room_id', '=',$id)->where('name','=',$request->name)->get();
          
		return response()->json($sensor[0]->value, 200); 
    }

    public function updateSensorValue(Request $request,$id)
    {

        $http = new \GuzzleHttp\Client;
        if ($request->has('state')) {
            $result=DB::table('sensors')->where('room_id', '=',$id)->where('name', '=',$request->name)->update(['value'=> $request->value,'state'=> $request->state]);

            $sensor = DB::table('sensors')->where('room_id', '=',$id)->where('name','=',$request->name)->get();
            
            $insertSensorHistory=DB::table('sensors_history')->insert([
                ['sensor_id' => $sensor[0]->id,'value' => $request->value,'created_at' => new Datetime()],
            ]);
            if ($request->state=="Danger") {
            	//unauthorized rfid detected
	           $response = $http->post('http://178.62.108.89:8080/check_danger', [
	            'form_params' => [
	                'danger' => true,'room'=>$sensor[0]->room_id, 'scope' => ''
	            ],
	            'exceptions' => false,
	            ]);
	            $errorCode= $response->getStatusCode(); 
	            //dd($errorCode);
            }


        }else{
            $result=DB::table('sensors')->where('room_id', '=',$id)->where('name', '=',$request->name)->update(['value'=> $request->value]);

            $sensor = DB::table('sensors')->where('room_id', '=',$id)->where('name','=',$request->name)->get();
            
            $insertSensorHistory=DB::table('sensors_history')->insert([
                ['sensor_id' => $sensor[0]->id,'value' => $request->value,'created_at' => new Datetime()],
            ]);
        }

        //$controller_sensors = DB::table('sensors')->where('controller_id', '=',$id)->get();
        //dd($controller_sensors)
        //string $send=$request->value;
        
        return response()->json($result, 200);
    }
    
    
    public function updateRfid(Request $request,$id)
    {
     
        //return response()->json($id, 200);
        $result=DB::table('sensors')->where('id', '=', $id)->update(['rfid_id' => $request->rfid_id]);
        return response()->json($result, 200);
    }
    
    public function test()
    {
     
        return response()->json(Auth::id(), 200);
    }

    public function sensorHistory(Request $request,$id)
    {

        //return response()->json($accessHistoryTemp, 200);
       // $room=DB::table('room_occupation')->where('user_id','=',(string)Auth::id())->select('room_id')->first();
        //if ($room) {
           // $mySensors=DB::table('sensors')->where('room_id','=',$room->room_id)->where('type',$type)->get();
          //  $idArray=array();
           // foreach ($mySensors as $sensor) {
			//	array_push($idArray,$sensor->id);
			//}
         	//dd($idArray);
            $mySensorsHistory=DB::table('sensors_history')->where('sensor_id',$id)->orderBy('sensor_id')->get();
         	//dd($mySensorsHistory);
            return response()->json($mySensorsHistory, 200);
       // }
    }

}/*
DB::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);*/
