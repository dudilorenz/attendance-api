<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class,'logout'])->name('logout');


Route::middleware('auth')->get('/attendance/status', function (Request $request) {
    $employee = $request->user()->employee;

    return response()->json([
        'clocked_in' => $employee?->isClockedIn() ?? false
    ]);
});


Route::middleware('auth')->get('/attendance', function () {
    return view('attendance', [
        'token' => session('api_token')
    ]);
})->name('attendance');
