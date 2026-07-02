<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\WorklistItem;
use App\Services\Dcm4chee\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class PacsMonitorController extends Controller
{
    public function index(Request $request): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('pacs_server_id', $servers->first()?->id);
        $statusFilter = $request->input('status', '');

        $items = WorklistItem::with('server')
            ->when($serverId, fn($q) => $q->where('server_id', $serverId))
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
            ->latest()
            ->take(100)
            ->get();

        return view('pacs-monitor.index', compact('servers', 'serverId', 'items', 'statusFilter'));
    }

    public function updateStatus(Request $request, WorklistItem $item): RedirectResponse
    {
        $validated = $request->validate(['status' => 'required|string|in:sent,failed']);
        $item->update([
            'status' => $validated['status'],
            'sent_at' => $validated['status'] === 'sent' ? now() : null,
        ]);

        return back()->with('success', "Status updated to {$validated['status']}.");
    }

    public function retry(WorklistItem $item): RedirectResponse
    {
        $server = Server::find($item->server_id);
        if (!$server) {
            return back()->withErrors(['error' => 'No server configured.']);
        }

        $item->update(['status' => 'waiting', 'error_message' => null]);

        return back()->with('success', 'Item requeued for processing.');
    }

    public function setServer(Request $request): RedirectResponse
    {
        $validated = $request->validate(['server_id' => 'required|exists:servers,id']);
        $request->session()->put('pacs_server_id', $validated['server_id']);
        return back();
    }
}
