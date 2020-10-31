<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileControlController;
use App\Http\Controllers\AuthController;

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
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);


    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('user', [AuthController::class, 'user']);
    });
});

Route::group([
    'middleware' => 'auth:api'
], function () {

Route::get('tree', [FileControlController::class, 'tree']);
Route::get('content', [FileControlController::class, 'content']);
Route::post('upload', [FileControlController::class, 'upload']);
Route::post('paste', [FileControlController::class, 'paste']);
Route::post('rename', [FileControlController::class, 'rename']);
Route::get('delete', [FileControlController::class, 'delete']);
Route::post('create-directory', [FileControlController::class, 'createDirectory']);
Route::post('create-file', [FileControlController::class, 'createFile']);
});
Route::get('download', [FileControlController::class, 'download']);
Route::get('thumbnails', [FileControlController::class, 'thumbnails']);
