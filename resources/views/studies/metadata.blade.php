<x-app-layout>
    @section('title', 'Instance Metadata')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Instance Metadata</h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('studies.rendered', [$studyUid, $seriesUid, $instanceUid]) }}"
                   target="_blank" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    View Image
                </a>
                <a href="{{ route('studies.series', [$studyUid, $seriesUid]) }}"
                   class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition">&larr; Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
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
