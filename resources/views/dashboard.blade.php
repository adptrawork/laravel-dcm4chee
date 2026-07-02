<x-app-layout>
    @section('title', 'Dashboard')
    <x-slot name="header">
        <x-ui.heading level="h1" size="xl">Dashboard</x-ui.heading>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                <div class="bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm">
                    <p class="text-2xl font-bold text-blue-700">{{ $stats['registered'] ?? 0 }}</p>
                    <p class="text-xs text-blue-600 font-medium mt-1">Registered</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm">
                    <p class="text-2xl font-bold text-indigo-700">{{ $stats['mw_published'] ?? 0 }}</p>
                    <p class="text-xs text-indigo-600 font-medium mt-1">MWL Published</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm">
                    <p class="text-2xl font-bold text-yellow-700">{{ $stats['acquiring'] ?? 0 }}</p>
                    <p class="text-xs text-yellow-600 font-medium mt-1">Acquiring</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm">
                    <p class="text-2xl font-bold text-orange-700">{{ $stats['acquired'] ?? 0 }}</p>
                    <p class="text-xs text-orange-600 font-medium mt-1">Acquired</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm">
                    <p class="text-2xl font-bold text-green-700">{{ $stats['archived'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 font-medium mt-1">Archived</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 text-center shadow-sm">
                    <p class="text-2xl font-bold text-gray-700">{{ $stats['dicom_status'] ?? '?' }}</p>
                    <p class="text-xs text-gray-600 font-medium mt-1">DCM4CHEE</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Recent Activity</h3>
                    @if($recentItems->count())
                        <div class="space-y-2">
                            @foreach($recentItems as $item)
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2">
                                        @php $dotColor = match($item->status) { 'sent_to_pacs', 'archived', 'reported', 'verified' => 'bg-green-400', 'failed', 'cancelled' => 'bg-red-400', 'acquiring' => 'bg-yellow-400', default => 'bg-gray-400' }; @endphp
                                        <span class="w-2 h-2 rounded-full {{ $dotColor }}"></span>
                                        <span class="text-gray-600">{{ $item->patient_name }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ $item->procedure_description }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-8">No activity yet.</p>
                    @endif
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('registration.index') }}" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                            <span class="text-blue-700 text-sm font-medium">Register New Patient</span>
                            <span class="ml-auto text-xs text-blue-500">→</span>
                        </a>
                        <a href="{{ route('worklist.index') }}" class="flex items-center p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                            <span class="text-yellow-700 text-sm font-medium">View Worklist</span>
                            <span class="ml-auto text-xs text-yellow-500">→</span>
                        </a>
                        <a href="{{ route('pacs-monitor.index') }}" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                            <span class="text-green-700 text-sm font-medium">PACS Monitor</span>
                            <span class="ml-auto text-xs text-green-500">→</span>
                        </a>
                        <a href="{{ route('studies.index') }}" class="flex items-center p-3 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                            <span class="text-indigo-700 text-sm font-medium">Browse Studies</span>
                            <span class="ml-auto text-xs text-indigo-500">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
