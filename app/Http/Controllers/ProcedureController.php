<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcedureController extends Controller
{
    public function index(): View
    {
        $procedures = Procedure::orderBy('sort_order')->orderBy('name')->get();
        return view('settings.procedures.index', compact('procedures'));
    }

    public function create(): View
    {
        return view('settings.procedures.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:32|unique:procedures,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'modality' => 'required|string|max:16',
            'body_part' => 'nullable|string|max:64',
            'estimated_duration' => 'nullable|integer|min:1',
            'default_physician' => 'nullable|string|max:255',
            'default_room' => 'nullable|string|max:50',
            'default_exposure' => 'nullable|string|max:255',
            'requires_contrast' => 'boolean',
            'contrast_detail' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        Procedure::create($validated);

        return to_route('settings.procedures.index')->with('success', 'Procedure created.');
    }

    public function edit(Procedure $procedure): View
    {
        return view('settings.procedures.form', compact('procedure'));
    }

    public function update(Request $request, Procedure $procedure): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:32|unique:procedures,code,' . $procedure->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'modality' => 'required|string|max:16',
            'body_part' => 'nullable|string|max:64',
            'estimated_duration' => 'nullable|integer|min:1',
            'default_physician' => 'nullable|string|max:255',
            'default_room' => 'nullable|string|max:50',
            'default_exposure' => 'nullable|string|max:255',
            'requires_contrast' => 'boolean',
            'contrast_detail' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $procedure->update($validated);

        return to_route('settings.procedures.index')->with('success', 'Procedure updated.');
    }

    public function destroy(Procedure $procedure): RedirectResponse
    {
        $procedure->delete();
        return to_route('settings.procedures.index')->with('success', 'Procedure deleted.');
    }
}
