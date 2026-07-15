<x-filament-panels::page>
    @php
        $servers = \App\Models\Server::orderBy('name')->get();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon name="heroicon-o-cpu-chip" class="w-4 h-4" />
                    PACS Servers
                </div>
            </x-slot>
            <p class="text-2xl font-bold text-gray-600">{{ $servers->count() }}</p>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon name="heroicon-o-queue-list" class="w-4 h-4 text-warning-400" />
                    Queue
                </div>
            </x-slot>
            <div class="flex gap-4">
                <div>
                    <span class="text-lg font-bold {{ $queuePending > 0 ? 'text-warning-600' : 'text-gray-400' }}">{{ $queuePending }}</span>
                    <span class="text-xs text-gray-500 ml-1">pending</span>
                </div>
                <div>
                    <span class="text-lg font-bold {{ $queueFailed > 0 ? 'text-danger-600' : 'text-gray-400' }}">{{ $queueFailed }}</span>
                    <span class="text-xs text-gray-500 ml-1">failed</span>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon name="heroicon-o-check-circle" class="w-4 h-4 text-success-400" />
                    Quick Actions
                </div>
            </x-slot>
            <x-filament::button wire:click="testAll" color="primary" size="sm" icon="heroicon-o-play">
                Test All Servers
            </x-filament::button>
        </x-filament::section>
    </div>

    @if($queueFailed > 0)
        <x-filament::section class="mb-6">
            <x-slot name="heading">
                <div class="flex items-center gap-2 text-danger-700">
                    <x-filament::icon name="heroicon-o-exclamation-triangle" class="w-4 h-4" />
                    Failed Jobs ({{ $queueFailed }})
                </div>
            </x-slot>
            <div class="max-h-[300px] overflow-y-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-2 py-1 text-left font-medium text-gray-500">Queue</th>
                            <th class="px-2 py-1 text-left font-medium text-gray-500">Exception</th>
                            <th class="px-2 py-1 text-left font-medium text-gray-500 w-32">Failed at</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach(\Illuminate\Support\Facades\DB::table('failed_jobs')->orderBy('failed_at', 'desc')->take(10)->get() as $job)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-1.5 font-mono">{{ $job->queue }}</td>
                                <td class="px-2 py-1.5 break-all max-w-[400px]">{{ \Illuminate\Support\Str::limit(explode("\n", $job->exception)[0] ?? $job->exception, 120) }}</td>
                                <td class="px-2 py-1.5">{{ $job->failed_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif

    <div class="space-y-4">
        @forelse($servers as $server)
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            @if(isset($results[$server->id]))
                                <span class="w-2 h-2 rounded-full {{ $results[$server->id]['ok'] ? 'bg-success-500' : 'bg-danger-500' }}"></span>
                            @endif
                            <span>{{ $server->name }}</span>
                        </div>
                        <span class="text-xs font-normal text-gray-500">{{ $server->base_url }}</span>
                    </div>
                </x-slot>

                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        AET: <span class="font-mono">{{ $server->aet }}</span>
                        &middot; Timeout: {{ $server->timeout }}s
                    </div>

                    <x-filament::button
                        wire:click="testEcho({{ $server->id }})"
                        wire:loading.attr="disabled"
                        color="primary"
                        size="sm"
                        icon="heroicon-o-play">
                        Test
                    </x-filament::button>
                </div>

                @if(isset($results[$server->id]))
                    <div class="mt-4">
                        <div class="font-medium text-sm mb-1 {{ $results[$server->id]['ok'] ? 'text-success-700' : 'text-danger-700' }}">
                            {{ $results[$server->id]['ok'] ? 'Connected' : 'Failed' }}
                        </div>
                        <pre class="text-xs bg-black/5 p-3 rounded-lg leading-relaxed">@foreach($results[$server->id]['steps'] ?? [] as $step)
{{ $step }}
@endforeach</pre>
                    </div>
                @endif
            </x-filament::section>
        @empty
            <x-filament::section>
                <p class="text-gray-400 text-sm py-4 text-center">No PACS servers configured yet.</p>
            </x-filament::section>
        @endforelse
    </div>
</x-filament-panels::page>
