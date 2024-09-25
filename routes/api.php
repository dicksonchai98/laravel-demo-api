<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PostsController;

use APP\Http\Middleware\TokenAuth;

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
Route::get('users',[UsersController::class,'index'])->middleware('token.auth');
// Route::middleware('token.auth')->get('users',[UsersController::class,'index']);
Route::get('users/{id}',[UsersController::class,'show'])->middleware('token.auth');
Route::put('users/{id}',[UsersController::class,'update'])->middleware('token.auth');
Route::delete('users/{id}',[UsersController::class,'destroy'])->middleware('token.auth');

Route::get('posts',[PostsController::class,'index']);
Route::get('posts/{id}',[PostsController::class,'show']);
Route::post('posts',[PostsController::class,'store'])->middleware('token.auth');
Route::put('posts/{id}',[PostsController::class,'update'])->middleware('token.auth');
Route::delete('posts/{id}',[PostsController::class,'destroy'])->middleware('token.auth');
Route::get('users/{user_id}/posts',[PostsController::class,'userPosts']);

Route::get('categories',[CategoriesController::class,'index']);
Route::get('categories/{id}/posts',[CategoriesController::class,'categoryPosts']);