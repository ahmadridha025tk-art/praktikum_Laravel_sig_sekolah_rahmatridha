<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use App\Support\Geometri;
use Illuminate\Support\Facades\Cache;

class PetaController extends Controller
{
    // Menampilkan halaman utama peta sekolah
    public function index()
    {
        return view('peta.index');
    }

    // Mencari sekolah
    public function cari()
    {
        return view('peta.cari');
    }

    // Menampilkan halaman klaster
    public function klaster()
    {
        return view('peta.klaster');
    }

    // Menampilkan halaman peta panas
    public function panas()
    {
        return view('peta.panas');
    }

    // Menampilkan halaman wilayah
    public function wilayah()
    {
        return view('peta.wilayah');
    }

    // Menampilkan halaman Rute
    public function rute()
    {
        return view('peta.rute');
    }

    // Menampilkan halaman Cek Lokasi
    public function alamat()
    {
        return view('peta.alamat');
    }

    // Mengambil data sekolah dalam format GeoJSON
    public function geojson(Request $request)
    {
        // --- MULAI PERUBAHAN (BARIS 56 - 81) ---
        $sekolahs = Cache::remember('data_sekolah', 300, function () {
            return Sekolah::with('profil')->get();
        });

        $features = $sekolahs
            ->when($request->jenjang, fn ($c, $j) =>
                $c->where('jenjang', $j)
            )
            ->when($request->status, fn ($c, $s) =>
                $c->where('status', $s)
            )
            ->when($request->filled('bbox'), function ($c) use ($request) {
                $bbox = explode(',', $request->bbox);

                if (count($bbox) === 4 && collect($bbox)->every('is_numeric')) {
                    [$barat, $selatan, $timur, $utara] = array_map('floatval', $bbox);

                    return $c->filter(fn ($s) =>
                        (float) $s->longitude >= $barat &&
                        (float) $s->longitude <= $timur &&
                        (float) $s->latitude >= $selatan &&
                        (float) $s->latitude <= $utara
                    );
                }

                return $c;
            })
            ->values()
        // --- AKHIR PERUBAHAN ---

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

    // Mengambil data poligon wilayah sekolah
    public function geojsonWilayah(Request $request)
    {
        $features = Sekolah::with('profil')

            ->when($request->jenjang, fn ($q, $j) =>
                $q->where('jenjang', $j)
            )

            ->get()

            ->groupBy('kecamatan')

            ->map(function ($kelompok, $kecamatan) {

                $titik = $kelompok

                    ->map(fn ($s) => [
                        $s->longitude,
                        $s->latitude
                    ])

                    ->all();

                $hull = Geometri::convexHull($titik);

                // Poligon memerlukan minimal 3 titik
                if (count($hull) < 3) {
                    return null;
                }

                // Menutup cincin poligon GeoJSON
                $hull[] = $hull[0];

                return [

                    'type' => 'Feature',

                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => [$hull],
                    ],

                    'properties' => [

                        'kecamatan' => $kecamatan,

                        'jumlah_sekolah' => $kelompok->count(),

                        'jumlah_siswa' => (int) $kelompok->sum(
                            fn ($s) => $s->profil?->jumlah_siswa ?? 0
                        ),

                    ],

                ];

            })

            ->filter()

            ->values();

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

}