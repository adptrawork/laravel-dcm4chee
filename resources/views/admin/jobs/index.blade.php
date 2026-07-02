<x-app-layout>
    @section('title', 'Job Queue')
    <x-slot name="header">
        <x-ui.heading level="h1" size="xl">Job Queue</x-ui.heading>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <h3 class="text-sm font-semibold text-gray-700 mb-3">Pending Jobs</h3>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Queue</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Attempts</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($pendingJobs as $job)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ $job->queue }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $job->attempts }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $job->created_at }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400">No pending jobs.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <h3 class="text-sm font-semibold text-gray-700 mb-3">Failed Jobs</h3>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Queue</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Failed At</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase max-w-md">Exception</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-20">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($failedJobs as $job)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ $job->queue }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-600 font-mono max-w-xs truncate">{{ $job->class ?? class_basename(json_decode($job->payload, true)['displayName'] ?? '') }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $job->failed_at }}</td>
                                    <td class="px-4 py-3 text-xs text-red-500 max-w-md truncate">{{ $job->exception ? str($job->exception)->limit(120) : '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.jobs.retry', $job->id) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Retry</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">No failed jobs.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">{{ $failedJobs->links() }}</div>
        </div>
    </div>
</x-app-layout>
