<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;

use App\Http\Resources\Access as AccessResource;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use App\Access;
use App\User;
use Hash;
use Datetime;

use App\Events\AccessEvent;

use Illuminate\Support\Facades\Auth;

class AccessControllerAPI extends Controller
{

    /*public function checkEntrance($user_id)
    {
        $roomOccupation=DB::table('room_occupation')->where('user_id',$user_id)->first();
        if($roomOccupation){
            //user esta na room,criando saida
           // $left_at = new Datetime();
            $entered_at = new Carbon($roomOccupation->entered_at);
            $left_at = Carbon::now();
            //$difference = Carbon::parse($entered_at->diff($left_at)->format('%Y-%M-%D %H:%I:%S'));
            $difference = $entered_at->diff($left_at);

            $time = sprintf(
                '%d Hours %02d Mins %02d Sec ',
                ($difference->d * 24) + $difference->h,
                $difference->i,
                $difference->s
            );
            DB::table('room_history')->insert([
                        ['room_id' => $roomOccupation->room_id,'user_id' => $user_id,'entered_at' => $roomOccupation->entered_at,'left_at' => new Datetime(),'time_in_room' => $time],
                    ]);
            DB::table('room_occupation')->where('user_id',$user_id)->delete();
           return response()->json([$request->rfid_id,'msg'=>'ACCESS DENIED'], 401);
        }else{
            //user nao está na room

        }
    }*/

    public function check_rfid(Request $request,$id){

        $http = new \GuzzleHttp\Client;
        if($id != 1){
            $response = $http->post('http://178.62.108.89:8080/check_rfid', [
                'form_params' => [
                    'room' => $id,'noPremission' => true, 'scope' => ''
                ],
                'exceptions' => false,
            ]);
            return response()->json("false", 401); 
        }
        
        
        $access_known=Access::where('rfid_id', '=', $request->rfid_id)->first();
        
        if ($access_known && $access_known->user_id) {//validate rfid_id
            $user_id=Access::where('rfid_id', '=', $request->rfid_id)->select('user_id')->first();
            $user = User::findOrFail($user_id)->first();
            
            $roomOccupation=DB::table('room_occupation')->where('room_id','=',$id)->where('user_id',$user->id)->first();
           // dd($roomOccupation);
            if ($roomOccupation) {
                //User leaving room
                DB::table('room_occupation')->where('room_id','=',$id)->where('user_id','=',$roomOccupation->user_id)->delete();
                $entered_at = new Carbon($roomOccupation->entered_at);
                $left_at = Carbon::now();
                //$difference = Carbon::parse($entered_at->diff($left_at)->format('%Y-%M-%D %H:%I:%S'));
                $difference = $entered_at->diff($left_at);

                $time = sprintf(
                    '%d Hours %02d Mins %02d Sec ',
                    ($difference->d * 24) + $difference->h,
                    $difference->i,
                    $difference->s
                );
                DB::table('room_history')->insert([
                            ['room_id' => $roomOccupation->room_id,'user_id' =>$roomOccupation->user_id,'entered_at' => $roomOccupation->entered_at,'left_at' => $left_at,'time_in_room' => $time],
                        ]);

###########################3 #ENVIO HISTORICO PARA A BLOCKCHAIN vvvvvvvvvv
                        $response = $http->post('http://178.62.108.89:5001/access/register', [
                        'form_params' => [
                            'room_id' => $roomOccupation->room_id,'user_id' =>$roomOccupation->user_id,'entered_at' => $roomOccupation->entered_at,'left_at' => $left_at->toDateTimeString(),'time_in_room' => $time, 'scope' => ''
                        ],
                        'exceptions' => false,
                        ]);
###########################3 #ENVIO HISTORICO PARA A BLOCKCHAIN^^^^^^^^

                $response = $http->post('http://178.62.108.89:8080/check_rfid', [
                    'form_params' => [
                        'name' => $user->name,'room' => $id,'leaving' => true, 'scope' => ''
                    ],
                    'exceptions' => false,
                ]);
                $errorCode= $response->getStatusCode();
                /*DB::table('access_history')->insert([
                    ['rfid_id' => $request->rfid_id,'room_id' => $id,'state' => 'Left','created_at' => new Datetime()],
                ]);*/
                return response()->json("true", 200);
            }else{

                DB::table('room_occupation')->insert(['user_id'=>$user->id,'room_id' => $id,'entered_at'=>new Datetime()]);
                $response = $http->post('http://178.62.108.89:8080/check_rfid', [
                    'form_params' => [
                        'name' => $user->name,'room' => $id,'entering' => true, 'scope' => ''
                    ],
                    'exceptions' => false,
                ]);
                $errorCode= $response->getStatusCode();
                /*DB::table('access_history')->insert([
                    ['rfid_id' => $request->rfid_id,'room_id' => $id,'state' => 'Left','created_at' => new Datetime()],
                ]);*/
                return response()->json("true", 200);
            }
        }else{
            //unauthorized rfid detected
           $response = $http->post('http://178.62.108.89:8080/check_rfid', [
            'form_params' => [
                'rfid_id' => $request->rfid_id,'room' => $id, 'scope' => ''
            ],
            'exceptions' => false,
            ]);
            $errorCode= $response->getStatusCode(); 
            DB::table('access_history')->insert([
                ['rfid_id' => $request->rfid_id,'room_id' => $id,'state' => 'Unknown ID','created_at' => new Datetime()],
            ]); 
            if (!$access_known ) {
                DB::table('access_id')->insert([
                    ['rfid_id' => $request->rfid_id,'finger_id'=>null,'user_id'=>null],
                ]);
            }
            return response()->json("false", 401); 
        }      
    }
    
