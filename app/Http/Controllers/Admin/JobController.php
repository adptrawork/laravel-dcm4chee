<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(): View
    {
        $failedJobs = DB::table('failed_jobs')->orderByDesc('failed_at')->paginate(50);
        $pendingJobs = DB::table('jobs')->orderByDesc('created_at')->paginate(50, ['*'], 'pending_page');

        return view('admin.jobs.index', compact('failedJobs', 'pendingJobs'));
    }

    public function retry(string $id): RedirectResponse
    {
        // ponytail: uses artisan call, add queue:retry all if batch needed
        $exitCode = \Illuminate\Support\Facades\Artisan::call('queue:retry', ['id' => $id]);
        $message = $exitCode === 0 ? 'Job queued for retry.' : 'Failed to retry job.';

        return back()->with($exitCode === 0 ? 'success' : 'error', $message);
    }
}
