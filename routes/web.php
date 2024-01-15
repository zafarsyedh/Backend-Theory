<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;



use Illuminate\Support\Facades\Auth;



Route::any('/', [HomeController::class, 'index']);
Route::any('/test', [HomeController::class, 'test']);


Route::controller(UserController::class)->group(function(){
    Route::get('users', 'index');
    Route::get('users-export', 'export')->name('users.export');
    Route::post('users-import', 'import')->name('users.import');
});
