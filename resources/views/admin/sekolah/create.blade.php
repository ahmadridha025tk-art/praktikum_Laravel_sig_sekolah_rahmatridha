@extends('layouts.app')
@section('title', 'Tambah Sekolah')

@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Sekolah</h1>

<form method="POST" action="{{ route('admin.sekolah.store') }}">
    @csrf

    @include('admin.sekolah._form', [
        'sekolah' => new \App\Models\Sekolah
    ])
</form>
@endsection