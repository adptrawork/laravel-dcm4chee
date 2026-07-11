@props(['order'])
@php
    $steps = [
        'pending' => ['label' => 'Draft', 'icon' => 'heroicon-o-document', 'color' => 'gray'],
        'scheduled' => ['label' => 'Scheduled', 'icon' => 'heroicon-o-calendar', 'color' => 'warning'],
        'in-progress' => ['label' => 'In Progress', 'icon' => 'heroicon-o-arrow-path', 'color' => 'info'],
        'completed' => ['label' => 'Completed', 'icon' => 'heroicon-o-check-circle', 'color' => 'success'],
        'reported' => ['label' => 'Reported', 'icon' => 'heroicon-o-document-text', 'color' => 'purple'],
    ];

    $current = $order->status;
    $found = false;
    $isCancelled = $current === 'cancelled';

    $statusOrder = array_keys($steps);
    $currentIndex = array_search($current, $statusOrder);
    if ($currentIndex === false) $currentIndex = -1;
@endphp

<div class="flex items-start">
    @foreach($steps as $key => $step)
        @php
            $stepIndex = array_search($key, $statusOrder);
            $done = $stepIndex <= $currentIndex && !$isCancelled;
            $active = $key === $current;
            $doneColor = match($step['color']) {
                'warning' => 'text-yellow-600',
                'info' => 'text-blue-600',
                'success' => 'text-green-600',
                'purple' => 'text-purple-600',
                default => 'text-gray-600',
            };
            $circleColor = $done ? ($active ? $doneColor : 'text-gray-400') : 'text-gray-300';
            $labelColor = $done ? 'text-gray-900' : 'text-gray-400';
        @endphp

        {{-- Connector line --}}
        @if(!$loop->first)
            <div class="flex-1 min-w-[1rem] h-px mt-3 {{ $done && !$isCancelled ? 'bg-gray-400' : 'bg-gray-200' }}"></div>
        @endif

        {{-- Step --}}
        <div class="flex flex-col items-center flex-1">
            <x-filament::icon :name="$step['icon']" class="w-5 h-5 {{ $circleColor }}" />
            <span class="text-xs mt-1 text-center {{ $labelColor }}">{{ $step['label'] }}</span>
            @if($active && !$isCancelled)
                <span class="text-[10px] text-gray-400 mt-0.5">{{ $order->updated_at?->format('H:i') }}</span>
            @endif
        </div>
    @endforeach
</div>

@if($isCancelled)
    <div class="mt-3 text-center">
        <x-filament::badge color="danger" icon="heroicon-o-x-circle">Cancelled</x-filament::badge>
    </div>
@endif