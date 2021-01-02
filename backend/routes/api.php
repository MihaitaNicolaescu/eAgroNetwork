<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DetailsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostVoteController;
use App\Http\Controllers\AplicationController;


Route::post('/addUser', [UserController::class, 'createUser']); // inregistrarea unui user
Route::post('/logout', [UserController::class, 'logoutUser']); // deconectarea userului actual
Route::post('/login', [UserController::class, 'loginUser']); // conectarea unui user
Route::get('/fetchUserData', [UserController::class, 'fetchUserData']); // preluarea datelor unui user
Route::get('/fetchProfile', [UserController::class, 'fetchProfile']);
Route::post('/cancelProducer', [UserController::class, 'cancelProducer']); // anuleaza gradul unui producator


Route::post('/updateUserData', [UserController::class, 'updateUserData']);

Route::post('/updateUserProfilePhoto', [UserController::class, 'updateUserProfilePhoto']);

Route::get('/user/search', [DetailsController::class, 'search']);

Route::get('/admin/users', [DetailsController::class, 'pagination']);

Route::get('/verifyData', [DetailsController::class, 'verifyData']);

Route::post('/admin/delete', [DetailsController::class, 'deleteUser']);

Route::post('/addPost', [PostController::class, 'addPost']);

Route::get('/fetchUserPosts', [PostController::class, 'fetchUserPosts']); // returneaza postarile de pe profilul unui user

Route::get('fetchPostsVoted', [PostVoteController::class, 'fetchUserPostsVoted']);

Route::get('/vote', [PostController::class, 'postVote']);

Route::get('/modifyVote',[PostVoteController::class, 'modifyVote']);

Route::post('/fallowUser', [UserController::class, 'addFallower']);
Route::post('/cancelFallowUser', [UserController::class, 'deleteFallower']);

Route::get('/fetchFallowList', [UserController::class, 'fetchFallowList']);
Route::get('/fetchFallowPosts', [PostController::class, 'fetchFallowPosts']);
Route::get('/getFallow', [UserController::class, 'getFallow']);

Route::post('/addApplication', [AplicationController::class, 'addApplication']);
Route::post('/addApplicationDocs', [AplicationController::class, 'uploadForm']);
Route::get('/checkApplication', [AplicationController::class, 'checkForApplication']);
Route::post('/deleteApplication', [AplicationController::class, 'deleteApplication']);
Route::get('/getApplications', [AplicationController::class, 'getApplications']);
Route::get('/getApplication', [AplicationController::class, 'getApplication']); // returneaza informatiile afernte unei anumite aplicatii
Route::post('/acceptApplication', [AplicationController::class, 'acceptApplication']); // returneaza informatiile afernte unei anumite aplicatii
Route::post('/rejectApplication', [AplicationController::class, 'rejectApplication']);
Route::post('/enableEditApplication', [AplicationController::class, 'enableEditApplication']);

