@extends('layouts.app')
@section('title', 'Ubah Sekolah')

@section('content')
<h1 class="text-2xl font-bold mb-1">Ubah Sekolah</h1>
<p class="text-gray-600 mb-4">NPSN {{ $sekolah->npsn }}</p>

{{-- Tab Navigasi dari Praktikum 15 --}}
@include('sekolah._tab', ['sekolah' => $sekolah])

<form method="POST"
      action="{{ route('sekolah.update', $sekolah) }}">
    @csrf
    @method('PUT')

    @include('admin.sekolah._form', [
        'sekolah' => $sekolah
    ])
</form>
@endsection
