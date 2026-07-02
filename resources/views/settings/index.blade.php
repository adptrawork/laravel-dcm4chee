<x-app-layout>
    @section('title', 'Settings')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Settings</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-3 text-sm text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="p-3 text-sm text-red-700 bg-red-100 rounded-lg">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">System Settings</h3>
                <form method="POST" action="{{ route('settings.update-system') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hospital Name</label>
                            <input type="text" name="hospital_name" value="{{ \App\Models\SystemSetting::get('hospital_name') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Timezone</label>
                            <input type="text" name="timezone" value="{{ \App\Models\SystemSetting::get('timezone', config('app.timezone')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="hospital_address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">{{ \App\Models\SystemSetting::get('hospital_address') }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Save</button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">MWL Configuration</h3>
                @php $mwlConfig = null; @endphp
                @if(($serverId = $servers->first()?->id) && $servers->count())
                    @php $mwlConfig = \App\Models\MwlConfig::where('server_id', $serverId)->first(); @endphp
                @endif
                <form method="POST" action="{{ route('settings.update-mwl') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Server</label>
                            <select name="server_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                @foreach($servers as $srv)
                                    <option value="{{ $srv->id }}" {{ old('server_id', $mwlConfig->server_id ?? '') == $srv->id ? 'selected' : '' }}>{{ $srv->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Default Station AE</label>
                            <input type="text" name="default_aet" value="{{ old('default_aet', $mwlConfig->default_aet ?? 'PZDR') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Default Modality</label>
                            <input type="text" name="default_modality" value="{{ old('default_modality', $mwlConfig->default_modality ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Default Physician</label>
                            <input type="text" name="default_physician" value="{{ old('default_physician', $mwlConfig->default_physician ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Default Room</label>
                            <input type="text" name="default_room" value="{{ old('default_room', $mwlConfig->default_room ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Default Institution</label>
                            <input type="text" name="default_institution" value="{{ old('default_institution', $mwlConfig->default_institution ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Save</button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700">Examination Templates</h3>
                </div>

                <table class="min-w-full divide-y divide-gray-200 text-sm mb-4">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-500 text-xs uppercase">Name</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-500 text-xs uppercase">Modality</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-500 text-xs uppercase">Room</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-500 text-xs uppercase">Priority</th>
                            <th class="px-3 py-2 text-right font-medium text-gray-500 text-xs uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($templates as $t)
                            <tr>
                                <td class="px-3 py-2 text-sm">{{ $t->name }}</td>
                                <td class="px-3 py-2 text-xs font-mono">{{ $t->modality }}</td>
                                <td class="px-3 py-2 text-xs">{{ $t->room ?? '-' }}</td>
                                <td class="px-3 py-2 text-xs">{{ ucfirst($t->priority) }}</td>
                                <td class="px-3 py-2 text-right">
                                    <form method="POST" action="{{ route('settings.destroy-template', $t) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Delete?')" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-6 text-center text-gray-400 text-sm">No templates.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <details class="border-t border-gray-200 pt-4">
                    <summary class="text-sm text-blue-600 cursor-pointer hover:text-blue-800 font-medium">+ Add Template</summary>
                    <form method="POST" action="{{ route('settings.store-template') }}" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Modality</label>
                            <select name="modality" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="DX">DX</option><option value="CT">CT</option><option value="MR">MR</option>
                                <option value="US">US</option><option value="CR">CR</option><option value="MG">MG</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <input type="text" name="description" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Room</label>
                            <input type="text" name="room"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Priority</label>
                            <select name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                <option value="routine">Routine</option><option value="urgent">Urgent</option><option value="stat">STAT</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sort Order</label>
                            <input type="number" name="sort_order" value="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Save</button>
                        </div>
                    </form>
                </details>
            </div>
        </div>
    </div>
</x-app-layout>
