<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ExamFormController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Auth\LoginController;

use Illuminate\Support\Facades\Auth;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

Route::view('/', 'auth.login');

Route::post('login-submit', [LoginController::class, 'loginSubmit'])->name('loginSubmit');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function() {

   Route::prefix('manager')->name('manager.')->middleware(['auth', 'manager'])->group(function () {
     
        Route::get('dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        Route::resource('roles', RoleController::class);

        Route::get('users/remark-details/{remark}', [UserController::class, 'userRemarkDetails'])
            ->name('users.remark-details');
        Route::post('users/close-remark-details', [UserController::class, 'closeRemarkDetails'])
            ->name('users.close-remark-details');
        Route::resource('users', UserController::class);
        Route::resource('permissions',PermissionController::class); // ← Add this line

       
    });

});