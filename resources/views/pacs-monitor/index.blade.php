<x-app-layout>
    @section('title', 'PACS Monitor')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">PACS Monitor</h2>
            <form action="{{ route('pacs-monitor.set-server') }}" method="POST" class="flex items-center space-x-2">
                @csrf
                <select name="server_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm">
                    @foreach($servers as $srv)
                        <option value="{{ $srv->id }}" {{ $srv->id == $serverId ? 'selected' : '' }}>{{ $srv->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 text-sm text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 rounded-lg">{{ $errors->first() }}</div>
            @endif

            <div class="flex space-x-2 mb-4">
                <a href="{{ route('pacs-monitor.index') }}" class="px-3 py-1.5 text-xs rounded-lg {{ !$statusFilter ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">All</a>
                <a href="{{ route('pacs-monitor.index', ['status' => 'waiting']) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $statusFilter == 'waiting' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Waiting</a>
                <a href="{{ route('pacs-monitor.index', ['status' => 'sent']) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $statusFilter == 'sent' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Sent</a>
                <a href="{{ route('pacs-monitor.index', ['status' => 'failed']) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $statusFilter == 'failed' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Failed</a>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Patient</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Examination</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Modality</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Accession</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Error</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 text-xs uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $item->patient_name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $item->procedure_description }}</td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-500">{{ $item->modality }}</td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-500">{{ $item->accession_number }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $colors = ['waiting' => 'bg-gray-100 text-gray-700', 'in_progress' => 'bg-yellow-100 text-yellow-700', 'completed' => 'bg-green-100 text-green-700', 'sent' => 'bg-indigo-100 text-indigo-700', 'failed' => 'bg-red-100 text-red-700'];
                                        $c = $colors[$item->status] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $c }}">{{ ucfirst($item->status) }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-red-500 max-w-[200px] truncate">{{ $item->error_message ?? '-' }}</td>
                                <td class="px-4 py-3 text-right space-x-1">
                                    @if($item->status === 'failed')
                                        <a href="{{ route('pacs-monitor.retry', $item) }}" class="text-xs text-blue-600 hover:text-blue-800">Retry</a>
                                    @endif
                                    @if(in_array($item->status, ['completed', 'sent']))
                                        <form method="POST" action="{{ route('pacs-monitor.update-status', $item) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="sent">
                                            <button class="text-xs text-green-600 hover:text-green-800">Mark Sent</button>
                                        </form>
                                    @endif
                                    @if($item->status === 'failed')
                                        <form method="POST" action="{{ route('pacs-monitor.update-status', $item) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="failed">
                                            <button class="text-xs text-red-600 hover:text-red-800">Mark Failed</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">No items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
