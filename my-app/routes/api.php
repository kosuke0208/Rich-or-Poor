<?php

use App\Http\Controllers\EntranceaApiController;
use App\Http\Controllers\GameApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});
Route::get('sample',[EntranceaApiController::class,'apiHello']);

Route::get('checkPlayer',[EntranceaApiController::class,'checkPlayer']);

Route::get('checkCard',[EntranceaApiController::class,'checkCard']);

Route::get('all',[GameApiController::class,'all']);

Route::get('hands',[GameApiController::class,'hands']);

Route::get('/turn',[GameApiController::class,'turn']);

Route::get('shed',[GameApiController::class,'shed']);

Route::get('finish',[GameApiController::class,'finish']);