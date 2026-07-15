@props(['order'])
@php
    $flow = ['pending', 'scheduled', 'in-progress', 'completed', 'reported'];
    $idx = array_search($order->status, $flow);
    $cancelled = $order->status === 'cancelled';
@endphp
<div class="space-y-1">
    @foreach($flow as $i => $status)
        @php
            $done = $idx !== false && $i <= $idx;
            $current = $idx !== false && $i === $idx;
            $label = \App\Models\Order::STATUS_LABELS[$status] ?? $status;
        @endphp
        <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg border
            {{ $current ? 'bg-primary-50 border-primary-300' : ($done ? 'bg-green-50 border-green-300' : 'bg-white border-gray-200') }}">
            <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs
                {{ $current ? 'bg-primary-500 text-white' : ($done ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400') }}">
                @if($done)
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                @else
                    {{ $i + 1 }}
                @endif
            </div>
            <span class="text-sm font-medium {{ $current ? 'text-primary-700' : ($done ? 'text-green-700' : 'text-gray-500') }}">{{ $label }}</span>
            @if($current)
                <span class="ml-auto text-xs font-medium text-primary-600 bg-primary-100 px-2 py-0.5 rounded-full">Current</span>
            @elseif($done)
                <span class="ml-auto text-xs text-green-600">&#10003;</span>
            @endif
        </div>
    @endforeach
    @if($cancelled && $idx === false)
        <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg border bg-red-50 border-red-300">
            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <span class="text-sm font-medium text-red-700">Cancelled</span>
        </div>
    @endif
</div>
