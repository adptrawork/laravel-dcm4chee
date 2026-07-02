<?php

namespace App\Http\Controllers;

use App\Models\ExaminationTemplate;
use App\Models\MwlConfig;
use App\Models\Server;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = SystemSetting::all()->groupBy('group');
        $servers = Server::where('enabled', true)->get();
        $templates = ExaminationTemplate::orderBy('sort_order')->get();

        return view('settings.index', compact('settings', 'servers', 'templates'));
    }

    public function updateSystem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hospital_name' => 'nullable|string|max:255',
            'hospital_address' => 'nullable|string|max:500',
            'timezone' => 'nullable|string|max:50',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::set($key, $value ?? '', 'general');
        }

        return back()->with('success', 'System settings saved.');
    }

    public function updateMwlConfig(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'default_aet' => 'required|string|max:16',
            'default_modality' => 'nullable|string|max:16',
            'default_physician' => 'nullable|string|max:255',
            'default_room' => 'nullable|string|max:50',
            'default_institution' => 'nullable|string|max:255',
        ]);

        MwlConfig::updateOrCreate(
            ['server_id' => $validated['server_id']],
            $validated
        );

        return back()->with('success', 'MWL config saved.');
    }

    // Examination Templates
    public function storeTemplate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'modality' => 'required|string|max:16',
            'description' => 'required|string',
            'room' => 'nullable|string|max:50',
            'priority' => 'required|in:routine,urgent,stat',
            'sort_order' => 'nullable|integer',
        ]);

        ExaminationTemplate::create($validated);

        return back()->with('success', 'Template created.');
    }

    public function destroyTemplate(ExaminationTemplate $template): RedirectResponse
    {
        $template->delete();
        return back()->with('success', 'Template deleted.');
    }
}
