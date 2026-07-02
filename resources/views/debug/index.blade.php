<x-app-layout>
    @section('title', 'API Debug')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">API Debug</h2>
            <form action="{{ route('debug.set-server') }}" method="POST" class="flex items-center space-x-2">
                @csrf
                <select name="server_id" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($servers as $srv)
                        <option value="{{ $srv->id }}" {{ $srv->id == $serverId ? 'selected' : '' }}>{{ $srv->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Test Cases -->
            @php $groups = collect($testCases)->groupBy('group') @endphp
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Test Cases</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
                    @foreach($groups as $group => $cases)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide px-2 pt-1 pb-2">{{ $group }}</p>
                            <div class="space-y-1">
                                @foreach($cases as $key => $tc)
                                    <button type="button"
                                        onclick="fillForm('{{ $key }}')"
                                        class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-blue-50 hover:text-blue-700 transition text-gray-600"
                                        data-key="{{ $key }}"
                                        data-method="{{ $tc['method'] }}"
                                        data-path="{{ $tc['path'] }}"
                                        data-headers="{{ $tc['headers'] }}"
                                        data-body="{{ $tc['body'] }}">
                                        <span class="inline-block w-10 font-mono font-bold
                                            {{ $tc['method'] == 'GET' ? 'text-green-600' : '' }}
                                            {{ $tc['method'] == 'POST' ? 'text-blue-600' : '' }}
                                            {{ $tc['method'] == 'PUT' ? 'text-orange-600' : '' }}
                                            {{ $tc['method'] == 'DELETE' ? 'text-red-600' : '' }}">
                                            {{ $tc['method'] }}
                                        </span>
                                        <span>{{ $tc['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Request Builder</h3>

                        <form method="POST" action="{{ route('debug.send') }}">
                            @csrf
                            <input type="hidden" name="server_id" value="{{ $serverId }}">

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Method</label>
                                    <select name="method" id="input-method" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="GET" {{ ($oldMethod ?? 'GET') == 'GET' ? 'selected' : '' }}>GET</option>
                                        <option value="POST" {{ ($oldMethod ?? '') == 'POST' ? 'selected' : '' }}>POST</option>
                                        <option value="PUT" {{ ($oldMethod ?? '') == 'PUT' ? 'selected' : '' }}>PUT</option>
                                        <option value="DELETE" {{ ($oldMethod ?? '') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Path</label>
                                    <input type="text" name="path" id="input-path" value="{{ $oldPath ?? '' }}"
                                        class="w-full rounded-lg border-gray-300 text-sm font-mono focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="patients?limit=5">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Headers <span class="text-gray-400 font-normal">(one per line, Key: Value)</span></label>
                                    <textarea name="headers" id="input-headers" rows="4" class="w-full rounded-lg border-gray-300 text-xs font-mono focus:border-blue-500 focus:ring-blue-500" placeholder="Accept: application/dicom+json">{{ $oldHeaders ?? '' }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Request Body</label>
                                    <textarea name="body" id="input-body" rows="8" class="w-full rounded-lg border-gray-300 text-xs font-mono focus:border-blue-500 focus:ring-blue-500" placeholder='{"00100020":{"vr":"LO","Value":["TEST"]}}'>{{ $oldBody ?? '' }}</textarea>
                                </div>

                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition font-medium">
                                    Send Request
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-400">
                                <strong>Tips:</strong>
                            </p>
                            <ul class="mt-1 text-xs text-gray-400 list-disc list-inside space-y-0.5">
                                <li>Click test case untuk isi form otomatis</li>
                                <li>Ganti <code class="bg-gray-100 px-1 rounded">{id}</code> dengan nilai asli dari hasil query sebelumnya</li>
                                <li>Hasil query bisa dipakai sebagai input test case berikutnya</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-700">Response</h3>
                            @isset($result['status'])
                                <div class="flex items-center space-x-3">
                                    <span class="text-xs text-gray-400">{{ $result['duration'] }}ms</span>
                                    <span class="px-2.5 py-1 text-xs rounded-full {{ $result['status'] < 400 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $result['status'] ?? 'Error' }}
                                    </span>
                                </div>
                            @endisset
                        </div>
                        <div class="p-6">
                            @isset($result['error'])
                                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                                    {{ $result['error'] }}
                                </div>
                            @elseisset($result['body'])
                                <pre class="text-xs leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-200 overflow-x-auto max-h-[600px]"><code>{{ $result['body'] }}</code></pre>

                                @if(!empty($result['headers']))
                                    <details class="mt-4">
                                        <summary class="text-sm text-gray-600 cursor-pointer hover:text-gray-800">Response Headers</summary>
                                        <pre class="mt-2 text-xs text-gray-500 bg-gray-50 p-3 rounded-lg overflow-x-auto"><code>@foreach($result['headers'] as $key => $values)
{{ $key }}: {{ is_array($values) ? implode(', ', $values) : $values }}
@endforeach</code></pre>
                                    </details>
                                @endif
                            @else
                                <div class="text-center py-12 text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm">Pilih test case atau isi manual, lalu klik <strong>Send Request</strong>.</p>
                                </div>
                            @endisset
                        </div>
                    </div>

                    @isset($result['body'])
                        <div class="mt-4 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <p class="text-xs text-gray-500">
                                <strong>Request URL:</strong>
                                <code class="ml-1 font-mono">{{ \App\Models\Server::find($serverId)?->api_base_url }}/{{ $oldPath ?? '' }}</code>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                <strong>Tip:</strong> Copy UID dari hasil response untuk test case selanjutnya.
                            </p>
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function fillForm(key) {
        const btn = document.querySelector(`[data-key="${key}"]`);
        if (!btn) return;

        document.getElementById('input-method').value = btn.dataset.method;
        document.getElementById('input-path').value = btn.dataset.path;
        document.getElementById('input-headers').value = btn.dataset.headers;
        document.getElementById('input-body').value = btn.dataset.body;

        // Highlight active test case
        document.querySelectorAll('[data-key]').forEach(el => {
            el.classList.remove('bg-blue-50', 'text-blue-700');
            el.classList.add('text-gray-600');
        });
        btn.classList.remove('text-gray-600');
        btn.classList.add('bg-blue-50', 'text-blue-700');
    }

    // Auto-fill from previous response if old values exist
    @if(isset($oldMethod) && isset($oldPath))
        // Highlight matching test case if any
        document.querySelectorAll('[data-key]').forEach(el => {
            if (el.dataset.method === '{{ $oldMethod }}' && el.dataset.path === '{{ $oldPath }}') {
                el.classList.remove('text-gray-600');
                el.classList.add('bg-blue-50', 'text-blue-700');
            }
        });
    @endif
    </script>
    @endpush
</x-app-layout>
