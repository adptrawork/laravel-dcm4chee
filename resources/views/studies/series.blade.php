<x-app-layout>
    @section('title', 'Series Instances')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <x-ui.heading level="h1" size="xl">Series Instances</x-ui.heading>
                <p class="text-sm text-gray-500">{{ $seriesInfo['Modality'] ?? '' }} - {{ $seriesInfo['SeriesDescription'] ?? 'Series' }}</p>
            </div>
            <a href="{{ route('studies.show', $studyUid) }}">
                <x-ui.button variant="outline" size="sm">&larr; Back to Series</x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Series #</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $seriesInfo['SeriesNumber'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Modality</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $seriesInfo['Modality'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Instances</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ count($instances) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <x-ui.heading level="h3" size="md">Instances ({{ count($instances) }})</x-ui.heading>
                </div>
                @if(!empty($instances))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instance UID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Number</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Preview</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($instances as $inst)
                                    @php $uid = \App\Services\Dcm4chee\DicomHelper::extractValue($inst, '00080018') @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 font-mono text-xs text-gray-500 max-w-md truncate">{{ $uid ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ \App\Services\Dcm4chee\DicomHelper::extractValue($inst, '00200013') ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('studies.metadata', [$studyUid, $seriesUid, $uid]) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Metadata</a>
                                                <a href="{{ route('studies.rendered', [$studyUid, $seriesUid, $uid]) }}" target="_blank">
                                                    <x-ui.button variant="primary" size="xs" color="green">View</x-ui.button>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-12 text-center text-sm text-gray-400">No instances found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500 text-sm">No instances found.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
