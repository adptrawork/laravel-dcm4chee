<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Services\Dcm4chee\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;

class ServerController extends Controller
{
    public function index(): View
    {
        $servers = Server::latest()->get();

        return view('servers.index', compact('servers'));
    }

    public function create(): View
    {
        return view('servers.form', ['server' => new Server]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'required|url|max:255',
            'archive' => 'required|string|max:255',
            'aet' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'timeout' => 'integer|min:5|max:120',
            'ssl_verify' => 'boolean',
            'enabled' => 'boolean',
        ]);

        $validated['password'] = Crypt::encryptString($validated['password']);

        Server::create($validated);

        return to_route('servers.index')
            ->with('success', 'Server configuration created.');
    }

    public function edit(Server $server): View
    {
        return view('servers.form', compact('server'));
    }

    public function update(Request $request, Server $server): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'required|url|max:255',
            'archive' => 'required|string|max:255',
            'aet' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'timeout' => 'integer|min:5|max:120',
            'ssl_verify' => 'boolean',
            'enabled' => 'boolean',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Crypt::encryptString($validated['password']);
        }

        $server->update($validated);

        return to_route('servers.index')
            ->with('success', 'Server configuration updated.');
    }

    public function destroy(Server $server): RedirectResponse
    {
        $server->delete();

        return to_route('servers.index')
            ->with('success', 'Server configuration deleted.');
    }

    public function test(Server $server): RedirectResponse
    {
        try {
            $auth = new AuthService($server);
            $token = $auth->getToken();

            $message = $token
                ? 'Connection successful. Token acquired.'
                : 'Connected but failed to acquire token.';
        } catch (\Exception $e) {
            return back()->with('error', 'Connection failed: ' . $e->getMessage());
        }

        return back()->with('success', $message);
    }
}
