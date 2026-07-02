<x-app-layout>
    @section('title', 'Audit Logs')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">Audit Logs</x-ui.heading>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.audit-logs.export', request()->only(['date_from', 'date_to'])) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Export CSV</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Method</label>
                        <select name="method" class="rounded-field border border-black/10 bg-white px-2 py-1.5 text-sm">
                            <option value="">All</option>
                            @foreach(['GET', 'POST', 'PUT', 'DELETE'] as $m)
                                <option {{ request('method') == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Endpoint</label>
                        <x-ui.input name="endpoint" value="{{ request('endpoint') }}" placeholder="search..." class="w-40" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <x-ui.input name="status_code" value="{{ request('status_code') }}" placeholder="e.g. 200" class="w-20" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                        <x-ui.input name="date_from" type="date" value="{{ request('date_from') }}" class="w-36" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                        <x-ui.input name="date_to" type="date" value="{{ request('date_to') }}" class="w-36" />
                    </div>
                    <button class="px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                    <a href="{{ route('admin.audit-logs.index') }}" class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-800">Clear</a>
                </form>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-36">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Endpoint</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Duration</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">User</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-2 text-xs text-gray-500">{{ $log->created_at }}</td>
                                    <td class="px-4 py-2">
                                        <span class="text-xs font-mono font-bold {{ $log->method === 'GET' ? 'text-green-600' : ($log->method === 'POST' ? 'text-blue-600' : ($log->method === 'DELETE' ? 'text-red-600' : 'text-yellow-600')) }}">{{ $log->method }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-xs text-gray-600 font-mono max-w-xs truncate">{{ $log->endpoint }}</td>
                                    <td class="px-4 py-2">
                                        <span class="text-xs font-mono {{ ($log->response_status ?? 0) >= 400 ? 'text-red-600' : 'text-green-600' }}">{{ $log->response_status ?? '-' }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-xs text-gray-500">{{ $log->duration_ms ? $log->duration_ms . 'ms' : '-' }}</td>
                                    <td class="px-4 py-2 text-xs text-gray-500">{{ $log->user?->name ?? 'System' }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <a href="{{ route('admin.audit-logs.show', $log) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">No audit logs.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">{{ $logs->links() }}</div>
        </div>
    </div>
</x-app-layout>
