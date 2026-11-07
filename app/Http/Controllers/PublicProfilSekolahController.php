<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;

class PublicProfilSekolahController extends Controller
{
    public function show(Sekolah $sekolah)
    {
        $sekolah->load('profil');

        return view('sekolah.profil-publik', compact('sekolah'));
    }
}
