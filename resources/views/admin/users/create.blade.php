
@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')

<h1 class="text-2xl font-bold mb-4">Tambah Pengguna</h1>

<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    @include('admin.users._form')
</form>

@endsection
