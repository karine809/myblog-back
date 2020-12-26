<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

    Route::post('verifiyemail', [AuthController::class, 'verifiyemail']);
    Route::post('newblog', [UserController::class, 'newblog']);
    Route::post('myblogs', [UserController::class, 'myblogs']);
    Route::post('myblog/{id}', [UserController::class, 'myblog']);

    Route::post('editblog/{id}', [UserController::class, 'editblog']);
    Route::get('deleteblog/{id}', [UserController::class, 'deleteblog']);

    Route::post('admin/allblogs', [UserController::class, 'allblogs']);
    Route::post('admin/allbloggers', [UserController::class, 'allbloggers']);


   Route::post('lockuser/{id}', [UserController::class, 'lockuser']);
   Route::post('unlockuser/{id}', [UserController::class, 'unlockuser']);


});
