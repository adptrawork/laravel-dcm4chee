<x-app-layout>
    @section('title', isset($user) ? 'Edit User' : 'New User')
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <x-ui.heading level="h1" size="xl">{{ isset($user) ? 'Edit User' : 'New User' }}</x-ui.heading>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" class="space-y-4">
                    @csrf
                    @if(isset($user)) @method('PUT') @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <x-ui.input name="name" value="{{ old('name', $user->name ?? '') }}" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <x-ui.input name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password{{ isset($user) ? ' (leave empty to keep)' : '' }}</label>
                        <x-ui.input name="password" type="password" {{ !isset($user) ? 'required' : '' }} />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <x-ui.input name="password_confirmation" type="password" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Roles</label>
                        <div class="space-y-1 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        {{ in_array($role->id, old('roles', $user?->roles->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300">
                                    {{ $role->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <a href="{{ route('admin.users.index') }}"><x-ui.button variant="secondary" type="button">Cancel</x-ui.button></a>
                        <x-ui.button variant="primary">Save</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
