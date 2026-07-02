<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Utilities\DicomToolController;
use App\Http\Controllers\Utilities\HealthController;
use App\Http\Controllers\Utilities\LogViewerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\MwlQueueController;
use App\Http\Controllers\PacsMonitorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ModalityMonitorController;
use App\Http\Controllers\StudyController;
use App\Http\Controllers\StudyPollerController;
use App\Http\Controllers\StudyTrackerController;
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

    // MWL Queue
    Route::get('/mwl-queue', [MwlQueueController::class, 'index'])->name('mwl-queue.index');
    Route::post('/mwl-queue/set-server', [MwlQueueController::class, 'setServer'])->name('mwl-queue.set-server');
    Route::post('/mwl-queue/{item}/renew', [MwlQueueController::class, 'renew'])->name('mwl-queue.renew');

    // PACS Monitor
    Route::get('/pacs-monitor', [PacsMonitorController::class, 'index'])->name('pacs-monitor.index');
    Route::post('/pacs-monitor/{item}/status', [PacsMonitorController::class, 'updateStatus'])->name('pacs-monitor.update-status');
    Route::get('/pacs-monitor/{item}/retry', [PacsMonitorController::class, 'retry'])->name('pacs-monitor.retry');
    Route::post('/pacs-monitor/set-server', [PacsMonitorController::class, 'setServer'])->name('pacs-monitor.set-server');

    // Modality Monitor
    Route::get('/modality-monitor', [ModalityMonitorController::class, 'index'])->name('modality-monitor.index');
    Route::post('/modality-monitor/set-server', [ModalityMonitorController::class, 'setServer'])->name('modality-monitor.set-server');
    Route::get('/modality-monitor/{device}/ping', [ModalityMonitorController::class, 'ping'])->name('modality-monitor.ping');

    // Study Tracker
    Route::get('/study-tracker', [StudyTrackerController::class, 'index'])->name('study-tracker.index');
    Route::get('/study-tracker/{accession}', [StudyTrackerController::class, 'show'])->name('study-tracker.show');

    // Study Poller (manual trigger)
    Route::get('/studies/poll', [StudyPollerController::class, 'poll'])->name('studies.poll');

    // Devices
    Route::resource('devices', DeviceController::class);
    Route::get('/devices/{device}/echo', [DeviceController::class, 'echo'])->name('devices.echo');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/system', [SettingsController::class, 'updateSystem'])->name('settings.update-system');
    Route::post('/settings/mwl', [SettingsController::class, 'updateMwlConfig'])->name('settings.update-mwl');
    Route::post('/settings/templates', [SettingsController::class, 'storeTemplate'])->name('settings.store-template');
    Route::delete('/settings/templates/{template}', [SettingsController::class, 'destroyTemplate'])->name('settings.destroy-template');

    // Admin - User Management
    Route::resource('admin/users', UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);

    // Admin - Role Management
    Route::resource('admin/roles', RoleController::class)->names([
        'index' => 'admin.roles.index',
        'create' => 'admin.roles.create',
        'store' => 'admin.roles.store',
        'edit' => 'admin.roles.edit',
        'update' => 'admin.roles.update',
        'destroy' => 'admin.roles.destroy',
    ]);

    // Admin - Audit Logs
    Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs.index');
    Route::get('/admin/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('admin.audit-logs.show');
    Route::get('/admin/audit-logs/export', [AuditLogController::class, 'export'])->name('admin.audit-logs.export');

    // Admin - Job Queue
    Route::get('/admin/jobs', [JobController::class, 'index'])->name('admin.jobs.index');
    Route::get('/admin/jobs/{id}/retry', [JobController::class, 'retry'])->name('admin.jobs.retry');

    // Utilities - DICOM Tools
    Route::get('/utilities/dicom', [DicomToolController::class, 'index'])->name('utilities.dicom.index');
    Route::post('/utilities/dicom/echo', [DicomToolController::class, 'echo'])->name('utilities.dicom.echo');
    Route::post('/utilities/dicom/ping', [DicomToolController::class, 'ping'])->name('utilities.dicom.ping');
    Route::post('/utilities/dicom/find', [DicomToolController::class, 'find'])->name('utilities.dicom.find');
    Route::post('/utilities/dicom/move', [DicomToolController::class, 'move'])->name('utilities.dicom.move');

    // Utilities - Health Check
    Route::get('/utilities/health', [HealthController::class, 'index'])->name('utilities.health');

    // Utilities - Log Viewer
    Route::get('/utilities/logs', [LogViewerController::class, 'index'])->name('utilities.log-viewer');

    // Procedure Catalog
    Route::get('/settings/procedures', [ProcedureController::class, 'index'])->name('settings.procedures.index');
    Route::get('/settings/procedures/create', [ProcedureController::class, 'create'])->name('settings.procedures.create');
    Route::post('/settings/procedures', [ProcedureController::class, 'store'])->name('settings.procedures.store');
    Route::get('/settings/procedures/{procedure}/edit', [ProcedureController::class, 'edit'])->name('settings.procedures.edit');
    Route::put('/settings/procedures/{procedure}', [ProcedureController::class, 'update'])->name('settings.procedures.update');
    Route::delete('/settings/procedures/{procedure}', [ProcedureController::class, 'destroy'])->name('settings.procedures.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
