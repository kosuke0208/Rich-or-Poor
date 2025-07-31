<?php

use App\Http\Controllers\EndController;
use App\Http\Controllers\EntranceController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Players;
use App\Http\Controllers\TestController;
use App\Models\Member;
use Illuminate\Support\Facades\Route;
use Spatie\FlareClient\View;
use App\Http\Controllers\PlayerNamesController;
use App\Http\Controllers\GameController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/test', [EntranceController::class, "test"]);

Route::view('/name', 'name');

Route::post('/add', [EntranceController::class, "add"]);

Route::view('/wait', 'wait');





Route::view('/delete', 'delete');
Route::post('/deletePlayer', [
  EntranceController::class,
  "delete"
])->name('deletePlayer');

Route::view('/nullid', 'nullid');
Route::post('/nullplayername_id', [
  EntranceController::class,
  "nullid"
])->name('nullplayername_id');

Route::view('/Turnnull', 'Turnnull');
Route::post('/Turnnullplayername_id', [
  EntranceController::class,
  "Turnnull"
])->name('Turnnullplayername_id');

Route::get('/game', [PlayerNamesController::class, 'show']);

Route::get('/end',[EndController::class,'end']);

Route::get('/finish',[EndController::class,'finish'])->name('finish');





