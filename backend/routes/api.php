<?php

use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DetailsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostVoteController;
use App\Http\Controllers\AplicationController;
use App\Http\Controllers\NotificationController;
use \App\Http\Controllers\ReportController;
use \App\Http\Controllers\CommentController;


Route::post('/addUser', [UserController::class, 'createUser']); // inregistrarea unui user
Route::post('/logout', [UserController::class, 'logoutUser']); // deconectarea userului actual
Route::post('/login', [UserController::class, 'loginUser']); // conectarea unui user
Route::get('/fetchUserData', [UserController::class, 'fetchUserData']); // preluarea datelor unui user
Route::get('/fetchProfile', [UserController::class, 'fetchProfile']);
Route::post('/cancelProducer', [UserController::class, 'cancelProducer']); // anuleaza gradul unui producator
Route::post('/giveProducer', [UserController::class, 'giveProducer']); // adauga gradul de producator unui utilizator
Route::post('/banUser', [UserController::class, 'banUser']);
Route::post('/warnUser', [UserController::class, 'warnUser']);
Route::post('/unbanUser', [UserController::class, 'unbanUser']);
Route::post('/updateUserData', [UserController::class, 'updateUserData']);
Route::post('/updateUserProfilePhoto', [UserController::class, 'updateUserProfilePhoto']);
Route::post('/fallowUser', [UserController::class, 'addFallower']);
Route::post('/cancelFallowUser', [UserController::class, 'deleteFallower']);
Route::get('/fetchFallowList', [UserController::class, 'fetchFallowList']);
Route::get('/fetchFallowPosts', [PostController::class, 'fetchFallowPosts']);
Route::get('/getFallow', [UserController::class, 'getFallow']);
Route::post('/sendEmail', [UserController::class, 'resendEmail']);
Route::post('/verifyAccount', [UserController::class, 'verifyAccount']);
Route::post('/recoveryPassword', [UserController::class, 'recoveryPassword']);
Route::post('/verifyRecovery', [UserController::class, 'verifyRecovery']);
Route::post('/resetPassword', [UserController::class, 'resetPassword']);
Route::get('/localProducers', [UserController::class, 'localProducers']);


//Raportare
Route::post('/sendReport', [ReportController::class, 'sendReport']);
Route::get('/getReports', [ReportController::class, 'getReports']);
Route::post('/markSolved', [ReportController::class, 'markSolved']);

//Notificari
Route::get('/getUnreadNotifications', [NotificationController::class, 'getUnreadNotifications']);
Route::post('/markReadNotifications', [NotificationController::class, 'markReadNotifications']);
Route::get('/getNotifications', [NotificationController::class, 'getNotifications']);

//Home
Route::get('/user/search', [DetailsController::class, 'search']);

//Admins
Route::get('/admin/users', [DetailsController::class, 'pagination']);
Route::post('/admin/delete', [DetailsController::class, 'deleteUser']);

//Check data
Route::get('/verifyData', [DetailsController::class, 'verifyData']); // verifica daca utilzatorul conectat este administrator in baza de date

//Postari
Route::post('/addPost', [PostController::class, 'addPost']);
Route::get('/fetchUserPosts', [PostController::class, 'fetchUserPosts']); // returneaza postarile de pe profilul unui user
Route::get('fetchPostsVoted', [PostVoteController::class, 'fetchUserPostsVoted']);
Route::get('/vote', [PostController::class, 'postVote']);
Route::get('/modifyVote',[PostVoteController::class, 'modifyVote']);
Route::get('/getPost', [PostController::class, 'getPost']);
Route::post('/deletePost', [PostController::class, 'deletePost']);
Route::post('/deleteUserPost', [PostController::class, 'deleteUserPost']);
Route::post('/modifyDescriptionPost', [PostController::class, 'modifyDescriptionPost']);


//Aplicatiii
Route::post('/addApplication', [AplicationController::class, 'addApplication']);
Route::post('/addApplicationDocs', [AplicationController::class, 'uploadForm']);
Route::get('/checkApplication', [AplicationController::class, 'checkForApplication']);
Route::post('/deleteApplication', [AplicationController::class, 'deleteApplication']);
Route::get('/getApplications', [AplicationController::class, 'getApplications']);
Route::get('/getApplication', [AplicationController::class, 'getApplication']); // returneaza informatiile afernte unei anumite aplicatii
Route::post('/acceptApplication', [AplicationController::class, 'acceptApplication']); // returneaza informatiile afernte unei anumite aplicatii
Route::post('/rejectApplication', [AplicationController::class, 'rejectApplication']);
Route::post('/enableEditApplication', [AplicationController::class, 'enableEditApplication']);

//Comments
Route::post('addComment', [CommentController::class, 'addComment']);
Route::get('getComments', [CommentController::class, 'getComments']);
Route::post('deleteComment', [CommentController::class, 'deleteComment']);

//Review
Route::post('sendReview', [ReviewController::class, 'sendReview']);
Route::get('getReviews', [ReviewController::class, 'getReviews']);
Route::get('getMyReview', [ReviewController::class, 'getMyReview']);

