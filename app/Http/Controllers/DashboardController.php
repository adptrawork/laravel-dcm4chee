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

        $stats['registered'] = WorklistItem::where('status', WorklistItem::STATUS_REGISTERED)->count();
        $stats['mw_published'] = WorklistItem::where('status', WorklistItem::STATUS_MW_PUBLISHED)->count();
        $stats['acquiring'] = WorklistItem::where('status', WorklistItem::STATUS_ACQUIRING)->count();
        $stats['acquired'] = WorklistItem::where('status', WorklistItem::STATUS_ACQUIRED)->count();
        $stats['archived'] = WorklistItem::where('status', WorklistItem::STATUS_ARCHIVED)->count();

        $recentItems = WorklistItem::latest()->take(10)->get();
        $recentLogs = AuditLog::with('server')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recentItems', 'recentLogs'));
    }
}
