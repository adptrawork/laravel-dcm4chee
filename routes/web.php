<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\MwlController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\StudyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('servers', ServerController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('servers/{server}/test', [ServerController::class, 'test'])->name('servers.test');

    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patientId}', [PatientController::class, 'show'])->name('patients.show');
    Route::delete('/patients/{patientId}', [PatientController::class, 'destroy'])->name('patients.destroy');
    Route::post('/patients/set-server', [PatientController::class, 'setServer'])->name('patients.set-server');

    Route::get('/studies', [StudyController::class, 'index'])->name('studies.index');
    Route::get('/studies/{studyUid}', [StudyController::class, 'show'])->name('studies.show');
    Route::get('/studies/{studyUid}/series/{seriesUid}', [StudyController::class, 'series'])->name('studies.series');
    Route::get('/studies/{studyUid}/series/{seriesUid}/instances/{instanceUid}/metadata', [StudyController::class, 'metadata'])->name('studies.metadata');
    Route::get('/studies/{studyUid}/series/{seriesUid}/instances/{instanceUid}/rendered', [StudyController::class, 'rendered'])->name('studies.rendered');

    Route::get('/debug', [DebugController::class, 'index'])->name('debug.index');
    Route::post('/debug/send', [DebugController::class, 'send'])->name('debug.send');
        Route::post('/debug/set-server', [DebugController::class, 'setServer'])->name('debug.set-server');

    // MWL
    Route::get('/mwl', [MwlController::class, 'index'])->name('mwl.index');
    Route::post('/mwl/create', [MwlController::class, 'create'])->name('mwl.create');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
