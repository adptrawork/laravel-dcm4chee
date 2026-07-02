<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">Server Configuration</x-ui.heading>
            <a href="{{ route('servers.create') }}">
                <x-ui.button variant="primary" size="sm">+ Add Server</x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            @if($servers->isEmpty())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                    </svg>
                    <x-ui.heading level="h3" size="md" class="mt-4">No servers configured</x-ui.heading>
                    <p class="mt-2 text-sm text-gray-500">Add your first DCM4CHEE server to get started.</p>
                    <a href="{{ route('servers.create') }}" class="mt-4 inline-block">
                        <x-ui.button variant="primary">Add Server</x-ui.button>
                    </a>
                </div>
            @else
                <div class="grid gap-4">
                    @foreach($servers as $server)
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg {{ $server->enabled ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                                        <svg class="w-5 h-5 {{ $server->enabled ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                        </svg>
                                    </div>
                                    <div>
                                        <x-ui.heading level="h3" size="md" class="!text-gray-900">{{ $server->name }}</x-ui.heading>
                                        <p class="text-sm text-gray-500">{{ $server->base_url }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ $server->enabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $server->enabled ? 'Active' : 'Disabled' }}
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-4 text-sm">
                                <div><span class="text-gray-500">Archive:</span> <span class="ml-1 font-mono">{{ $server->archive }}</span></div>
                                <div><span class="text-gray-500">AET:</span> <span class="ml-1 font-mono">{{ $server->aet }}</span></div>
                                <div><span class="text-gray-500">Timeout:</span> <span class="ml-1">{{ $server->timeout }}s</span></div>
                            </div>

                            <div class="mt-4 flex items-center gap-2">
                                <form action="{{ route('servers.test', $server) }}" method="POST" class="inline">
                                    @csrf
                                    <x-ui.button type="submit" variant="outline" size="sm">Test Connection</x-ui.button>
                                </form>
                                <a href="{{ route('servers.edit', $server) }}">
                                    <x-ui.button variant="outline" size="sm">Edit</x-ui.button>
                                </a>
                                <form action="{{ route('servers.destroy', $server) }}" method="POST" class="inline" onsubmit="return confirm('Delete this server configuration?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="submit" variant="outline" size="sm" color="red">Delete</x-ui.button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
