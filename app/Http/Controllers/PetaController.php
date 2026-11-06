<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    public function index()
    {
        return view('peta');
    }

    public function geojson(Request $request)
    {
        // Mengambil data sekolah beserta profilnya
        $features = Sekolah::with('profil')
            ->when($request->jenjang, fn ($q, $j) => $q->where('jenjang', $j))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->get()
            ->map(fn ($s) => [
                'type' => 'Feature',

                'geometry' => [
                   'type' => 'Point',
                    'coordinates' => [$s->longitude, $s->latitude],
                ],

                'properties' => [
                    'npsn'           => $s->npsn,
                    'nama'           => $s->nama,
                    'jenjang'        => $s->jenjang,
                    'status'         => $s->status,
                    'alamat'         => $s->alamat,
                    'kecamatan'      => $s->kecamatan,
                    'telepon'        => $s->telepon,

                    // >>> TAMBAHAN PRAKTIKUM 17
                    'kepala_sekolah' => $s->profil?->kepala_sekolah,
                    'akreditasi'     => $s->profil?->akreditasi,
                    'jumlah_siswa'   => $s->profil?->jumlah_siswa,
                    'jumlah_guru'    => $s->profil?->jumlah_guru,
                    // <<< AKHIR TAMBAHAN PRAKTIKUM 17
                ],
            ]);

        // Mengirim data dalam format GeoJSON
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