    /*public function check_rfid(Request $request,$id)
    {

        $http = new \GuzzleHttp\Client;
   			//     return response()->json($request, 401);
        $access_known=Access::where('rfid_id', '=', $request->rfid_id)->first();
        if ($access_known && $access_known->user_id) {//validate rfid_id
            $user_id=Access::where('rfid_id', '=', $request->rfid_id)->select('user_id')->first();
            $roomOccupation=DB::table('room_occupation')->where('room_id','=',$id)->first();
            $user = User::findOrFail($user_id)->first();
            if($roomOccupation){
                //$user_id=Access::where('rfid_id', '=', $request->rfid_id)->select('user_id')->first();
                $roomUser = User::findOrFail($roomOccupation->user_id);
                //dd($roomOccupation);
                if ($roomUser->id==$user->id) {
                    //User currently in room 
                    DB::table('room_occupation')->where('room_id','=',$id)->where('user_id','=',$roomUser->id)->delete();
                    $response = $http->post('http://178.62.108.89:8080/check_rfid', [
                        'form_params' => [
                            'name' => $user->name,'room' => $id,'leaving' => true, 'scope' => ''
                        ],
                        'exceptions' => false,
                    ]);
                    $errorCode= $response->getStatusCode();
                    DB::table('access_history')->insert([
                        ['rfid_id' => $request->rfid_id,'room_id' => $id,'state' => 'Left','created_at' => new Datetime()],
                    ]);
                    return response()->json("true", 200);
                }else{
                    //Room currently occupied.
                    $response = $http->post('http://178.62.108.89:8080/check_rfid', [
                        'form_params' => [
                            'name' => $user->name,'room' => $id,'occupied' => true, 'scope' => ''
                        ],
                        'exceptions' => false,
                    ]);
                    $errorCode= $response->getStatusCode();
                    DB::table('access_history')->insert([
                        ['rfid_id' => $request->rfid_id,'room_id' => $id,'state' => 'Room occupied','created_at' => new Datetime()],
                    ]);
                    return response()->json("false", 200);
                }
            }else{
                //room available , filling room_occupations table
                DB::table('room_occupation')->insert(['user_id'=>$user->id,'room_id' => $id]);
                $response = $http->post('http://178.62.108.89:8080/check_rfid', [
                    'form_params' => [
                        'name' => $user->name,'room' => $id,'entering' => true, 'scope' => ''
                    ],
                    'exceptions' => false,
                ]);
                $errorCode= $response->getStatusCode();
                DB::table('access_history')->insert([
                    ['rfid_id' => $request->rfid_id,'room_id' => $id,'state' => 'Entered','created_at' => new Datetime()],
                ]);
                return response()->json("true", 200);
            }
        }else{
            //unauthorized rfid detected
           $response = $http->post('http://178.62.108.89:8080/check_rfid', [
            'form_params' => [
                'rfid_id' => $request->rfid_id,'room' => $id, 'scope' => ''
            ],
            'exceptions' => false,
            ]);
            $errorCode= $response->getStatusCode(); 
            DB::table('access_history')->insert([
                ['rfid_id' => $request->rfid_id,'room_id' => $id,'state' => 'Unknown ID','created_at' => new Datetime()],
            ]); 
            if (!$access_known ) {
                DB::table('access_id')->insert([
                    ['rfid_id' => $request->rfid_id,'finger_id'=>null,'user_id'=>null],
                ]);
            }
            return response()->json("false", 401); 
        }
    }*/


