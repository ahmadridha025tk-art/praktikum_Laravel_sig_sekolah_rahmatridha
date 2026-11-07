@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Pengguna</h1>

    <a href="{{ route('admin.users.create') }}"
       class="px-4 py-2 bg-blue-700 text-white rounded">
        + Tambah Pengguna
    </a>
</div>

{{-- Filter berdasarkan role --}}
<form method="GET" class="mb-4 flex gap-2">
    <select name="role" class="border rounded px-3 py-2">
        <option value="">Semua role</option>

        @foreach ([
            'admin' => 'Admin',
            'dinas' => 'Dinas Pendidikan',
            'sekolah' => 'Sekolah'
        ] as $nilai => $label)
            <option value="{{ $nilai }}"
                @selected(request('role') === $nilai)>
                {{ $label }}
            </option>
        @endforeach
    </select>

    <button class="px-4 py-2 bg-gray-700 text-white rounded">
        Filter
    </button>
</form>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">

        <thead class="bg-gray-200 text-left">
            <tr>
                <th class="p-3">Nama</th>
                <th class="p-3">Email</th>
                <th class="p-3">Role</th>
                <th class="p-3">Sekolah</th>
                <th class="p-3">Status</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($users as $u)
                <tr class="border-t">

                    <td class="p-3">
                        {{ $u->name }}
                    </td>

                    <td class="p-3">
                        {{ $u->email }}
                    </td>

                    <td class="p-3">
                        {{ $u->roleLabel() }}
                    </td>

                    <td class="p-3">
                        {{ $u->sekolah?->nama ?? '-' }}
                    </td>

                    {{-- Status akun --}}
                    <td class="p-3">
                        @if ($u->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded">
                                Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded">
                                Nonaktif
                            </span>
                        @endif
                    </td>

                    {{-- Aksi pengguna --}}
                    <td class="p-3">
                        <div class="flex flex-wrap items-center gap-3">

                            {{-- Ubah pengguna --}}
                            <a href="{{ route('admin.users.edit', $u) }}"
                               class="text-blue-700">
                                Ubah
                            </a>

                            @if (! $u->is(auth()->user()))

                                {{-- Aktifkan / Nonaktifkan --}}
                                <form method="POST"
                                      action="{{ route('admin.users.toggleActive', $u) }}"
                                      onsubmit="return confirm('Yakin ingin mengubah status akun ini?')">

                                    @csrf
                                    @method('PATCH')

                                    @if ($u->is_active)
                                        <button type="submit"
                                                class="text-orange-600">
                                            Nonaktifkan
                                        </button>
                                    @else
                                        <button type="submit"
                                                class="text-green-600">
                                            Aktifkan
                                        </button>
                                    @endif
                                </form>

                                {{-- Hapus pengguna --}}
                                <form method="POST"
                                      action="{{ route('admin.users.destroy', $u) }}"
                                      onsubmit="return confirm('Hapus pengguna ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="text-red-600">
                                        Hapus
                                    </button>
                                </form>

                            @endif

                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-4 text-center">
                        Belum ada pengguna.
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $users->links() }}
</div>

@endsection
