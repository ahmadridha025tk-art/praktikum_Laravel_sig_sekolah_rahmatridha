<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    // Menampilkan halaman utama peta sekolah
    public function index()
    {
        return view('peta.index');
    }

    //cari sekolah berdasarkan nama, jenjang, status, dan kecamatan

    public function cari()
    {
        return view('peta.cari');
    }
    //method klaster
        public function klaster()
    {
        return view('peta.klaster');
    }


    // Mengambil data sekolah dalam format GeoJSON
    public function geojson(Request $request)
    {
        $features = Sekolah::with('profil')

            ->when($request->jenjang, fn ($q, $j) =>
                $q->where('jenjang', $j)
            )

            ->when($request->status, fn ($q, $s) =>
                $q->where('status', $s)
            )

            ->get()

            ->map(fn ($s) => [

                'type' => 'Feature',

                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        $s->longitude,
                        $s->latitude
                    ],
                ],

                'properties' => [

                    'id' => $s->id,
                    'npsn' => $s->npsn,
                    'nama' => $s->nama,
                    'jenjang' => $s->jenjang,
                    'status' => $s->status,
                    'alamat' => $s->alamat,
                    'kecamatan' => $s->kecamatan,
                    'telepon' => $s->telepon,

                    // Informasi profil sekolah
                    'kepala_sekolah' => $s->profil?->kepala_sekolah,
                    'akreditasi' => $s->profil?->akreditasi,
                    'jumlah_siswa' => $s->profil?->jumlah_siswa,
                    'jumlah_guru' => $s->profil?->jumlah_guru,

                    // Logo sekolah
                    'logo_url' => $s->profil?->logo
                        ? asset('storage/' . $s->profil->logo)
                        : null,

                    // Link halaman profil publik
                    'profil_url' => route(
                        'sekolah.profil.publik',
                        $s->id
                    ),

                ],

            ]);

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