    public function check_finger(Request $request,$id){
        $http = new \GuzzleHttp\Client;
        if($id != 2)
        {
            $response = $http->post('http://178.62.108.89:8080/check_finger', [
                'form_params' => [
                    'room' => $id,'noPremission' => true, 'scope' => ''
                ],
                'exceptions' => false,
            ]);
            return response()->json("false", 401);  
        }

        
        $access_known=Access::where('finger_id', '=', $request->finger_id)->first();
        
        if ($access_known && $access_known->user_id) {//validate finger_id
            $user_id=Access::where('finger_id', '=', $request->finger_id)->select('user_id')->first();
            $user = User::findOrFail($user_id)->first();
            
            $roomOccupation=DB::table('room_occupation')->where('room_id','=',$id)->where('user_id',$user->id)->first();
           // dd($roomOccupation);
            if ($roomOccupation) {
                //User leaving room
                DB::table('room_occupation')->where('room_id','=',$id)->where('user_id','=',$roomOccupation->user_id)->delete();
                $entered_at = new Carbon($roomOccupation->entered_at);
                $left_at = Carbon::now();
                //$difference = Carbon::parse($entered_at->diff($left_at)->format('%Y-%M-%D %H:%I:%S'));
                $difference = $entered_at->diff($left_at);

                $time = sprintf(
                    '%d Hours %02d Mins %02d Sec ',
                    ($difference->d * 24) + $difference->h,
                    $difference->i,
                    $difference->s
                );
                DB::table('room_history')->insert([
                            ['room_id' => $roomOccupation->room_id,'user_id' =>$roomOccupation->user_id,'entered_at' => $roomOccupation->entered_at,'left_at' =>$left_at,'time_in_room' => $time],
                        ]);

###########################3 #ENVIO HISTORICO PARA A BLOCKCHAIN vvvvvvvvvv
                        $response = $http->post('http://178.62.108.89:5001/access/register', [
                        'form_params' => [
                            'room_id' => $roomOccupation->room_id,'user_id' =>$roomOccupation->user_id,'entered_at' => $roomOccupation->entered_at,'left_at' => $left_at->toDateTimeString(),'time_in_room' => $time, 'scope' => ''
                        ],
                        'exceptions' => false,
                        ]);
###########################3 #ENVIO HISTORICO PARA A BLOCKCHAIN^^^^^^^^

                $response = $http->post('http://178.62.108.89:8080/check_finger', [
                    'form_params' => [
                        'name' => $user->name,'room' => $id,'leaving' => true, 'scope' => ''
                    ],
                    'exceptions' => false,
                ]);
                $errorCode= $response->getStatusCode();
                /*DB::table('access_history')->insert([
                    ['finger_id' => $request->finger_id,'room_id' => $id,'state' => 'Left','created_at' => new Datetime()],
                ]);*/
                //return response()->json("true", 200);
                return response()->json("User: [".$user->name."] leaved Room: [".$id."]", 200);
            }else{

                DB::table('room_occupation')->insert(['user_id'=>$user->id,'room_id' => $id,'entered_at'=>new Datetime()]);
                $response = $http->post('http://178.62.108.89:8080/check_finger', [
                    'form_params' => [
                        'name' => $user->name,'room' => $id,'entering' => true, 'scope' => ''
                    ],
                    'exceptions' => false,
                ]);
                $errorCode= $response->getStatusCode();
                /*DB::table('access_history')->insert([
                    ['finger_id' => $request->finger_id,'room_id' => $id,'state' => 'Left','created_at' => new Datetime()],
                ]);*/
                //return response()->json("true", 200);
                 return response()->json("User: [".$user->name."] entered Room: [".$id."]", 200);
            }
        }else{
            //unauthorized rfid detected
           $response = $http->post('http://178.62.108.89:8080/check_finger', [
            'form_params' => [
                'finger_id' => $request->finger_id,'room' => $id, 'scope' => ''
            ],
            'exceptions' => false,
            ]);
            $errorCode= $response->getStatusCode(); 
            DB::table('access_history')->insert([
                ['finger_id' => $request->finger_id,'room_id' => $id,'state' => 'Unknown ID','created_at' => new Datetime()],
            ]); 
            if (!$access_known ) {
                DB::table('access_id')->insert([
                    ['finger_id' => $request->finger_id,'rfid_id'=>null,'user_id'=>null],
                ]);
            }
            return response()->json("ID Unknown", 200);
            

        }      
    }

