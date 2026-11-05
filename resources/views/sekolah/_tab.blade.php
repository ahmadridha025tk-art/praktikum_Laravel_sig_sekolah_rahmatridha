@php
    $aktif = 'border-blue-700 text-blue-700';
    $biasa = 'border-transparent text-gray-600';
@endphp
<div class="mb-4 border-b flex gap-6">
    <a href="{{ route('sekolah.edit', $sekolah) }}"
       class="pb-2 border-b-2 {{ request()->routeIs('sekolah.edit') ? $aktif : $biasa }}">
        Data Sekolah
    </a>
    <a href="{{ route('sekolah.profil.edit', $sekolah) }}"
       class="pb-2 border-b-2 {{ request()->routeIs('sekolah.profil.edit') ? $aktif : $biasa }}">
        Profil Sekolah
    </a>
</div>
