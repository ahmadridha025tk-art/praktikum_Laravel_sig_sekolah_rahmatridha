@extends('layouts.app')
@section('title', 'Data Sekolah')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Data Sekolah</h1>
    <a href="{{ route('admin.sekolah.create') }}"
       class="px-4 py-2 bg-blue-700 text-white rounded">
        + Tambah Sekolah
    </a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}"
           placeholder="Cari nama, NPSN, atau kecamatan"
           class="border rounded px-3 py-2 w-80">
    <button class="px-4 py-2 bg-gray-700 text-white rounded">
        Cari
    </button>
</form>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-200 text-left">
            <tr>
                <th class="p-3">No</th>
                <th class="p-3">NPSN</th>
                <th class="p-3">Nama</th>
                <th class="p-3">Jenjang</th>
                <th class="p-3">Status</th>
                <th class="p-3">Kecamatan</th>
                <th class="p-3">Koordinat</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sekolahs as $s)
                <tr class="border-t">
                    <td class="p-3">
                        {{ $sekolahs->firstItem() + $loop->index }}
                    </td>
                    <td class="p-3">{{ $s->npsn }}</td>
                    <td class="p-3">{{ $s->nama }}</td>
                    <td class="p-3">{{ $s->jenjang }}</td>
                    <td class="p-3">{{ $s->status }}</td>
                    <td class="p-3">{{ $s->kecamatan }}</td>
                    <td class="p-3 font-mono text-xs">
                        {{ $s->latitude }}, {{ $s->longitude }}
                    </td>
                    <td class="p-3 flex gap-3">
                        <a href="{{ route('admin.sekolah.edit', $s) }}"
                           class="text-blue-700">Ubah</a>

                        <form method="POST"
                              action="{{ route('admin.sekolah.destroy', $s) }}"
                              onsubmit="return confirm('Hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="p-4 text-center">
                        Belum ada data.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $sekolahs->links() }}</div>
@endsection