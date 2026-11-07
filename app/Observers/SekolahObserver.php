<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Sekolah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class SekolahObserver
{
    // Mencatat ketika sekolah ditambahkan
    public function created(Sekolah $sekolah): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'sekolah_id' => $sekolah->id,
            'action' => 'created',
            'old_values' => null,
            'new_values' => Arr::except(
                $sekolah->getAttributes(),
                ['created_at', 'updated_at']
            ),
        ]);
    }

    // Mencatat ketika data sekolah diubah
    public function updated(Sekolah $sekolah): void
    {
        $changes = $sekolah->getChanges();

        // Abaikan perubahan updated_at saja
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $keys = array_keys($changes);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'sekolah_id' => $sekolah->id,
            'action' => 'updated',
            'old_values' => Arr::only(
                $sekolah->getOriginal(),
                $keys
            ),
            'new_values' => Arr::only(
                $sekolah->getAttributes(),
                $keys
            ),
        ]);
    }

    // Mencatat ketika sekolah dihapus
    public function deleted(Sekolah $sekolah): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'sekolah_id' => $null,
            'action' => 'deleted',
            'old_values' => Arr::except(
                $sekolah->getAttributes(),
                ['created_at', 'updated_at']
            ),
            'new_values' => null,
        ]);
    }
}
