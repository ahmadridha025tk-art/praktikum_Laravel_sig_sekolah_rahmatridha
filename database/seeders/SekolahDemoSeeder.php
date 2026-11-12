<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahDemoSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026);

        // kecamatan => [latitude pusat, longitude pusat, jumlah sekolah]
        $kecamatan = [
            'Banjarmasin Utara'   => [-3.2900, 114.5950, 30],
            'Banjarmasin Timur'   => [-3.3150, 114.6150, 22],
            'Banjarmasin Tengah'  => [-3.3250, 114.5900, 16],
            'Banjarmasin Selatan' => [-3.3550, 114.5900, 26],
            'Banjarmasin Barat'   => [-3.3200, 114.5650, 12],
        ];

        $jenjang = ['SD', 'SD', 'SD', 'SMP', 'SMP', 'SMA', 'SMK'];

        $akreditasi = ['A', 'B', 'C'];

        $nomor = 0;

        foreach ($kecamatan as $nama => [$lat, $lng, $banyak]) {

            for ($i = 1; $i <= $banyak; $i++) {

                $nomor++;

                $j = $jenjang[array_rand($jenjang)];

                $sekolah = Sekolah::updateOrCreate(
                    ['npsn' => sprintf('9%07d', $nomor)],
                    [
                        'nama'      => "{$j} Demo {$nomor}",
                        'jenjang'   => $j,
                        'status'    => mt_rand(0, 2) ? 'Negeri' : 'Swasta',
                        'alamat'    => "Jl. Demo No. {$i}",
                        'kecamatan' => $nama,
                        'latitude'  => round(
                            $lat + mt_rand(-100, 100) / 10000,
                            7
                        ),
                        'longitude' => round(
                            $lng + mt_rand(-100, 100) / 10000,
                            7
                        ),
                    ]
                );

                $sekolah->profil()->updateOrCreate([], [
                    'kepala_sekolah' => "Kepala Demo {$nomor}",
                    'akreditasi'     => $akreditasi[mt_rand(0, 2)],
                    'jumlah_siswa'   => mt_rand(80, 900),
                    'jumlah_guru'    => mt_rand(8, 60),
                ]);
            }
        }
    }
}
