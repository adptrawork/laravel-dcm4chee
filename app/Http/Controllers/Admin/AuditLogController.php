<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('user', 'server')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('server_id')) {
            $query->where('server_id', $request->server_id);
        }
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }
        if ($request->filled('endpoint')) {
            $query->where('endpoint', 'like', '%' . $request->endpoint . '%');
        }
        if ($request->filled('status_code')) {
            $query->where('response_status', $request->status_code);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        return view('admin.audit-logs.index', compact('logs'));
    }

    public function show(AuditLog $auditLog): View
    {
        $auditLog->load('user', 'server');
        return view('admin.audit-logs.show', compact('auditLog'));
    }

    public function export(Request $request): RedirectResponse
    {
        // ponytail: CSV export via raw query, add streaming if >10k rows
        $query = AuditLog::with('user', 'server')->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->take(5000)->get();

        $filename = 'audit-logs-' . now()->format('YmdHis') . '.csv';
        $path = storage_path('app/exports/' . $filename);
        // ponytail: single directory without subfolder check
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $handle = fopen($path, 'w');
        fputcsv($handle, ['Date', 'User', 'Server', 'Method', 'Endpoint', 'Status', 'Duration (ms)']);

        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->created_at,
                $log->user?->name ?? '-',
                $log->server?->name ?? '-',
                $log->method,
                $log->endpoint,
                $log->response_status,
                $log->duration_ms,
            ]);
        }

        fclose($handle);

        return back()->with('success', "Exported {$logs->count()} rows. File: {$filename}");
    }
}
