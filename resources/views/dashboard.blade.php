@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold">Selamat datang, {{ $user->name }}</h1>
<p class="text-gray-600 mb-6">Masuk sebagai {{ $user->roleLabel() }}</p>

@if ($user->isAdmin())
    <div class="grid md:grid-cols-3 gap-4">
        @foreach ($jumlahPerRole as $label => $jumlah)
            <div class="bg-white rounded shadow p-5">
                <p class="text-3xl font-bold text-blue-800">{{ $jumlah }}</p>
                <p class="text-gray-600">Akun {{ $label }}</p>
            </div>
        @endforeach
    </div>
@elseif ($user->isDinas())
    <div class="bg-white rounded shadow p-5 w-64">
        <p class="text-3xl font-bold text-blue-800">{{ $jumlahSekolah }}</p>
        <p class="text-gray-600">Sekolah terdata</p>
    </div>
@elseif ($sekolah)
    <div class="bg-white rounded shadow p-5 max-w-lg">
        <p class="text-xl font-bold">{{ $sekolah->nama }}</p>
        <p class="text-gray-600">NPSN {{ $sekolah->npsn }} - {{ $sekolah->jenjang }} {{ $sekolah->status }}</p>
        <p class="mt-4">Kelengkapan profil: <strong>{{ $kelengkapan }}%</strong></p>
        <div class="w-full bg-gray-200 rounded h-3 mt-1">
            <div class="bg-blue-700 h-3 rounded" style="width: {{ $kelengkapan }}%"></div>
        </div>
    </div>
@else
    <div class="p-4 rounded bg-yellow-100 text-yellow-800">
        Akun Anda belum ditautkan ke sekolah. Hubungi admin.
    </div>
@endif
@endsection
