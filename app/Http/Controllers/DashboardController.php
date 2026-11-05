<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $jumlahPerRole = null;
        $jumlahSekolah = null;
        $sekolah = null;
        $kelengkapan = 0;

        if ($user->isAdmin()) {
            $jumlahPerRole = [
                'Admin' => User::where('role', User::ROLE_ADMIN)->count(),
                'Dinas Pendidikan' => User::where('role', User::ROLE_DINAS)->count(),
                'Sekolah' => User::where('role', User::ROLE_SEKOLAH)->count(),
            ];
        } elseif ($user->isDinas()) {
            $jumlahSekolah = Sekolah::count();
        } elseif ($user->sekolah) {
            $sekolah = $user->sekolah->load('profil');
            $kolom = [
                'kepala_sekolah', 'email', 'website', 'tahun_berdiri',
                'akreditasi', 'jumlah_siswa', 'jumlah_guru', 'deskripsi'
            ];
            $profil = $sekolah->profil;
            $terisi = $profil
                ? collect($kolom)->filter(fn ($k) => filled($profil->$k))->count()
                : 0;
            $kelengkapan = (int) round(($terisi / count($kolom)) * 100);
        }

        return view('dashboard', compact(
            'user', 'jumlahPerRole', 'jumlahSekolah', 'sekolah', 'kelengkapan'
        ));
    }
}
