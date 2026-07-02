<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Server;
use App\Models\WorklistItem;
use App\Services\Dcm4chee\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $server = Server::where('enabled', true)->first();
        $stats = [];

        if ($server) {
            $client = new Client($server);
            try {
                $stats['dicom_status'] = $client->raw('GET', 'monitoring/health', prefix: null)->successful() ? 'UP' : 'DOWN';
            } catch (\Exception) {
                $stats['dicom_status'] = 'DOWN';
            }
        }

        $stats['patients_today'] = 0;
        $stats['waiting_mwl'] = WorklistItem::where('status', 'waiting')->count();
        $stats['in_progress'] = WorklistItem::where('status', 'in_progress')->count();
        $stats['completed'] = WorklistItem::where('status', 'completed')->count();
        $stats['sent'] = WorklistItem::where('status', 'sent')->count();
        $stats['failed'] = WorklistItem::where('status', 'failed')->count();

        $recentItems = WorklistItem::latest()->take(10)->get();
        $recentLogs = AuditLog::with('server')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recentItems', 'recentLogs'));
    }
}
