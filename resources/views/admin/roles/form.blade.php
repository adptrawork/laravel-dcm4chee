<x-app-layout>
    @section('title', isset($role) ? 'Edit Role' : 'New Role')
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <x-ui.heading level="h1" size="xl">{{ isset($role) ? 'Edit Role' : 'New Role' }}</x-ui.heading>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <form method="POST" action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" class="space-y-4">
                    @csrf
                    @if(isset($role)) @method('PUT') @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                        <x-ui.input name="name" value="{{ old('name', $role->name ?? '') }}" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                        <div class="space-y-3 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-3">
                            @foreach($permissions as $group => $groupPerms)
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ $group }}</p>
                                    @foreach($groupPerms as $perm)
                                        <label class="flex items-center gap-2 text-sm py-0.5">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                {{ in_array($perm->id, old('permissions', $role?->permissions->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300">
                                            {{ $perm->name }}
                                        </label>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <a href="{{ route('admin.roles.index') }}"><x-ui.button variant="secondary" type="button">Cancel</x-ui.button></a>
                        <x-ui.button variant="primary">Save</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
