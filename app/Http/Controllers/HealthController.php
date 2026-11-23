<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function __invoke(Request $request)
    {
        $mulai = microtime(true);

        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');

            $sehat = true;
            $kodeStatus = 200;
            $pesan = 'Koneksi database aktif dan merespons normal.';
        } catch (\Throwable $e) {
            $sehat = false;
            $kodeStatus = 503;
            $pesan = 'Koneksi database gagal: ' . $e->getMessage();
        }

        $latensiMs = round((microtime(true) - $mulai) * 1000, 2);

        $data = [
            'status' => $sehat ? 'OK' : 'ERROR',
            'sehat' => $sehat,
            'aplikasi' => config('app.name'),
            'environment' => config('app.env'),
            'database' => [
                'status' => $sehat ? 'Connected' : 'Disconnected',
                'driver' => config('database.default'),
                'latensi_ms' => $latensiMs,
                'pesan' => $pesan,
            ],
            'waktu' => now()->toDateTimeString(),
        ];

        // Jika diakses via API / curl / GitHub Actions dengan header Accept: application/json
        if ($request->wantsJson()) {
            return response()->json($data, $kodeStatus);
        }

        // Jika diakses lewat browser, tampilkan halaman pemantau sederhana
        return response()->view('health', $data, $kodeStatus);
    }
}