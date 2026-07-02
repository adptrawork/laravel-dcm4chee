<x-app-layout>
    @section('title', 'Instance Metadata')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">Instance Metadata</x-ui.heading>
            <div class="flex items-center gap-3">
                <a href="{{ route('studies.rendered', [$studyUid, $seriesUid, $instanceUid]) }}" target="_blank">
                    <x-ui.button variant="outline" size="sm">View Image</x-ui.button>
                </a>
                <a href="{{ route('studies.series', [$studyUid, $seriesUid]) }}">
                    <x-ui.button variant="outline" size="sm">&larr; Back</x-ui.button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <p class="text-xs text-gray-500 font-mono">{{ $instanceUid }}</p>
                </div>
                <div class="p-6 overflow-x-auto">
                    <pre class="text-xs leading-relaxed"><code>{{ json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
