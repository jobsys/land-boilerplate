<?php

use Illuminate\Support\Facades\Route;
use Tightenco\Ziggy\Ziggy;
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
Route::prefix("manager")->name("api.manager.")->group(function () {


	Route::post('/tool/upload', [\App\Http\Controllers\Manager\ToolController::class, 'upload'])->name('tool.upload');

	Route::post('/configuration', [\App\Http\Controllers\Manager\ConfigurationController::class, 'edit'])->name('configuration.edit');

    Route::post('/center/profile', [\App\Http\Controllers\Manager\IndexController::class, 'centerProfileEdit'])->name('center.profile.edit');
    Route::post('/center/password', [\App\Http\Controllers\Manager\IndexController::class, 'centerPasswordEdit'])->name('center.password.edit');

    Route::post('/department', [\App\Http\Controllers\Manager\DepartmentController::class, 'edit'])->name('department.edit');
    Route::get('/department', [\App\Http\Controllers\Manager\DepartmentController::class, 'items'])->name('department.items');
    Route::get('/department/{id}', [\App\Http\Controllers\Manager\DepartmentController::class, 'item'])->where('id', '[0-9]+')->name('department.item');
    Route::post('/department/delete', [\App\Http\Controllers\Manager\DepartmentController::class, 'delete'])->name('department.delete');

    Route::post('/user', [\App\Http\Controllers\Manager\UserController::class, 'edit'])->name('user.edit');
    Route::get('/user', [\App\Http\Controllers\Manager\UserController::class, 'items'])->name('user.items');
    Route::get('/user/{id}', [\App\Http\Controllers\Manager\UserController::class, 'item'])->where('id', '[0-9]+')->name('user.item');
    Route::post('/user/delete', [\App\Http\Controllers\Manager\UserController::class, 'delete'])->name('user.delete');
    Route::post('/user/department', [\App\Http\Controllers\Manager\UserController::class, 'department'])->name('user.department');
	Route::post('/user/import', [\App\Http\Controllers\Manager\UserController::class, 'import'])->name('user.import');

});


Route::get('ziggy/{group?}', fn($group = null) => response()->json(new Ziggy($group)));
