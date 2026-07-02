<x-app-layout>
    @section('title', 'Worklist')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Worklist</h2>
            <div class="flex items-center space-x-3">
                <form action="{{ route('worklist.set-server') }}" method="POST" class="flex items-center space-x-2">
                    @csrf
                    <select name="server_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm">
                        @foreach($servers as $srv)
                            <option value="{{ $srv->id }}" {{ $srv->id == $serverId ? 'selected' : '' }}>{{ $srv->name }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('worklist.refresh') }}" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition">Refresh</a>
            </div>
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
                <a href="{{ route('worklist.index') }}" class="px-3 py-1.5 text-xs rounded-lg {{ !$status ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">All</a>
                <a href="{{ route('worklist.index', ['status' => 'waiting']) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $status == 'waiting' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Waiting</a>
                <a href="{{ route('worklist.index', ['status' => 'in_progress']) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $status == 'in_progress' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">In Progress</a>
                <a href="{{ route('worklist.index', ['status' => 'completed']) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $status == 'completed' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Completed</a>
                <a href="{{ route('worklist.index', ['status' => 'cancelled']) }}" class="px-3 py-1.5 text-xs rounded-lg {{ $status == 'cancelled' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Cancelled</a>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Accession</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Patient</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Examination</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Modality</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Schedule</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 text-xs uppercase">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 text-xs uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $item->accession_number }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $item->patient_name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $item->procedure_description }}</td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-500">{{ $item->modality }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    @if($item->scheduled_date)
                                        {{ substr($item->scheduled_date, 0, 4) }}-{{ substr($item->scheduled_date, 4, 2) }}-{{ substr($item->scheduled_date, 6, 2) }}
                                        {{ substr($item->scheduled_time, 0, 2) }}:{{ substr($item->scheduled_time, 2, 2) }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = ['waiting' => 'bg-gray-100 text-gray-700', 'in_progress' => 'bg-yellow-100 text-yellow-700', 'completed' => 'bg-green-100 text-green-700', 'cancelled' => 'bg-red-100 text-red-700', 'sent' => 'bg-indigo-100 text-indigo-700', 'failed' => 'bg-red-100 text-red-700'];
                                        $color = $statusColors[$item->status] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $color }}">{{ str_replace('_', ' ', ucfirst($item->status)) }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($item->status === 'waiting')
                                        <form method="POST" action="{{ route('worklist.update-status', $item) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="cancelled">
                                            <button onclick="return confirm('Cancel this MWL?')" class="text-xs text-red-600 hover:text-red-800">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">No worklist items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
