<x-app-layout>
    @section('title', 'Procedures')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-ui.heading level="h1" size="xl">Procedure Catalog</x-ui.heading>
            <a href="{{ route('settings.procedures.create') }}">
                <x-ui.button variant="primary" size="sm">+ Add Procedure</x-ui.button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28">Code</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16">Modality</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28">Body Part</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Duration</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Active</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-20">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($procedures as $p)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $p->code }}</td>
                                    <td class="px-4 py-3 font-medium text-sm">{{ $p->name }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $p->modality }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $p->body_part ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $p->estimated_duration ? $p->estimated_duration . 'm' : '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ $p->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $p->is_active ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right space-x-2">
                                        <a href="{{ route('settings.procedures.edit', $p) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                        <form method="POST" action="{{ route('settings.procedures.destroy', $p) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button onclick="return confirm('Delete procedure {{ $p->code }}?')" class="text-xs text-red-600 hover:text-red-800 font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">No procedures configured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
