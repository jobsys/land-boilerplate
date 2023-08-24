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
    Route::get('/center/profile', [\App\Http\Controllers\Manager\IndexController::class, 'pageCenterProfile'])->name('center.profile');
    Route::get('/center/password', [\App\Http\Controllers\Manager\IndexController::class, 'pageCenterPassword'])->name('center.password');
    Route::get('/user', [\App\Http\Controllers\Manager\UserController::class, 'pageUser'])->name('user');

    Route::get('/department', [\App\Http\Controllers\Manager\DepartmentController::class, 'pageDepartment'])->name('department');

});


Route::get('local/temp/{path?}', function (string $path) {
    //由于路径中的 / 会被转义，所以这里需要把 - 恢复成 /，才能正确读取到文件路径
    return Storage::disk('local')->download(str_replace('-', '/', $path));
})->name('local.temp');
