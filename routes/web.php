<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


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
Route::name('page.manager.')->prefix('manager')->group(function () {
	Route::get('/', [\App\Http\Controllers\Manager\IndexController::class, 'pageDashboard'])->name('dashboard');
	Route::get('/tool/data-transfer', [\App\Http\Controllers\Manager\ToolController::class, 'pageDataTransfer'])->name('tool.data-transfer');

	Route::get('/center/profile', [\App\Http\Controllers\Manager\IndexController::class, 'pageCenterProfile'])->name('center.profile');
	Route::get('/center/password', [\App\Http\Controllers\Manager\IndexController::class, 'pageCenterPassword'])->name('center.password');
	Route::get('/user', [\App\Http\Controllers\Manager\UserController::class, 'pageUser'])->name('user');

	Route::get('/department', [\App\Http\Controllers\Manager\DepartmentController::class, 'pageDepartment'])->name('department');

});



Route::get('/', fn() => response(config('app.name')));

Route::any('temp/file', function () {
	if (!request()->hasValidSignature()) {
		abort(401);
	}

	return Storage::disk('private')->download(request('path'));
})->name('temp.file');
