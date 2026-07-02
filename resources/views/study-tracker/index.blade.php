<x-app-layout>
    @section('title', 'Study Tracker')
    <x-slot name="header">
        <x-ui.heading level="h1" size="xl">Study Tracker</x-ui.heading>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
                <form method="GET" class="flex items-center gap-3">
                    <div class="flex-1">
                        <x-ui.input name="query" value="{{ $query }}" placeholder="Search by patient name, ID, or accession..." />
                    </div>
                    <select name="status" class="rounded-field border border-black/10 bg-white px-3 py-2 text-sm">
                        <option value="">All Status</option>
                        @foreach(\App\Models\WorklistItem::STATUSES as $s)
                            <option value="{{ $s }}" {{ $status == $s ? 'selected' : '' }}>{{ \App\Models\WorklistItem::statusLabel($s) }}</option>
                        @endforeach
                    </select>
                    <x-ui.button type="submit" variant="primary" size="sm">Search</x-ui.button>
                </form>
            </div>

            <div class="space-y-3">
                @forelse($items as $item)
                    <a href="{{ route('study-tracker.show', $item->accession_number) }}"
                        class="block bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:border-blue-300 transition-colors">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $item->patient_name }}</p>
                                <p class="text-sm text-gray-500 mt-0.5">
                                    <span class="font-mono">{{ $item->accession_number }}</span>
                                    &middot; {{ $item->modality }}
                                    &middot; {{ $item->procedure_description }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ \App\Models\WorklistItem::statusColor($item->status) }}">
                                    {{ \App\Models\WorklistItem::statusLabel($item->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center gap-4 text-xs text-gray-500">
                            <span>Patient ID: <span class="font-mono">{{ $item->patient_id }}</span></span>
                            @if($item->study_instance_uid)
                                <span>Study: <span class="font-mono text-green-600">Linked</span></span>
                            @endif
                            <span>Created: {{ $item->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @empty
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center text-sm text-gray-400">
                        No items found.
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $items->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
