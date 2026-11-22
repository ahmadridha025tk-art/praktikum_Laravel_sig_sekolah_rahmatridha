@php
    $aktif = 'border-blue-600 text-blue-700 bg-blue-50 font-semibold';
    $biasa = 'border-transparent text-gray-500 hover:text-blue-600 hover:bg-gray-50';
@endphp

<div class="mb-6 border-b border-gray-200">
    <div class="flex gap-2 overflow-x-auto">

        <a href="{{ route('sekolah.edit', $sekolah) }}"
           class="whitespace-nowrap px-5 py-3 border-b-2 rounded-t-xl transition-all duration-200
           {{ request()->routeIs('sekolah.edit') ? $aktif : $biasa }}">
            <span class="flex items-center gap-2">
                <i class="fas fa-school"></i>
                Data Sekolah
            </span>
        </a>

        <a href="{{ route('sekolah.profil.edit', $sekolah) }}"
           class="whitespace-nowrap px-5 py-3 border-b-2 rounded-t-xl transition-all duration-200
           {{ request()->routeIs('sekolah.profil.edit') ? $aktif : $biasa }}">
            <span class="flex items-center gap-2">
                <i class="fas fa-file-alt"></i>
                Profil Sekolah
            </span>
        </a>

    </div>
</div>
