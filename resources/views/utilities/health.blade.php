<x-app-layout>
    @section('title', 'System Health')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">System Health</x-ui.heading>
            <a href="{{ route('utilities.health') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">↻ Refresh</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm divide-y divide-gray-100">
                @foreach($checks as $key => $check)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full {{ $check['status'] ? 'bg-green-400' : 'bg-red-400' }}"></div>
                            <span class="text-sm font-medium text-gray-700">{{ $check['label'] }}</span>
                        </div>
                        <span class="text-xs text-gray-500 {{ $check['status'] ? '' : 'text-red-500' }}">{{ $check['detail'] }}</span>
                    </div>
                @endforeach
            </div>
            <p class="mt-3 text-xs text-gray-400 text-center">Last checked: {{ now()->format('d M Y H:i:s') }}</p>
        </div>
    </div>
</x-app-layout>
