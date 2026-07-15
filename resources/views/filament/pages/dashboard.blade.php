<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon name="heroicon-o-clock" class="w-4 h-4 text-gray-400" />
                    Orders Waiting
                </div>
            </x-slot>
            <p class="text-2xl font-bold text-gray-600">{{ $ordersWaiting }}</p>
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon name="heroicon-o-play" class="w-4 h-4 text-warning-400" />
                    In Progress
                </div>
            </x-slot>
            <p class="text-2xl font-bold text-info-600">{{ $ordersInProgress }}</p>
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon name="heroicon-o-document-text" class="w-4 h-4 text-success-400" />
                    Ready to Read
                </div>
            </x-slot>
            <p class="text-2xl font-bold text-success-600">{{ $readyToRead }}</p>
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon name="heroicon-o-flag" class="w-4 h-4 text-purple-400" />
                    Reported Today
                </div>
            </x-slot>
            <p class="text-2xl font-bold text-purple-600">{{ $reportedToday }}</p>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon name="heroicon-o-cpu-chip" class="w-4 h-4" />
                    PACS
                </div>
            </x-slot>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full {{ str_contains($pacsStatus, 'Connected') ? 'bg-success-500' : 'bg-danger-500' }}"></span>
                <span class="{{ str_contains($pacsStatus, 'Connected') ? 'text-success-700' : 'text-danger-700' }}">{{ $pacsStatus }}</span>
            </div>
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
                    <x-filament::icon name="heroicon-o-calendar" class="w-4 h-4 text-gray-400" />
                    Today
                </div>
            </x-slot>
            <div class="flex gap-4">
                <div>
                    <span class="text-lg font-bold text-warning-600">{{ $ordersScheduled }}</span>
                    <span class="text-xs text-gray-500 ml-1">scheduled</span>
                </div>
                <div>
                    <span class="text-lg font-bold text-purple-600">{{ $reportedToday }}</span>
                    <span class="text-xs text-gray-500 ml-1">reported</span>
                </div>
            </div>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-filament::section>
            <x-slot name="heading">Active Worklist</x-slot>
            {{ $this->table }}
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Recent Orders</x-slot>
            @php
                $recentOrders = \App\Models\Order::with('patient')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            @endphp
            @if($recentOrders->count() > 0)
                <div class="space-y-2">
                    @foreach($recentOrders as $order)
                        <div class="flex items-center justify-between py-1.5 border-b border-gray-100 last:border-0">
                            <div class="min-w-0 flex-1">
                                <div class="text-sm truncate">
                                    <a href="{{ url("/admin/orders/{$order->id}") }}" class="text-primary-600 hover:underline">
                                        {{ $order->accession_number }}
                                    </a>
                                    <span class="text-gray-500">—</span>
                                    {{ $order->patient?->full_name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                  style="color: {{ match($order->status) {
                                      'pending' => '#6b7280',
                                      'scheduled' => '#d97706',
                                      'in-progress' => '#2563eb',
                                      'completed' => '#16a34a',
                                      'reported' => '#9333ea',
                                      default => '#6b7280',
                                  } }}; background-color: {{ match($order->status) {
                                      'pending' => '#f3f4f6',
                                      'scheduled' => '#fef3c7',
                                      'in-progress' => '#dbeafe',
                                      'completed' => '#dcfce7',
                                      'reported' => '#f3e8ff',
                                      default => '#f3f4f6',
                                  } }}">
                                {{ \App\Models\Order::STATUS_LABELS[$order->status] ?? $order->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm py-4 text-center">No recent orders</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
