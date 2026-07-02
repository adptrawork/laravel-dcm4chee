<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\PacsMonitorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudyController;
use App\Http\Controllers\WorklistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Servers
    Route::resource('servers', ServerController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('servers/{server}/test', [ServerController::class, 'test'])->name('servers.test');

    // Patients
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patientId}', [PatientController::class, 'show'])->name('patients.show');
    Route::delete('/patients/{patientId}', [PatientController::class, 'destroy'])->name('patients.destroy');
    Route::post('/patients/set-server', [PatientController::class, 'setServer'])->name('patients.set-server');

    // Studies
    Route::get('/studies', [StudyController::class, 'index'])->name('studies.index');
    Route::get('/studies/{studyUid}', [StudyController::class, 'show'])->name('studies.show');
    Route::get('/studies/{studyUid}/series/{seriesUid}', [StudyController::class, 'series'])->name('studies.series');
    Route::get('/studies/{studyUid}/series/{seriesUid}/instances/{instanceUid}/metadata', [StudyController::class, 'metadata'])->name('studies.metadata');
    Route::get('/studies/{studyUid}/series/{seriesUid}/instances/{instanceUid}/rendered', [StudyController::class, 'rendered'])->name('studies.rendered');

    // Registration
    Route::get('/registration', [RegistrationController::class, 'index'])->name('registration.index');
    Route::post('/registration', [RegistrationController::class, 'store'])->name('registration.store');
    Route::post('/registration/search-patients', [RegistrationController::class, 'searchPatients'])->name('registration.search-patients');
    Route::post('/registration/set-server', [RegistrationController::class, 'setServer'])->name('registration.set-server');

    // Worklist
    Route::get('/worklist', [WorklistController::class, 'index'])->name('worklist.index');
    Route::get('/worklist/refresh', [WorklistController::class, 'refresh'])->name('worklist.refresh');
    Route::post('/worklist/{item}/status', [WorklistController::class, 'updateStatus'])->name('worklist.update-status');
    Route::post('/worklist/set-server', [WorklistController::class, 'setServer'])->name('worklist.set-server');

    // PACS Monitor
    Route::get('/pacs-monitor', [PacsMonitorController::class, 'index'])->name('pacs-monitor.index');
    Route::post('/pacs-monitor/{item}/status', [PacsMonitorController::class, 'updateStatus'])->name('pacs-monitor.update-status');
    Route::get('/pacs-monitor/{item}/retry', [PacsMonitorController::class, 'retry'])->name('pacs-monitor.retry');
    Route::post('/pacs-monitor/set-server', [PacsMonitorController::class, 'setServer'])->name('pacs-monitor.set-server');

    // Devices
    Route::resource('devices', DeviceController::class);
    Route::get('/devices/{device}/echo', [DeviceController::class, 'echo'])->name('devices.echo');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/system', [SettingsController::class, 'updateSystem'])->name('settings.update-system');
    Route::post('/settings/mwl', [SettingsController::class, 'updateMwlConfig'])->name('settings.update-mwl');
    Route::post('/settings/templates', [SettingsController::class, 'storeTemplate'])->name('settings.store-template');
    Route::delete('/settings/templates/{template}', [SettingsController::class, 'destroyTemplate'])->name('settings.destroy-template');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
