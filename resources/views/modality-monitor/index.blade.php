<x-app-layout>
    @section('title', 'Modality Monitor')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">Modality Monitor</x-ui.heading>
            <div class="flex items-center gap-3">
                <form action="{{ route('modality-monitor.set-server') }}" method="POST">
                    @csrf
                    <select name="server_id" onchange="this.form.submit()"
                        class="rounded-field border border-black/10 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        @foreach($servers as $srv)
                            <option value="{{ $srv->id }}" {{ $srv->id == $serverId ? 'selected' : '' }}>{{ $srv->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            @if($devices->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                    <x-ui.heading level="h3" size="md" class="mt-4">No devices configured</x-ui.heading>
                    <p class="mt-2 text-sm text-gray-500">Add devices first in Configuration &rarr; Devices.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($devices as $device)
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <x-ui.heading level="h3" size="sm" class="!text-gray-900">{{ $device->name }}</x-ui.heading>
                                    <p class="text-xs font-mono text-gray-500 mt-0.5">{{ $device->ae_title }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full
                                    {{ $device->status === 'online' ? 'bg-green-100 text-green-700' : ($device->status === 'offline' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500') }}">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </div>

                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Last C-ECHO</span>
                                    <span class="font-mono text-xs {{ $device->last_echo_at && $device->last_echo_at->gt(now()->subMinutes(10)) ? 'text-green-600' : 'text-gray-400' }}">
                                        {{ $device->last_echo_at ? $device->last_echo_at->format('H:i') : 'Never' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Last MWL Query</span>
                                    <span class="font-mono text-xs text-gray-500">
                                        {{ $device->last_mwl_query_at ? $device->last_mwl_query_at->format('H:i') : 'Never' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Last Store</span>
                                    <span class="font-mono text-xs {{ $device->last_store_at && $device->last_store_at->gt(now()->subHours(1)) ? 'text-green-600' : 'text-gray-400' }}">
                                        {{ $device->last_store_at ? $device->last_store_at->format('H:i') : 'Never' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Queue</span>
                                    <span class="font-mono text-xs {{ $device->queue_count > 0 ? 'text-yellow-600 font-semibold' : 'text-gray-500' }}">
                                        {{ $device->queue_count }} MWL waiting
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-100">
                                <a href="{{ route('modality-monitor.ping', $device) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">C-ECHO Now</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
