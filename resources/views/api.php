<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;

Route::post("/login", [AuthController::class,""])->name("login");

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/attendance/in', [AttendanceController::class, 'clockIn']);
    Route::post('/attendance/out', [AttendanceController::class, 'clockOut']);
});


// web.php

// Route::middleware('auth')->get('/attendance', function () {
//     return view('attendance', [
//         'status' => 'לא בעבודה'
//     ]);
// });


