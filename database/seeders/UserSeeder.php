<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use App\Models\User;
use App\Enums\Role;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $sekolahs = Sekolah::orderBy('id')->take(2)->get();

        $akun = [
            ['Admin Sistem', 'admin@sig.test', Role::ADMIN, null],
            ['Dinas Pendidikan', 'dinas@sig.test', Role::DINAS, null],
            ['Operator Sekolah 1', 'sekolah1@sig.test', Role::SEKOLAH, $sekolahs->get(0)?->id],
            ['Operator Sekolah 2', 'sekolah2@sig.test', Role::SEKOLAH, $sekolahs->get(1)?->id],
        ];

        foreach ($akun as [$nama, $email, $role, $sekolahId]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nama,
                    'password' => 'password123',
                    'role' => $role,
                    'sekolah_id' => $sekolahId,
                ]
            );
        }
    }
}