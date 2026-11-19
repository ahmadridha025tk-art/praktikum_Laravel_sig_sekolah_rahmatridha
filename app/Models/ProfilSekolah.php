<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProfilSekolah extends Model
{
   protected $fillable = [
    'sekolah_id',
    'kepala_sekolah',
    'email',
    'website',
    'tahun_berdiri',
    'akreditasi',
    'jumlah_siswa',
    'jumlah_guru',
    'deskripsi',
    'logo',
];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }
    //hasone dan hasmany
    public function profil(): HasOne
    {
        return $this->hasOne(ProfilSekolah::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}