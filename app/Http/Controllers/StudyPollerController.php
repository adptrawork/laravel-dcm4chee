<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Services\Ris\StudyPollerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudyPollerController extends Controller
{
    public function poll(Request $request): RedirectResponse
    {
        $serverId = $request->session()->get('active_server_id');
        $server = Server::findOrFail($serverId);

        $service = new StudyPollerService;
        $result = $service->poll($server);

        return back()->with('success', "Study poll complete: {$result['message']}");
    }
}
