<x-app-layout>
    @section('title', isset($procedure) ? 'Edit Procedure' : 'Add Procedure')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">{{ isset($procedure) ? 'Edit Procedure' : 'Add Procedure' }}</x-ui.heading>
            <a href="{{ route('settings.procedures.index') }}">
                <x-ui.button variant="outline" size="sm">&larr; Back</x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <form method="POST" action="{{ isset($procedure) ? route('settings.procedures.update', $procedure) : route('settings.procedures.store') }}">
                    @csrf
                    @if(isset($procedure)) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                            <x-ui.input name="code" value="{{ old('code', $procedure->code ?? '') }}" placeholder="THORAX-AP" required class="font-mono uppercase" />
                            @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <x-ui.input name="name" value="{{ old('name', $procedure->name ?? '') }}" placeholder="Thorax AP" required />
                            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Modality</label>
                            <select name="modality"
                                class="w-full rounded-field border border-black/10 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                @foreach(['DX','CT','MR','US','CR','MG','XA','RF','NM','PT','OT'] as $m)
                                    <option value="{{ $m }}" {{ old('modality', $procedure->modality ?? '') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Body Part</label>
                            <x-ui.input name="body_part" value="{{ old('body_part', $procedure->body_part ?? '') }}" placeholder="CHEST" class="uppercase" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Duration (minutes)</label>
                            <x-ui.input type="number" name="estimated_duration" value="{{ old('estimated_duration', $procedure->estimated_duration ?? '') }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Default Physician</label>
                            <x-ui.input name="default_physician" value="{{ old('default_physician', $procedure->default_physician ?? '') }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Default Room</label>
                            <x-ui.input name="default_room" value="{{ old('default_room', $procedure->default_room ?? '') }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Default Exposure</label>
                            <x-ui.input name="default_exposure" value="{{ old('default_exposure', $procedure->default_exposure ?? '') }}" placeholder="kV/mAs" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <x-ui.input type="number" name="sort_order" value="{{ old('sort_order', $procedure->sort_order ?? 0) }}" />
                        </div>
                        <div class="flex items-center gap-2 pt-6">
                            <input type="checkbox" name="requires_contrast" value="1" id="rc" class="rounded border-gray-300"
                                {{ old('requires_contrast', $procedure->requires_contrast ?? false) ? 'checked' : '' }}>
                            <label for="rc" class="text-sm text-gray-700">Requires Contrast</label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2"
                            class="w-full rounded-field border border-black/10 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('description', $procedure->description ?? '') }}</textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contrast Detail</label>
                        <textarea name="contrast_detail" rows="2"
                            class="w-full rounded-field border border-black/10 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('contrast_detail', $procedure->contrast_detail ?? '') }}</textarea>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <x-ui.button type="submit" variant="primary">{{ isset($procedure) ? 'Update' : 'Create' }}</x-ui.button>
                        <a href="{{ route('settings.procedures.index') }}">
                            <x-ui.button type="button" variant="outline">Cancel</x-ui.button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
