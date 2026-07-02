<x-app-layout>
    @section('title', 'PACS Monitor')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">PACS Monitor</x-ui.heading>
            <div class="flex items-center gap-3">
                <a href="{{ route('studies.poll') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Poll Studies Now</a>
                <form action="{{ route('pacs-monitor.set-server') }}" method="POST">
                    @csrf
                    <select name="server_id" onchange="this.form.submit()"
                        class="rounded-field border border-black/10 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        @foreach($servers as $srv)
                            <option value="{{ $srv->id }}" {{ $srv->id == $serverId ? 'selected' : '' }}>{{ $srv->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="flex gap-1 mb-4 flex-wrap">
                @foreach(['' => 'All', \App\Models\WorklistItem::STATUS_MW_PUBLISHED => 'Published', \App\Models\WorklistItem::STATUS_SENT_TO_PACS => 'Sent', \App\Models\WorklistItem::STATUS_FAILED => 'Failed', \App\Models\WorklistItem::STATUS_ARCHIVED => 'Archived'] as $val => $label)
                    <a href="{{ route('pacs-monitor.index', $val ? ['status' => $val] : []) }}"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                            {{ $statusFilter == $val ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Examination</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Modality</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-[130px]">Accession</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Error</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($items as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-sm text-gray-800">{{ $item->patient_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $item->procedure_description }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $item->modality }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $item->accession_number }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ \App\Models\WorklistItem::statusColor($item->status) }}">
                                            {{ \App\Models\WorklistItem::statusLabel($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-red-500 max-w-[200px] truncate">{{ $item->error_message ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right space-x-1">
                                        @if(in_array($item->status, [\App\Models\WorklistItem::STATUS_MW_PUBLISHED, \App\Models\WorklistItem::STATUS_ACQUIRED]))
                                            <form method="POST" action="{{ route('pacs-monitor.update-status', $item) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="sent_to_pacs">
                                                <button class="text-xs text-green-600 hover:text-green-800 font-medium">Mark Sent</button>
                                            </form>
                                        @endif
                                        @if(in_array($item->status, [\App\Models\WorklistItem::STATUS_MW_PUBLISHED, \App\Models\WorklistItem::STATUS_FAILED]))
                                            <form method="POST" action="{{ route('pacs-monitor.update-status', $item) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="failed">
                                                <button class="text-xs text-red-600 hover:text-red-800 font-medium">Mark Failed</button>
                                            </form>
                                        @endif
                                        @if($item->status === \App\Models\WorklistItem::STATUS_FAILED)
                                            <a href="{{ route('pacs-monitor.retry', $item) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Retry</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">No items.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
