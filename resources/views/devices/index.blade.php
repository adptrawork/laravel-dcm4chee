<x-app-layout>
    @section('title', 'Devices')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">Devices</x-ui.heading>
            <a href="{{ route('devices.create') }}">
                <x-ui.button variant="primary" size="sm">+ Add Device</x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">AE Title</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Host:Port</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Modality</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($devices as $device)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-sm">{{ $device->name }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $device->ae_title }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $device->hostname }}:{{ $device->port }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $device->modality ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @php $sc = ['online' => 'bg-green-100 text-green-700', 'offline' => 'bg-red-100 text-red-700', 'unknown' => 'bg-gray-100 text-gray-700']; @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ $sc[$device->status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($device->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right space-x-2">
                                        <a href="{{ route('devices.echo', $device) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">C-ECHO</a>
                                        <a href="{{ route('devices.edit', $device) }}" class="text-xs text-gray-600 hover:text-gray-800 font-medium">Edit</a>
                                        <form method="POST" action="{{ route('devices.destroy', $device) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button onclick="return confirm('Delete?')" class="text-xs text-red-600 hover:text-red-800 font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400">No devices configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
