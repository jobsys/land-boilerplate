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


Route::get('/', [\App\Http\Controllers\Controller::class, 'index'])->name('index');

// 有 CAS 认证时开启
// Route::get('/cas/login', [\App\Http\Controllers\CasCgiController::class, 'login'])->name('cas.login');

Route::any('/temp/file', function (Request $request) {

	if (!request()->hasValidSignature()) {
		abort(401);
	}

	return Storage::disk('private')->download(request('path'));
})->name('temp.file');

Route::name('page.manager.')->prefix('manager')->group(function () {
	Route::get('/', [\App\Http\Controllers\Manager\IndexController::class, 'pageDashboard'])->name('dashboard');
	Route::get('/tool/data-transfer', [\App\Http\Controllers\Manager\ToolController::class, 'pageDataTransfer'])->name('tool.data-transfer');
	Route::get('/configuration/security', [\App\Http\Controllers\Manager\ConfigurationController::class, 'pageConfigurationSecurity'])->name('configuration.security');

	Route::get('/todo', [\App\Http\Controllers\Manager\IndexController::class, 'pageTodo'])->name('todo');

	Route::get('/center/profile', [\App\Http\Controllers\Manager\IndexController::class, 'pageCenterProfile'])->name('center.profile');
	Route::get('/center/password', [\App\Http\Controllers\Manager\IndexController::class, 'pageCenterPassword'])->name('center.password');

	Route::get('/user', [\App\Http\Controllers\Manager\UserController::class, 'pageUser'])->name('user');
	Route::get('/department', [\App\Http\Controllers\Manager\DepartmentController::class, 'pageDepartment'])->name('department');
});

