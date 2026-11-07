<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SekolahController extends Controller
{
    public function index(Request $request)
    {
        $sekolahs = Sekolah::query()
            ->when($request->q, function ($query, $q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nama', 'like', "%{$q}%")
                        ->orWhere('npsn', 'like', "%{$q}%")
                        ->orWhere('kecamatan', 'like', "%{$q}%");
                });
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.sekolah.index', compact('sekolahs'));
    }

    public function create()
    {
        return view('admin.sekolah.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        // Simpan koordinat pertama sebagai titik awal
        $data['latitude_awal'] = $data['latitude'];
        $data['longitude_awal'] = $data['longitude'];

        Sekolah::create($data);

        return redirect()->route('dinas.sekolah.index')
            ->with('success', 'Data sekolah berhasil ditambahkan.');
    }

    public function edit(Sekolah $sekolah)
    {
        Gate::authorize('update', $sekolah);

        return view('admin.sekolah.edit', compact('sekolah'));
    }

    public function update(Request $request, Sekolah $sekolah)
    {
        Gate::authorize('update', $sekolah);

        $data = $request->validate($this->rules($sekolah->id));

        // Pengguna Sekolah tidak boleh mengubah NPSN
        if ($request->user()->isSekolah()) {
            unset($data['npsn']);

            // Pastikan koordinat awal tersedia
            if (
                $sekolah->latitude_awal === null ||
                $sekolah->longitude_awal === null
            ) {
                return back()
                    ->withErrors([
                        'latitude' => 'Koordinat awal sekolah belum tersedia.',
                    ])
                    ->withInput();
            }

            // Hitung jarak dari koordinat awal
            $jarak = $this->hitungJarak(
                (float) $sekolah->latitude_awal,
                (float) $sekolah->longitude_awal,
                (float) $data['latitude'],
                (float) $data['longitude']
            );

            // Tolak perubahan lebih dari 100 meter
            if ($jarak > 100) {
                return back()
                    ->withErrors([
                        'latitude' => 'Lokasi sekolah tidak dapat diubah karena jaraknya '
                            . round($jarak, 2)
                            . ' meter dari titik awal. Batas maksimal adalah 100 meter.',
                    ])
                    ->withInput();
            }
        }

        // Simpan perubahan jika validasi jarak berhasil
        $sekolah->update($data);

        return $this->kembali($request)
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function destroy(Sekolah $sekolah)
    {
        $sekolah->delete();

        return redirect()->route('dinas.sekolah.index')
            ->with('success', 'Data sekolah berhasil dihapus.');
    }

    // Menghitung jarak menggunakan rumus Haversine
    private function hitungJarak(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $radiusBumi = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1))
            * cos(deg2rad($lat2))
            * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radiusBumi * $c;
    }

    private function kembali(Request $request)
    {
        return $request->user()->isDinas()
            ? redirect()->route('dinas.sekolah.index')
            : redirect()->route('dashboard');
    }

    private function rules(?int $id = null): array
    {
        return [
            'npsn' => [
                'required',
                'digits:8',
                Rule::unique('sekolahs', 'npsn')->ignore($id),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', Rule::in(['SD', 'SMP', 'SMA', 'SMK'])],
            'status' => ['required', Rule::in(['Negeri', 'Swasta'])],
            'alamat' => ['required', 'string'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}
