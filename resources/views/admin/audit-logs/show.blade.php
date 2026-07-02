<x-app-layout>
    @section('title', 'Audit Log Detail')
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.audit-logs.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <x-ui.heading level="h1" size="xl">Audit Log Detail</x-ui.heading>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Date</p>
                        <p class="text-sm text-gray-800">{{ $auditLog->created_at }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">User</p>
                        <p class="text-sm text-gray-800">{{ $auditLog->user?->name ?? 'System' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Server</p>
                        <p class="text-sm text-gray-800">{{ $auditLog->server?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Method</p>
                        <p class="text-sm font-mono text-gray-800">{{ $auditLog->method }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Status</p>
                        <p class="text-sm font-mono {{ ($auditLog->response_status ?? 0) >= 400 ? 'text-red-600' : 'text-green-600' }}">{{ $auditLog->response_status ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Duration</p>
                        <p class="text-sm text-gray-800">{{ $auditLog->duration_ms ? $auditLog->duration_ms . ' ms' : '-' }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">URL</p>
                    <p class="text-sm font-mono text-gray-800 break-all">{{ $auditLog->url ?? $auditLog->endpoint }}</p>
                </div>
                @if($auditLog->request_body)
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Request Body</p>
                        <pre class="text-xs bg-gray-50 rounded-lg p-3 max-h-60 overflow-auto">{{ $auditLog->request_body }}</pre>
                    </div>
                @endif
                @if($auditLog->response_body)
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Response Body</p>
                        <pre class="text-xs bg-gray-50 rounded-lg p-3 max-h-60 overflow-auto">{{ $auditLog->response_body }}</pre>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
