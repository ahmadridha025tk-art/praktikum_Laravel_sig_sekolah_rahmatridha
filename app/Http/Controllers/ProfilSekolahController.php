<?php

namespace App\Http\Controllers;

use App\Models\ProfilSekolah;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProfilSekolahController extends Controller
{
    public function edit(Sekolah $sekolah)
    {
        Gate::authorize('update', $sekolah);
        $profil = $sekolah->profil ?? new ProfilSekolah();
        return view('sekolah.profil', compact('sekolah', 'profil'));
    }

    public function update(Request $request, Sekolah $sekolah)
    {
        Gate::authorize('update', $sekolah);

        $data = $request->validate([
            'kepala_sekolah' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'tahun_berdiri' => ['nullable', 'integer', 'between:1800,' . date('Y')],
            'akreditasi' => ['nullable', Rule::in(['A', 'B', 'C', 'Belum Terakreditasi'])],
            'jumlah_siswa' => ['nullable', 'integer', 'min:0'],
            'jumlah_guru' => ['nullable', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ]);

        $sekolah->profil()->updateOrCreate([], $data);

        return back()->with('success', 'Profil sekolah berhasil disimpan.');
    }
}
