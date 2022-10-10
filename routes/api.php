<?php

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

Route::middleware(['auth:sanctum', 'permission-check'])->get('/user', function (Request $request) {
    return $request->user();
})->name('api/user');

Route::any('/token/create', function (Request $request) {
    $user = \App\Models\User::find(1);

    $user->tokens()->delete();

    $token = $user->createToken($user->name);

    return ['token' => $token->plainTextToken];
});



