<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\WorklistItem;
use App\Services\Dcm4chee\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MwlQueueController extends Controller
{
    public function index(Request $request): View
    {
        $servers = Server::where('enabled', true)->get();
        $serverId = $request->session()->get('mwl_queue_server_id', $servers->first()?->id);
        $status = $request->input('status', '');

        $items = WorklistItem::when($serverId, fn($q) => $q->where('server_id', $serverId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->take(100)
            ->get();

        return view('mwl-queue.index', compact('servers', 'serverId', 'items', 'status'));
    }

    public function setServer(Request $request): RedirectResponse
    {
        $validated = $request->validate(['server_id' => 'required|exists:servers,id']);
        $request->session()->put('mwl_queue_server_id', $validated['server_id']);
        return back();
    }

    public function renew(WorklistItem $item): RedirectResponse
    {
        if (!in_array($item->status, ['registered', 'mw_published'])) {
            return back()->withErrors(['error' => 'Item is not in a renewable state.']);
        }

        $server = Server::find($item->server_id);
        if (!$server) {
            return back()->withErrors(['error' => 'No server configured.']);
        }

        try {
            $client = new Client($server);
            $client->raw('DELETE', "workitems/{$item->accession_number}", [], 'mwl');
        } catch (\Exception) {
            // may not exist on PACS, ignore
        }

        $item->update(['status' => WorklistItem::STATUS_MW_PUBLISHED]);
        return back()->with('success', 'MWL re-published.');
    }
}
