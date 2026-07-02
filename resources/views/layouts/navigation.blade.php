@php
    $navGroups = [
        'Main' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['label' => 'Registrasi Pasien', 'route' => 'registration.index', 'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
            ['label' => 'Worklist', 'route' => 'worklist.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label' => 'Pemeriksaan', 'route' => 'studies.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'PACS Monitor', 'route' => 'pacs-monitor.index', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
        ],
        'Configuration' => [
            ['label' => 'Devices', 'route' => 'devices.index', 'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
            ['label' => 'Servers', 'route' => 'servers.index', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
            ['label' => 'Settings', 'route' => 'settings.index', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
        ],
    ];
    $activeServer = session('active_server_id')
        ? \App\Models\Server::find(session('active_server_id'))
        : \App\Models\Server::where('enabled', true)->first();
@endphp

<div x-data="{ open: false }"
     class="fixed inset-y-0 left-0 z-30 w-64 bg-[#111827] transform transition-transform duration-200 ease-in-out lg:translate-x-0 {{ request()->routeIs('login') || request()->routeIs('register') ? '-translate-x-full' : '' }}"
     :class="{ '-translate-x-full': !open, 'translate-x-0': open }">
    <div class="flex items-center h-16 px-6 bg-gray-800">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <span class="text-white font-semibold text-lg">{{ \App\Models\SystemSetting::getHospitalName() }}</span>
        </a>
    </div>

    <nav class="mt-5 px-3 space-y-6 overflow-y-auto" style="max-height: calc(100vh - 8rem);">
        @foreach($navGroups as $group => $items)
            <div>
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $group }}</p>
                <div class="mt-2 space-y-1">
                    @foreach($items as $item)
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150
                                   {{ request()->routeIs($item['route'] . '*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    @if($activeServer)
        <div class="absolute bottom-0 left-0 right-0 p-4 space-y-2 bg-gray-800">
            <a href="{{ rtrim($activeServer->base_url, '/') }}/dcm4chee-arc/ui2/en/study/study"
               target="_blank"
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Open DCM4CHEE UI
            </a>
            <div class="flex items-center px-3 py-2 text-sm text-gray-400">
                <div class="w-2 h-2 rounded-full bg-green-400 mr-2"></div>
                <span>{{ $activeServer->name }}</span>
        </div>
    </div>
    @endif
</div>

<form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>

<div class="lg:hidden fixed top-0 left-0 z-40 p-4">
    <button @click="open = !open" class="text-gray-500 hover:text-gray-700 focus:outline-none">
        <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<div class="lg:hidden bg-white border-b border-gray-200">
    <div class="flex justify-end items-center h-16 px-4">
        <x-ui.dropdown position="bottom-end">
            <x-slot name="button">
                <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                    <div>{{ Auth::user()->name }}</div>
                    <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>
            <x-slot name="menu">
                <x-ui.dropdown.item :href="route('profile.edit')">{{ __('Profile') }}</x-ui.dropdown.item>
                <x-ui.dropdown.item :href="route('logout')" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Log Out') }}</x-ui.dropdown.item>
            </x-slot>
        </x-ui.dropdown>
    </div>
</div>

<div class="hidden lg:flex bg-white border-b border-gray-200">
    <div class="flex justify-end items-center h-16 px-8 ml-auto">
        <x-ui.dropdown position="bottom-end">
            <x-slot name="button">
                <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                    <div>{{ Auth::user()->name }}</div>
                    <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>
            <x-slot name="menu">
                <x-ui.dropdown.item :href="route('profile.edit')">{{ __('Profile') }}</x-ui.dropdown.item>
                <x-ui.dropdown.item :href="route('logout')" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Log Out') }}</x-ui.dropdown.item>
            </x-slot>
        </x-ui.dropdown>
    </div>
</div>
