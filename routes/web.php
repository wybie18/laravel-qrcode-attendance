<?php

use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::resources([
        'positions' => PositionController::class,
        'offices' => OfficeController::class,
        'personnels' => PersonnelController::class,
        'attendance-logs' => AttendanceLogController::class,
    ]);

    Route::post('/qr-code/download', [QrCodeController::class, 'download'])->name('qr-code.download');
});

require __DIR__.'/settings.php';