    /*public function check_finger(Request $request,$id)
    {

        $http = new \GuzzleHttp\Client;
            //     return response()->json($request, 401);
        $access_known=Access::where('finger_id', '=', $request->finger_id)->first();
        if ($access_known && $access_known->user_id) {//validate finger_id
            $user_id=Access::where('finger_id', '=', $request->finger_id)->select('user_id')->first();
            $roomOccupation=DB::table('room_occupation')->where('room_id','=',$id)->first();
            $user = User::findOrFail($user_id)->first();
            if($roomOccupation){
                //$user_id=Access::where('finger_id', '=', $request->finger_id)->select('user_id')->first();
                $roomUser = User::findOrFail($roomOccupation->user_id);
                //dd($roomOccupation);
                if ($roomUser->id==$user->id) {
                    //User currently in room 
                    DB::table('room_occupation')->where('room_id','=',$id)->where('user_id','=',$roomUser->id)->delete();
                    $response = $http->post('http://178.62.108.89:8080/check_finger', [
                        'form_params' => [
                            'name' => $user->name,'room' => $id,'leaving' => true, 'scope' => ''
                        ],
                        'exceptions' => false,
                    ]);
                    $errorCode= $response->getStatusCode();
                    DB::table('access_history')->insert([
                        ['finger_id' => $request->finger_id,'room_id' => $id,'state' => 'Left','created_at' => new Datetime()],
                    ]);
                    //return response()->json("true", 200);
                    return response()->json("User: [".$user->name."] leaved Room: [".$id."]", 200);
                }else{
                    //Room currently occupied.
                    $response = $http->post('http://178.62.108.89:8080/check_finger', [
                        'form_params' => [
                            'name' => $user->name,'room' => $id,'occupied' => true, 'scope' => ''
                        ],
                        'exceptions' => false,
                    ]);
                    $errorCode= $response->getStatusCode();
                    DB::table('access_history')->insert([
                        ['finger_id' => $request->finger_id,'room_id' => $id,'state' => 'Room occupied','created_at' => new Datetime()],
                    ]);
                    //return response()->json("false", 200);
                    return response()->json("User: [".$user->name."] Room is occupied: [".$id."]", 200);
                }
            }else{
                //room available , filling room_occupations table
                DB::table('room_occupation')->insert(['user_id'=>$user->id,'room_id' => $id]);
                $response = $http->post('http://178.62.108.89:8080/check_finger', [
                    'form_params' => [
                        'name' => $user->name,'room' => $id,'entering' => true, 'scope' => ''
                    ],
                    'exceptions' => false,
                ]);
                $errorCode= $response->getStatusCode();
                DB::table('access_history')->insert([
                    ['finger_id' => $request->finger_id,'room_id' => $id,'state' => 'Entered','created_at' => new Datetime()],
                ]);
                //return response()->json("true", 200);
                return response()->json("User: [".$user->name."] entered Room: [".$id."]", 200);
            }
        }else{
            //unauthorized rfid detected
           $response = $http->post('http://178.62.108.89:8080/check_finger', [
            'form_params' => [
                'finger_id' => $request->finger_id,'room' => $id, 'scope' => ''
            ],
            'exceptions' => false,
            ]);
            $errorCode= $response->getStatusCode(); 
            DB::table('access_history')->insert([
                ['finger_id' => $request->finger_id,'room_id' => $id,'state' => 'Unknown ID','created_at' => new Datetime()],
            ]); 
            if (!$access_known ) {
                DB::table('access_id')->insert([
                    ['finger_id' => $request->finger_id,'rfid_id'=>null,'user_id'=>null],
                ]);
            }
            //return response()->json("false", 401); 
            //['rfid_id' => $request->rfid_id,'finger_id'=>null,'user_id'=>null],
            return response()->json("ID Unknown", 200);
        }
    }*/
    

