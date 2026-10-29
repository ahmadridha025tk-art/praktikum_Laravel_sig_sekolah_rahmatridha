<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['30000001', 'SD Negeri Contoh 1', 'SD', 'Negeri',
             'Jl. Contoh No. 1', 'Banjarmasin Utara', -3.2950, 114.5900],
            ['30000002', 'SD Swasta Contoh 2', 'SD', 'Swasta',
             'Jl. Contoh No. 2', 'Banjarmasin Selatan', -3.3500, 114.5950],
            ['30000003', 'SMP Negeri Contoh 1', 'SMP', 'Negeri',
             'Jl. Contoh No. 3', 'Banjarmasin Tengah', -3.3250, 114.5880],
            ['30000004', 'SMP Swasta Contoh 2', 'SMP', 'Swasta',
             'Jl. Contoh No. 4', 'Banjarmasin Barat', -3.3200, 114.5700],
            ['30000005', 'SMA Negeri Contoh 1', 'SMA', 'Negeri',
             'Jl. Contoh No. 5', 'Banjarmasin Timur', -3.3100, 114.6100],
            ['30000006', 'SMA Swasta Contoh 2', 'SMA', 'Swasta',
             'Jl. Contoh No. 6', 'Banjarmasin Tengah', -3.3300, 114.6000],
            ['30000007', 'SMK Negeri Contoh 1', 'SMK', 'Negeri',
             'Jl. Contoh No. 7', 'Banjarmasin Selatan', -3.3600, 114.6050],
            ['30000008', 'SMK Swasta Contoh 2', 'SMK', 'Swasta',
             'Jl. Contoh No. 8', 'Banjarmasin Utara', -3.2850, 114.6000],
        ];

        foreach ($data as $d) {
            Sekolah::updateOrCreate(
                ['npsn' => $d[0]],
                [
                    'nama'      => $d[1],
                    'jenjang'   => $d[2],
                    'status'    => $d[3],
                    'alamat'    => $d[4],
                    'kecamatan' => $d[5],
                    'latitude'  => $d[6],
                    'longitude' => $d[7],
                ]
            );
        }
    }
}