<x-app-layout>
    @section('title', 'Log Viewer')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">Log Viewer</x-ui.heading>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400">{{ number_format($logSize / 1024, 1) }} KB</span>
                <a href="{{ route('utilities.log-viewer') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">↻ Refresh</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4 flex items-center gap-2">
                <form method="GET" class="flex items-center gap-2">
                    <label class="text-xs text-gray-500">Lines:</label>
                    <select name="lines" onchange="this.form.submit()" class="rounded-field border border-black/10 bg-white px-2 py-1 text-xs">
                        <option value="100" {{ request('lines', 500) == 100 ? 'selected' : '' }}>100</option>
                        <option value="500" {{ request('lines', 500) == 500 ? 'selected' : '' }}>500</option>
                        <option value="1000" {{ request('lines', 500) == 1000 ? 'selected' : '' }}>1000</option>
                        <option value="5000" {{ request('lines', 500) == 5000 ? 'selected' : '' }}>5000</option>
                    </select>
                </form>
                <span class="text-xs text-gray-400">Showing last {{ count($lines) }} lines</span>
            </div>

            <div class="bg-[#1e1e1e] rounded-xl border border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <pre class="text-xs leading-5 p-4 font-mono text-gray-300 max-h-[70vh] overflow-y-auto" style="tab-size: 2;">@if(count($lines))
@foreach($lines as $i => $line)
{{ str_pad($i + 1, 5, ' ', STR_PAD_LEFT) }}  {{ $line }}
@endforeach
@else<span class="text-gray-500">Log file is empty.</span>@endif</pre>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
