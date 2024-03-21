<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PusherController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/pdf', function () {
    return view('pdf');
});

Route::get('/test-pusher/{text}', [PusherController::class, 'index'])->name('home');
Route::post('/exam', [PusherController::class, 'pushedData'])->name('exam');
Route::get('/exam-show', [PusherController::class, 'examShow']);

Route::get('test', function () {
    event(new App\Events\MessageSent('websolutionstuff_team'));
    return "Event has been sent!";
});

Route::controller(UserController::class)->group(function(){
    Route::get('users', 'index');
    Route::get('users-export', 'export')->name('users.export');
    Route::post('users-import', 'import')->name('users.import');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
