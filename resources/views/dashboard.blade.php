@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="max-w-7xl mx-auto px-2 sm:px-4 pb-8">

    {{-- Header Dashboard --}}
    <div class="mb-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

            <div>
                <p class="text-sm font-semibold text-cyan-400 mb-2">
                    Dashboard
                </p>

                <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">
                    Selamat datang, {{ $user->name }}
                </h1>

                <p class="text-slate-400 mt-2">
                    Anda masuk sebagai
                    <span class="font-semibold text-slate-200">
                        {{ $user->roleLabel() }}
                    </span>
                </p>
            </div>

            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-semibold w-fit">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Akun Aktif
            </div>

        </div>
    </div>

    {{-- Dashboard Admin --}}
    @if ($user->isAdmin())

        <div class="mb-6">
            <h2 class="text-lg font-bold text-white">
                Statistik Pengguna
            </h2>

            <p class="text-sm text-slate-400 mt-1">
                Ringkasan akun berdasarkan peran pengguna.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

            @foreach ($jumlahPerRole as $label => $jumlah)

                <div class="group bg-slate-800/80 rounded-2xl border border-slate-700/80 p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-cyan-500/30 hover:shadow-lg hover:shadow-cyan-950/20">

                    <div class="flex items-center justify-between mb-5">

                        <div class="w-12 h-12 rounded-xl bg-violet-500/10 border border-violet-500/10 flex items-center justify-center text-violet-400 text-xl">
                            👤
                        </div>

                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            Pengguna
                        </span>

                    </div>

                    <p class="text-3xl font-extrabold text-white">
                        {{ $jumlah }}
                    </p>

                    <p class="text-sm text-slate-400 mt-2">
                        Akun {{ $label }}
                    </p>

                </div>

            @endforeach

        </div>

    {{-- Dashboard Dinas --}}
    @elseif ($user->isDinas())

        <div class="mb-6">
            <h2 class="text-lg font-bold text-white">
                Statistik Pendidikan
            </h2>

            <p class="text-sm text-slate-400 mt-1">
                Ringkasan sekolah yang terdaftar dalam sistem.
            </p>
        </div>

        <div class="max-w-md">

            <div class="bg-slate-800/80 rounded-2xl border border-slate-700/80 p-6 shadow-sm transition duration-300 hover:border-cyan-500/30 hover:shadow-lg">

                <div class="flex items-center justify-between mb-5">

                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-400 text-xl">
                        🏫
                    </div>

                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                        Data Pendidikan
                    </span>

                </div>

                <p class="text-4xl font-extrabold text-white">
                    {{ $jumlahSekolah }}
                </p>

                <p class="text-slate-400 mt-2">
                    Sekolah terdata
                </p>

            </div>

        </div>

    {{-- Dashboard Sekolah --}}
    @elseif ($sekolah)

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

            {{-- Informasi Sekolah --}}
            <div class="xl:col-span-2 bg-slate-800/80 rounded-2xl border border-slate-700/80 p-6 shadow-sm">

                <div class="flex items-start gap-4">

                    <div class="w-14 h-14 shrink-0 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 text-2xl">
                        🏫
                    </div>

                    <div class="min-w-0">

                        <h2 class="text-xl font-bold text-white">
                            {{ $sekolah->nama }}
                        </h2>

                        <p class="text-sm text-slate-400 mt-1">
                            NPSN {{ $sekolah->npsn }}
                        </p>

                        <div class="flex flex-wrap gap-2 mt-3">

                            <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-xs font-semibold border border-blue-500/10">
                                {{ $sekolah->jenjang }}
                            </span>

                            <span class="px-3 py-1 rounded-full bg-slate-700/70 text-slate-300 text-xs font-semibold">
                                {{ $sekolah->status }}
                            </span>

                        </div>

                    </div>

                </div>

                <div class="mt-6 border-t border-slate-700 pt-5">

                    <div class="flex items-center justify-between mb-2">

                        <p class="text-sm font-semibold text-slate-300">
                            Kelengkapan Profil
                        </p>

                        <p class="text-sm font-bold text-cyan-400">
                            {{ $kelengkapan }}%
                        </p>

                    </div>

                    <div class="w-full bg-slate-700 rounded-full h-3 overflow-hidden">

                        <div
                            class="bg-gradient-to-r from-blue-500 to-cyan-400 h-3 rounded-full transition-all duration-500"
                            style="width: {{ $kelengkapan }}%"
                        ></div>

                    </div>

                    <p class="text-xs text-slate-400 mt-3">
                        Lengkapi informasi sekolah agar profil lebih informatif.
                    </p>

                </div>

            </div>

            {{-- Kelengkapan --}}
            <div class="bg-gradient-to-br from-blue-600 to-cyan-600 rounded-2xl p-6 text-white shadow-sm">

                <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-xl mb-5">
                    📋
                </div>

                <p class="text-sm text-blue-100 font-medium">
                    Kelengkapan Profil
                </p>

                <p class="text-4xl font-extrabold mt-2">
                    {{ $kelengkapan }}%
                </p>

                <p class="text-sm text-blue-100 mt-3 leading-relaxed">
                    @if ($kelengkapan >= 100)
                        Profil sekolah Anda sudah lengkap.
                    @elseif ($kelengkapan >= 70)
                        Profil sekolah hampir lengkap. Sedikit lagi!
                    @elseif ($kelengkapan >= 40)
                        Profil sekolah sudah terisi sebagian.
                    @else
                        Mulai lengkapi profil sekolah Anda.
                    @endif
                </p>

            </div>

        </div>

    {{-- Akun Belum Terhubung --}}
    @else

        <div class="max-w-2xl rounded-2xl border border-amber-500/20 bg-amber-500/10 p-6">

            <div class="flex items-start gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400 text-xl">
                    ⚠️
                </div>

                <div>

                    <h2 class="font-bold text-amber-300">
                        Sekolah Belum Terhubung
                    </h2>

                    <p class="mt-2 text-sm leading-relaxed text-amber-200/80">
                        Akun Anda belum ditautkan ke sekolah.
                        Silakan hubungi administrator untuk menghubungkan
                        akun Anda dengan data sekolah.
                    </p>

                </div>

            </div>

        </div>

    @endif

</div>

@endsection