<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Jsonable;
use App\User;
use App\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Hash;
use DateTime;
class ResetPasswordControllerAPI extends Controller
{
    public function resetPassword(Request $user)
    {
    	if (DB::table('password_resets')->where('email','=',$user->email)->where('token','=',$user->token)->exists()){

	        $id = DB::table('users')
	        ->where('email', $user->email)->value('id');
	        
	        $userData = User::findOrFail($id);

	        $userData->password = Hash::make($user->password);
	        $userData->remember_token=str_random(60);
	        $userData->email_verified_at=new DateTime();
	        $userData->save();
	        /*$newUser = new User();
	        $newUser->password = Hash::make($user->password);
	        $newUser->remember_token=str_random(60);
	        $newUser->email_verified_at=new DateTime();
	       // $newUser->updated_at=new DateTime();

	        $newUser->save();
	        $userData->update($newUser->all());*/
	        return response()->json($userData,201);
    	}
        return response()->json(404);
    }
}
