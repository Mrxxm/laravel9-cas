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

//Route::middleware(['auth:sanctum'])->get('/cache', function (Request $request) {
//
//
//    \Illuminate\Support\Facades\DB::beginTransaction();
//
//    try {
//
//        $permission = new \Hptown\Hms\PermissionCheck\Models\PermissionGroupHasPermission();
//        $permission->permission_group_id = 10;
//        $permission->permission_id = 20;
//        $permission->save();
//
//        \Hptown\Hms\PermissionCheck\Models\PermissionGroupHasPermission::query()
//            ->where('permission_group_id', 1)
//            ->where('permission_id', 2)
//            ->delete();
//
//        \Illuminate\Support\Facades\DB::commit();
//    } catch (Exception $e) {
//        dd($e->getMessage());
//        \Illuminate\Support\Facades\DB::rollBack();
//    }
//});

Route::any('/token/create', function (Request $request) {
    $user = \App\Models\User::find(1);

    $user->tokens()->delete();

    $token = $user->createToken($user->name);

    return ['token' => $token->plainTextToken];
});



