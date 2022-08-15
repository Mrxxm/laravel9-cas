<?php

use App\Http\Controllers\SSOController;
use Illuminate\Support\Facades\Route;

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


Route::any('/login', [SSOController::class, 'login']);

Route::any('/doLogin', [SSOController::class, 'doLogin']);

Route::any('/verifyTmpTicket', [SSOController::class, 'verifyTmpTicket']);

Route::any('/logout', [SSOController::class, 'logout']);
