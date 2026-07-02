<x-app-layout>
    @section('title', 'MWL Queue')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">MWL Queue</x-ui.heading>
            <div class="flex items-center gap-3">
                <form action="{{ route('mwl-queue.set-server') }}" method="POST">
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

            <div class="flex gap-1 mb-4 flex-wrap">
                @foreach(['' => 'All', \App\Models\WorklistItem::STATUS_REGISTERED => 'Registered', \App\Models\WorklistItem::STATUS_MW_PUBLISHED => 'Published', \App\Models\WorklistItem::STATUS_TAKEN_BY_MODALITY => 'Taken'] as $val => $label)
                    @php $valClean = $val ?: ''; @endphp
                    <a href="{{ route('mwl-queue.index', $val ? ['status' => $val] : []) }}"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                            {{ ($status ?? '') == $val ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-[130px]">Accession</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Examination</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16">Modality</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Schedule</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-20">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($items as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $item->accession_number }}</td>
                                    <td class="px-4 py-3 font-medium text-sm text-gray-800">{{ $item->patient_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $item->procedure_description }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $item->modality }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">
                                        @if($item->scheduled_date)
                                            {{ substr($item->scheduled_date, 0, 4) }}-{{ substr($item->scheduled_date, 4, 2) }}-{{ substr($item->scheduled_date, 6, 2) }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ \App\Models\WorklistItem::statusColor($item->status) }}">
                                            {{ \App\Models\WorklistItem::statusLabel($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if(in_array($item->status, ['registered', 'mw_published']))
                                            <form method="POST" action="{{ route('mwl-queue.renew', $item) }}" class="inline">
                                                @csrf
                                                <button class="text-xs text-blue-600 hover:text-blue-800 font-medium">Re-publish</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">No MWL items.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
