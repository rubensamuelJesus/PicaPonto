<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/datago/{usage}/{status}',['uses'=>"LogController@storeData"]);
Route::get('/datashow',"LogController@showData");

//Route::get('/phpmyadmin',"LogController@showData");

Route::get('dashboard', function () {
    return redirect('/usr/share/phpmyadmin');
});
