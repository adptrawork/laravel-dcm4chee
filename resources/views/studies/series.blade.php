<x-app-layout>
    @section('title', 'Series Instances')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Series Instances</h2>
                <p class="text-sm text-gray-500">{{ $seriesInfo['Modality'] ?? '' }} - {{ $seriesInfo['SeriesDescription'] ?? 'Series' }}</p>
            </div>
            <a href="{{ route('studies.show', $studyUid) }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition">&larr; Back to Series</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Series #</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $seriesInfo['SeriesNumber'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Modality</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $seriesInfo['Modality'] ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Instances</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ count($instances) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Instances ({{ count($instances) }})</h3>
                </div>
                @if(!empty($instances))
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instance UID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Number</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Preview</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($instances as $inst)
                                @php $uid = \App\Services\Dcm4chee\DicomHelper::extractValue($inst, '00080018') @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-mono text-gray-500 max-w-md truncate">{{ $uid ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ \App\Services\Dcm4chee\DicomHelper::extractValue($inst, '00200013') ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('studies.metadata', [$studyUid, $seriesUid, $uid]) }}"
                                               class="text-sm text-blue-600 hover:text-blue-800">Metadata</a>
                                            <a href="{{ route('studies.rendered', [$studyUid, $seriesUid, $uid]) }}"
                                               class="text-sm text-green-600 hover:text-green-800"
                                               target="_blank">View</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-6 text-center text-gray-500 text-sm">No instances found.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
