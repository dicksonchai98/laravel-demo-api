<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

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


Route::post('/register',[UsersController::class,'register']);
Route::post('login',[UsersController::class,'login']);
Route::get('users',[UsersController::class,'index']);
Route::get('users/{id}',[UsersController::class,'show']);
Route::put('users/{id}','UsersController@update');
Route::delete('users/{id','UsersController@destroy');