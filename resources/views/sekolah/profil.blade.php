@extends('layouts.app')

@section('title', 'Profil Sekolah')

@section('content')

<h1 class="text-2xl font-bold mb-1">
    {{ $sekolah->nama }}
</h1>

<p class="text-gray-600 mb-4">
    NPSN {{ $sekolah->npsn }}
</p>

@include('sekolah._tab', ['sekolah' => $sekolah])

@if (session('success'))
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $teks = [
        'kepala_sekolah' => ['Kepala Sekolah', 'text'],
        'email'          => ['Email Sekolah', 'email'],
        'website'        => ['Website (diawali https://)', 'url'],
        'tahun_berdiri'  => ['Tahun Berdiri', 'number'],
        'jumlah_siswa'   => ['Jumlah Siswa', 'number'],
        'jumlah_guru'    => ['Jumlah Guru', 'number'],
    ];

    $cls = 'w-full border rounded px-3 py-2';
@endphp

<form
    method="POST"
    action="{{ route('sekolah.profil.update', $sekolah) }}"
    enctype="multipart/form-data"
    class="bg-white rounded shadow p-6 grid md:grid-cols-2 gap-6"
>
    @csrf
    @method('PUT')

    {{-- Input data profil --}}
    @foreach ($teks as $name => [$label, $type])
        <div>
            <label for="{{ $name }}" class="block font-medium">
                {{ $label }}
            </label>

            <input
                type="{{ $type }}"
                id="{{ $name }}"
                name="{{ $name }}"
                value="{{ old($name, $profil->$name) }}"
                class="{{ $cls }}"
            >

            @error($name)
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>
    @endforeach

    {{-- Akreditasi --}}
    <div>
        <label for="akreditasi" class="block font-medium">
            Akreditasi
        </label>

        <select
            id="akreditasi"
            name="akreditasi"
            class="{{ $cls }}"
        >
            <option value="">-- Pilih --</option>

            @foreach (['A', 'B', 'C', 'Belum Terakreditasi'] as $o)
                <option
                    value="{{ $o }}"
                    @selected(old('akreditasi', $profil->akreditasi) === $o)
                >
                    {{ $o }}
                </option>
            @endforeach
        </select>

        @error('akreditasi')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    {{-- Deskripsi --}}
    <div class="md:col-span-2">
        <label for="deskripsi" class="block font-medium">
            Deskripsi
        </label>

        <textarea
            id="deskripsi"
            name="deskripsi"
            rows="5"
            class="{{ $cls }}"
        >{{ old('deskripsi', $profil->deskripsi) }}</textarea>

        @error('deskripsi')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    {{-- Upload logo sekolah --}}
    <div class="md:col-span-2 border-t pt-5">
        <label for="logo" class="block font-medium mb-2">
            Logo Sekolah
        </label>

        {{-- Tampilkan logo yang sudah tersimpan --}}
        @if ($profil->logo)
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">
                    Logo saat ini:
                </p>

                <img
                    src="{{ asset('storage/' . $profil->logo) }}"
                    alt="Logo {{ $sekolah->nama }}"
                    class="w-32 h-32 object-contain border rounded p-2"
                >
            </div>
        @endif

        <input
            type="file"
            id="logo"
            name="logo"
            accept=".jpg,.jpeg,.png,.webp"
            class="{{ $cls }}"
        >

        <p class="text-sm text-gray-500 mt-1">
            Format: JPG, JPEG, PNG, atau WebP. Maksimal 2 MB.
        </p>

        @error('logo')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    {{-- Tombol simpan --}}
    <div class="md:col-span-2">
        <button
            type="submit"
            class="px-5 py-2 bg-blue-700 text-white rounded hover:bg-blue-800"
        >
            Simpan Profil
        </button>
    </div>

</form>

@endsection