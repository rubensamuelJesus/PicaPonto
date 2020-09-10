<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\View;

use Illuminate\Database\Eloquent\Model;

use DB;

class LogController extends Controller
{
    public function showData (){
		$results = DB::select('select * from rpi'); //query to fetch all the records in an array
       return View::make('showdata',['data'=>$results]);   //passing this array (results) with a key (data)
	}
	public function storeData ($usage,$status){
		/*insert into table cpu */
		DB::table('cpu')->insert([ 'date' => DB::raw('now()') , 'time'=> DB::raw('now()') , 'usage'=>$usage , 'status'=>$status]);
		return response()->json(200); //return 200 response when done.
	}
}

