<x-app-layout>
    @section('title', 'Settings')
    <x-slot name="header">
        <x-ui.heading level="h1" size="xl">Settings</x-ui.heading>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <x-ui.heading level="h2" size="sm" class="mb-4">System Settings</x-ui.heading>
                <form method="POST" action="{{ route('settings.update-system') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hospital Name</label>
                            <x-ui.input name="hospital_name" value="{{ \App\Models\SystemSetting::get('hospital_name') }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                            <x-ui.input name="timezone" value="{{ \App\Models\SystemSetting::get('timezone', config('app.timezone')) }}" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="hospital_address" rows="2" class="w-full rounded-field border border-black/10 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ \App\Models\SystemSetting::get('hospital_address') }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-ui.button type="submit" variant="primary">Save</x-ui.button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <x-ui.heading level="h2" size="sm" class="mb-4">MWL Configuration</x-ui.heading>
                @php $mwlConfig = null; @endphp
                @if(($serverId = $servers->first()?->id) && $servers->count())
                    @php $mwlConfig = \App\Models\MwlConfig::where('server_id', $serverId)->first(); @endphp
                @endif
                <form method="POST" action="{{ route('settings.update-mwl') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Server</label>
                            <select name="server_id"
                                class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                @foreach($servers as $srv)
                                    <option value="{{ $srv->id }}" {{ old('server_id', $mwlConfig->server_id ?? '') == $srv->id ? 'selected' : '' }}>{{ $srv->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Default Station AE</label>
                            <x-ui.input name="default_aet" value="{{ old('default_aet', $mwlConfig->default_aet ?? 'PZDR') }}" class="font-mono" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Default Modality</label>
                            <x-ui.input name="default_modality" value="{{ old('default_modality', $mwlConfig->default_modality ?? '') }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Default Physician</label>
                            <x-ui.input name="default_physician" value="{{ old('default_physician', $mwlConfig->default_physician ?? '') }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Default Room</label>
                            <x-ui.input name="default_room" value="{{ old('default_room', $mwlConfig->default_room ?? '') }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Default Institution</label>
                            <x-ui.input name="default_institution" value="{{ old('default_institution', $mwlConfig->default_institution ?? '') }}" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-ui.button type="submit" variant="primary">Save</x-ui.button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <x-ui.heading level="h2" size="sm">Examination Templates</x-ui.heading>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Modality</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Priority</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-20">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($templates as $t)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm">{{ $t->name }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $t->modality }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $t->room ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($t->priority) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('settings.destroy-template', $t) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button onclick="return confirm('Delete?')" class="text-xs text-red-600 hover:text-red-800 font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-400">No templates.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <details class="border-t border-gray-200 pt-4">
                    <summary class="text-sm text-blue-600 cursor-pointer hover:text-blue-800 font-medium">+ Add Template</summary>
                    <form method="POST" action="{{ route('settings.store-template') }}" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <x-ui.input name="name" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Modality</label>
                            <select name="modality"
                                class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="DX">DX</option><option value="CT">CT</option><option value="MR">MR</option>
                                <option value="US">US</option><option value="CR">CR</option><option value="MG">MG</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <x-ui.input name="description" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                            <x-ui.input name="room" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select name="priority"
                                class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="routine">Routine</option><option value="urgent">Urgent</option><option value="stat">STAT</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <x-ui.input type="number" name="sort_order" value="0" />
                        </div>
                        <div class="md:col-span-2">
                            <x-ui.button type="submit" variant="primary">Save</x-ui.button>
                        </div>
                    </form>
                </details>
            </div>
        </div>
    </div>
</x-app-layout>