    public function checkOccupation(Request $request,$id)
    {
        if(DB::table('room_occupation')->where('room_id','=',$id)->get()){
            return response()->json(true, 200);
        }
        return response()->json(false, 200);
    }

    public function checkAllFingerID(Request $request)
    {
        $allFingers = array();
        $num = 0;
        $allFingerID = DB::table('access_id')
        ->select('finger_id')
        ->where('finger_id', '!=','')
        ->orderBy('finger_id', 'asc')
        ->get();

        foreach ($allFingerID as $finger) {
            $allFingers[] = $finger->finger_id;
            $num++;
        }
        return response()->json($allFingers, 200);
    }




    public function checkOPermissionToShow(Request $request,$id)
    {
        $user = DB::table('users')->where('id','=',(string)Auth::id())->select('*')->first();
        if($user){
            return response()->json(true, 200);
        }
        return response()->json(false, 200);
    }



    public function accessPermissions(Request $request)
    {
        $accessPermissions=DB::table('access_id')
        ->leftJoin('users','user_id','=','users.id')
        ->select('access_id.id','access_id.rfid_id','access_id.finger_id','users.id as user_id','users.name')
        ->get();
        return response()->json($accessPermissions, 200);

    }

    public function allUsers(Request $request)
    {
        $allUsers=DB::table('users')
        ->get();
        return response()->json($allUsers, 200);

    }

    public function accessHistory(Request $request)
    {
        $accessHistory=DB::table('access_history')->get();
        return response()->json($accessHistory, 200);
    }

    
    public function deleteAccess(Request $request,$id)
    {
        //DB::table('access_history')->where('id', '=',$id)->delete();
        return response()->json(DB::table('access_history')->where('id', '=',$id)->delete(), 204);
    }
    
    public function createPermission(Request $request)
    {
       // return response()->json($request->user_id, 200);

        $user= User::findOrFail($request->user_id);
        return response()->json(DB::table('access_id')->insert(['user_id'=>$user->id,'rfid_id' => $request->rfid_id,'finger_id'=> $request->finger_id]), 200);
    }
    
    public function deletePermission(Request $request,$id)
    {
        $result = DB::table('access_id')->where('id', '=',$id)->delete();
        return response()->json($result, 202);
    }
    
    
    public function updateAccessId(Request $request,$id)
    {
     
        //return response()->json($id, 200);
        if ($request->rfid_id) {
            $result=DB::table('access_id')->where('id', '=', $id)->update(['rfid_id' => $request->rfid_id]);
        }
        if ($request->finger_id) {
            $result=DB::table('access_id')->where('id', '=', $id)->update(['finger_id' => $request->finger_id]);
        }
        return response()->json($result, 200);
    }

    public function updateFingerPrintAndRFID(Request $request,$id)
    {
        $result=DB::table('access_id')->where('id', '=', $id)->update(['user_id' => $request->idUserEscolhido]);
        return response()->json($result, 200);
    }
    


}/*
DB::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);*/