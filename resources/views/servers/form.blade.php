<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('servers.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">&larr; Back to Servers</a>
        <x-ui.heading level="h1" size="xl">{{ $server->exists ? 'Edit Server' : 'Add Server' }}</x-ui.heading>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <form action="{{ $server->exists ? route('servers.update', $server) : route('servers.store') }}" method="POST">
                    @csrf
                    @if($server->exists) @method('PUT') @endif

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Server Name</label>
                            <x-ui.input id="name" name="name" value="{{ old('name', $server->name) }}" placeholder="e.g. Production PACS" />
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="base_url" class="block text-sm font-medium text-gray-700 mb-1">Base URL</label>
                            <x-ui.input id="base_url" name="base_url" type="url" value="{{ old('base_url', $server->base_url) }}" placeholder="https://192.168.1.100:8443" />
                            @error('base_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400">The full URL including port.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="archive" class="block text-sm font-medium text-gray-700 mb-1">Archive Name</label>
                                <x-ui.input id="archive" name="archive" value="{{ old('archive', $server->archive ?? 'dcm4chee-arc') }}" class="font-mono" />
                                @error('archive') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="aet" class="block text-sm font-medium text-gray-700 mb-1">AET</label>
                                <x-ui.input id="aet" name="aet" value="{{ old('aet', $server->aet ?? 'DCM4CHEE') }}" class="font-mono" />
                                @error('aet') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <x-ui.input id="username" name="username" value="{{ old('username', $server->username) }}" placeholder="admin" />
                                @error('username') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ $server->exists ? 'Password (leave empty to keep)' : 'Password' }}</label>
                                <x-ui.input id="password" type="password" name="password" :revealable="true" placeholder="{{ $server->exists ? '••••••••' : 'changeit' }}" />
                                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="timeout" class="block text-sm font-medium text-gray-700 mb-1">Timeout (seconds)</label>
                                <x-ui.input id="timeout" type="number" name="timeout" value="{{ old('timeout', $server->timeout ?? 30) }}" min="5" max="120" />
                                @error('timeout') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex items-end pb-2 gap-6">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="ssl_verify" value="1" {{ old('ssl_verify', $server->ssl_verify ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">SSL Verify</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="enabled" value="1" {{ old('enabled', $server->enabled ?? true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Enabled</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('servers.index') }}" class="text-sm text-gray-600 hover:text-gray-800 font-medium">Cancel</a>
                            <x-ui.button type="submit" variant="primary">{{ $server->exists ? 'Update Server' : 'Add Server' }}</x-ui.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
