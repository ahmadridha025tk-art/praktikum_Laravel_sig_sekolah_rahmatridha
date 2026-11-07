@extends('layouts.app')

@section('title', 'Ubah Kata Sandi')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">

    <h1 class="text-2xl font-bold mb-6">
        Ubah Kata Sandi
    </h1>

    <form action="{{ route('password.update') }}"
          method="POST">

        @csrf
        @method('PUT')

        {{-- Kata sandi lama --}}
        <div class="mb-4">
            <label class="block font-medium mb-2">
                Kata Sandi Lama
            </label>

            <input type="password"
                   name="current_password"
                   class="w-full border rounded p-2"
                   required>

            @error('current_password')
                <p class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Kata sandi baru --}}
        <div class="mb-4">
            <label class="block font-medium mb-2">
                Kata Sandi Baru
            </label>

            <input type="password"
                   name="password"
                   class="w-full border rounded p-2"
                   required>

            @error('password')
                <p class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Konfirmasi kata sandi --}}
        <div class="mb-6">
            <label class="block font-medium mb-2">
                Konfirmasi Kata Sandi Baru
            </label>

            <input type="password"
                   name="password_confirmation"
                   class="w-full border rounded p-2"
                   required>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-blue-700 text-white px-5 py-2 rounded hover:bg-blue-800">
                Simpan Kata Sandi
            </button>

            <a href="{{ route('dashboard') }}"
               class="bg-gray-200 px-5 py-2 rounded">
                Kembali
            </a>
        </div>

    </form>
</div>
@endsection
