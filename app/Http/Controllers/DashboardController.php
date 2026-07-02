<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Server;

class DashboardController extends Controller
{
    public function index()
    {
        $serversCount = Server::count();
        $activeServers = Server::where('enabled', true)->count();
        $logsCount = AuditLog::count();
        $recentLogs = AuditLog::with('server')->latest()->take(5)->get();

        return view('dashboard', compact('serversCount', 'activeServers', 'logsCount', 'recentLogs'));
    }
}
