@props(['studyUid', 'label' => 'Buka di OHIF'])
@php
    if (!$studyUid) return;
    $url = config('services.ohif.url', 'http://localhost:3000') . '/viewer?StudyInstanceUIDs=' . $studyUid;
@endphp
<a href="{{ $url }}" target="_blank" rel="noopener"
   {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors']) }}>
    <x-filament::icon name="heroicon-o-eye" class="w-4 h-4" />
    {{ $label }}
</a>