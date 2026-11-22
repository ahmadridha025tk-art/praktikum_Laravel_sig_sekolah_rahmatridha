@extends('layouts.app')

@section('title', 'Profil Sekolah')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
            {{ $sekolah->nama }}
        </h1>

        <p class="text-slate-500 mt-1">
            NPSN {{ $sekolah->npsn }}
        </p>
    </div>

    {{-- Tab Navigasi --}}
    @include('sekolah._tab', ['sekolah' => $sekolah])

    {{-- Notifikasi Berhasil --}}
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-green-700">
            <span class="text-xl">✓</span>
            <div>
                <p class="font-semibold">Berhasil!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
            <p class="font-semibold mb-2">Terdapat kesalahan pada input:</p>
            <ul class="list-disc pl-5 space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $teks = [
            'kepala_sekolah' => ['Kepala Sekolah', 'text'],
            'email'           => ['Email Sekolah', 'email'],
            'website'          => ['Website (diawali https://)', 'url'],
            'tahun_berdiri'  => ['Tahun Berdiri', 'number'],
            'jumlah_siswa'   => ['Jumlah Siswa', 'number'],
            'jumlah_guru'    => ['Jumlah Guru', 'number'],
        ];

        $cls = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100';
    @endphp

    {{-- Form --}}
    <form
        method="POST"
        action="{{ route('sekolah.profil.update', $sekolah) }}"
        enctype="multipart/form-data"
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
    >
        @csrf
        @method('PUT')

        {{-- Bagian Informasi Profil --}}
        <div class="border-b border-slate-100 p-5 md:p-7">

            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-800">
                    Informasi Profil Sekolah
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Lengkapi informasi umum dan identitas sekolah.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                @foreach ($teks as $name => [$label, $type])
                    <div>
                        <label for="{{ $name }}"
                               class="mb-2 block text-sm font-semibold text-slate-700">
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
                            <p class="mt-1.5 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                @endforeach

                {{-- Akreditasi --}}
                <div>
                    <label for="akreditasi"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Akreditasi
                    </label>

                    <select
                        id="akreditasi"
                        name="akreditasi"
                        class="{{ $cls }}"
                    >
                        <option value="">-- Pilih Akreditasi --</option>

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
                        <p class="mt-1.5 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="border-b border-slate-100 p-5 md:p-7">

            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-800">
                    Deskripsi Sekolah
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Ceritakan informasi, keunggulan, dan gambaran sekolah.
                </p>
            </div>

            <div>
                <label for="deskripsi"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    Deskripsi
                </label>

                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="6"
                    class="{{ $cls }} resize-y"
                    placeholder="Tuliskan deskripsi sekolah..."
                >{{ old('deskripsi', $profil->deskripsi) }}</textarea>

                @error('deskripsi')
                    <p class="mt-1.5 text-sm text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>

        {{-- Upload Logo --}}
        <div class="p-5 md:p-7">

            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-800">
                    Logo Sekolah
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Unggah logo untuk identitas visual sekolah.
                </p>
            </div>

            {{-- Logo saat ini --}}
            @if ($profil->logo)
                <div class="mb-5 rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <p class="mb-3 text-sm font-semibold text-slate-600">
                        Logo saat ini
                    </p>

                    <div class="inline-flex rounded-xl border border-slate-200 bg-white p-3">
                        <img
                            src="{{ asset('storage/' . $profil->logo) }}"
                            alt="Logo {{ $sekolah->nama }}"
                            class="h-32 w-32 object-contain"
                        >
                    </div>

                </div>
            @endif

            <div>
                <label for="logo"
                       class="mb-2 block text-sm font-semibold text-slate-700">
                    Pilih File Logo
                </label>

                <input
                    type="file"
                    id="logo"
                    name="logo"
                    accept=".jpg,.jpeg,.png,.webp"
                    class="{{ $cls }}"
                >

                <p class="mt-2 text-xs text-slate-500">
                    Format JPG, JPEG, PNG, atau WebP. Maksimal 2 MB.
                </p>

                @error('logo')
                    <p class="mt-1.5 text-sm text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>

        {{-- Tombol Simpan --}}
        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 p-5 md:flex-row md:justify-end md:px-7">

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 hover:shadow-md active:scale-[0.98]"
            >
                <span>💾</span>
                Simpan Profil
            </button>

        </div>

    </form>

</div>

@endsection
