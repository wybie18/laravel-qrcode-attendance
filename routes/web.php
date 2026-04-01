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

    Route::get('positions/export', [PositionController::class, 'export'])
        ->name('positions.export');
    Route::get('positions/template', [PositionController::class, 'template'])
        ->name('positions.template');
    Route::post('positions/import', [PositionController::class, 'import'])
        ->name('positions.import');
        Route::get('offices/export', [OfficeController::class, 'exportMethod'])
            ->name('offices.export');
        Route::get('offices/template', [OfficeController::class, 'template'])
            ->name('offices.template');
        Route::post('offices/import', [OfficeController::class, 'importMethod'])
            ->name('offices.import');

    Route::resources([
        'positions' => PositionController::class,
        'offices' => OfficeController::class,
        'personnels' => PersonnelController::class,
        'attendance-logs' => AttendanceLogController::class,
    ]);

    Route::post('/qr-code/download', [QrCodeController::class, 'download'])->name('qr-code.download');
});

require __DIR__.'/settings.php';
