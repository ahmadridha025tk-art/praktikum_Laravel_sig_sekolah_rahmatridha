@extends('layouts.app')

@section('title', 'Profil Sekolah')

@section('content')
<h1 class="text-2xl font-bold mb-1">{{ $sekolah->nama }}</h1>
<p class="text-gray-600 mb-4">NPSN {{ $sekolah->npsn }}</p>

@include('sekolah._tab', ['sekolah' => $sekolah])

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

<form method="POST" action="{{ route('sekolah.profil.update', $sekolah) }}"
      class="bg-white rounded shadow p-6 grid md:grid-cols-2 gap-6">
    @csrf
    @method('PUT')

    @foreach ($teks as $name => [$label, $type])
        <div>
            <label for="{{ $name }}" class="block font-medium">
                {{ $label }}
            </label>
            <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
                   value="{{ old($name, $profil->$name) }}"
                   class="{{ $cls }}">
            @error($name)
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>
    @endforeach

    <div>
        <label for="akreditasi" class="block font-medium">Akreditasi</label>
        <select id="akreditasi" name="akreditasi" class="{{ $cls }}">
            <option value="">-- Pilih --</option>
            @foreach (['A', 'B', 'C', 'Belum Terakreditasi'] as $o)
                <option value="{{ $o }}"
                    @selected(old('akreditasi', $profil->akreditasi) === $o)>
                    {{ $o }}
                </option>
            @endforeach
        </select>
        @error('akreditasi')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="deskripsi" class="block font-medium">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" rows="5"
                  class="{{ $cls }}">{{ old('deskripsi', $profil->deskripsi) }}</textarea>
        @error('deskripsi')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <button class="px-5 py-2 bg-blue-700 text-white rounded">
            Simpan Profil
        </button>
    </div>
</form>
@endsection