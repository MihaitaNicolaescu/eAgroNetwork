<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DetailsController;

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


 Route::post('/addUser', [UserController::class, 'createUser']);

 Route::post('/logout', [UserController::class, 'logoutUser']);

 Route::post('/login', [UserController::class, 'loginUser']);

 Route::get('/fetchUserData', [UserController::class, 'fetchUserData']);

 Route::get('/fetchProfile', [UserController::class, 'fetchProfile']);

 Route::post('/updateUserData', [UserController::class, 'updateUserData']);

 Route::post('/updateUserProfilePhoto', [UserController::class, 'updateUserProfilePhoto']);

 Route::get('/user/search', [DetailsController::class, 'search']);
  
 Route::get('/admin/users', [DetailsController::class, 'pagination']);

 Route::get('/verifyData', [DetailsController::class, 'verifyData']);

 Route::post('/admin/delete', [DetailsController::class, 'deleteUser']);