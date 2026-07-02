<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $server->exists ? 'Edit Server' : 'Add Server' }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form action="{{ $server->exists ? route('servers.update', $server) : route('servers.store') }}" method="POST">
                    @csrf
                    @if($server->exists)
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Server Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $server->name) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                placeholder="e.g. Production PACS">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="base_url" class="block text-sm font-medium text-gray-700 mb-1">Base URL</label>
                            <input type="url" id="base_url" name="base_url" value="{{ old('base_url', $server->base_url) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                placeholder="https://192.168.1.100:8443">
                            @error('base_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400">The full URL including port. Example: https://localhost:8443</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="archive" class="block text-sm font-medium text-gray-700 mb-1">Archive Name</label>
                                <input type="text" id="archive" name="archive" value="{{ old('archive', $server->archive ?? 'dcm4chee-arc') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                                    placeholder="dcm4chee-arc">
                                @error('archive') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="aet" class="block text-sm font-medium text-gray-700 mb-1">AET</label>
                                <input type="text" id="aet" name="aet" value="{{ old('aet', $server->aet ?? 'DCM4CHEE') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono"
                                    placeholder="DCM4CHEE">
                                @error('aet') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <input type="text" id="username" name="username" value="{{ old('username', $server->username) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="admin">
                                @error('username') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $server->exists ? 'Password (leave empty to keep)' : 'Password' }}
                                </label>
                                <input type="password" id="password" name="password"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    placeholder="{{ $server->exists ? '••••••••' : 'changeit' }}">
                                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="timeout" class="block text-sm font-medium text-gray-700 mb-1">Timeout (seconds)</label>
                                <input type="number" id="timeout" name="timeout" value="{{ old('timeout', $server->timeout ?? 30) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    min="5" max="120">
                                @error('timeout') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-end pb-2">
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="ssl_verify" value="1"
                                        {{ old('ssl_verify', $server->ssl_verify ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">SSL Verify</span>
                                </label>
                                <label class="flex items-center space-x-3 ml-6">
                                    <input type="checkbox" name="enabled" value="1"
                                        {{ old('enabled', $server->enabled ?? true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Enabled</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('servers.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                {{ $server->exists ? 'Update Server' : 'Add Server' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
