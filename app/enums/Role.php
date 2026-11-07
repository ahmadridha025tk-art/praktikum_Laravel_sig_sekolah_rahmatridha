<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case DINAS = 'dinas';
    case SEKOLAH = 'sekolah';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::DINAS => 'Dinas Pendidikan',
            self::SEKOLAH => 'Sekolah',
        };
    }
}