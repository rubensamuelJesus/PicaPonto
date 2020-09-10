<?php

namespace App\Http\Controllers;

use Datetime;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Hash;

use Illuminate\Support\Facades\Auth;

class RoomControllerAPI extends Controller
{
    public function index(Request $request)
    {
       /*$roomList=DB::table('room')
        ->leftJoin('room_occupation','room.id','=','room_occupation.room_id')
        ->leftJoin('users','users.id','=','room_occupation.user_id')
        ->select('room.id as id','room.name as name','room.controller_name','users.name as user')
        ->get();
        return response()->json($roomList,200);*/
       
       $roomList=DB::table('room')
        ->leftJoin('room_occupation','room.id','=','room_occupation.room_id')
        ->select('room.id as id','room.name as name','room.node as node','room.controller_name',DB::raw('count(room_occupation.room_id) as users_entered'))
        ->groupBy('room.id')
        ->get();
        return response()->json($roomList,200);
    }

    public function roomHistory(Request $request,$room_id)
    {
       $roomHistory=DB::table('room_history')
        ->where('room_id',$room_id)
        ->leftJoin('users','users.id','=','room_history.user_id')
        ->select('room_history.room_id as room_id','room_history.user_id as user_id','users.name as name','room_history.user_id as user_id','room_history.entered_at','room_history.left_at','room_history.time_in_room')
        ->get();
        return response()->json($roomHistory,200);

    }
    public function updateRoom(Request $request,$room_id)
    {
        if ($request->has('name')&&$request->has('controller_name')&&$request->has('node')) {
            $result=DB::table('room')->where('id',$room_id)->update(['name'=>$request->name,'controller_name'=>$request->controller_name,'node'=>$request->node]);
            return response()->json($result,200);
        }
        return response()->json(400);

    }
    public function addRoom(Request $request)
    {
       $result=DB::table('room')->insert(['name'=>$request->name,'controller_name'=>$request->controller_name]);
        return response()->json($result,200);

    }

    public function roomUsers(Request $request,$room_id)
    {
       $roomUsers=DB::table('room_occupation')->where('room_id',$room_id)
        ->leftJoin('users','users.id','=','room_occupation.user_id')
        ->get();
        return response()->json($roomUsers,200);
    }

    public function getNodes()
    {
       $nodes=DB::table('room')->where('node','!=',null)->select('node as node','id as node_id')->get();
        return response()->json($nodes,200);
    }

    public function myHistory(Request $request)
    {
        $roomHistory=DB::table('room_history')
        ->where('user_id',auth()->user()->id)
        ->get();
        return response()->json($roomHistory,200);
    }
}
