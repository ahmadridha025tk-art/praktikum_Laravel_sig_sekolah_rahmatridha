
@extends('layouts.app')

@section('title', 'Ubah Pengguna')

@section('content')

<h1 class="text-2xl font-bold mb-4">Ubah Pengguna</h1>

<form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf
    @method('PUT')

    @include('admin.users._form')
</form>

@endsection
