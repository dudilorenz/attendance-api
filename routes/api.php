<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::post('/entry', [AttendanceController::class, 'store']);
Route::get('/report/{workerId}', [AttendanceController::class, 'report']);
