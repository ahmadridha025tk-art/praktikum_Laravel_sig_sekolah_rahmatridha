@extends('layouts.public')

@section('title', 'Wilayah')

@push('styles')
<style>
    .toolbar {
        position: relative;
        top: auto;
        left: auto;
        right: auto;
        width: 100%;
        max-width: 100%;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        padding: 16px;
        margin-bottom: 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.05);
    }

    .toolbar label {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
    }

    .toolbar select {
        height: 42px;
        padding: 0 12px;
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        background: #f8fafc;
        color: #1e293b;
        outline: none;
        transition: 0.2s;
    }

    .toolbar select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
    }

    #jumlah {
        padding: 7px 12px;
        background: #eff6ff;
        color: #1d4ed8;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
    }

    .peta {
        width: 100%;
        height: 75vh;
        min-height: 450px;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #dbe3ee;
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.08);
    }

    .panel {
        z-index: 1000;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(12px);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        color: #334155;
    }

    .panel strong {
        display: block;
        color: #0f172a;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .legenda-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 8px 0;
        font-size: 13px;
        color: #475569;
    }

    .legenda-warna {
        width: 13px;
        height: 13px;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .legenda-catatan {
        display: block;
        margin-top: 10px;
        color: #94a3b8;
        font-size: 11px;
        line-height: 1.5;
    }

    @media (max-width: 768px) {
        .toolbar {
            padding: 12px;
            gap: 10px;
        }

        .toolbar label {
            flex: 1;
            min-width: 140px;
            flex-wrap: wrap;
        }

        .toolbar select {
            width: 100%;
        }

        .peta {
            height: 70vh;
            min-height: 400px;
        }

        .panel {
            right: 12px !important;
            bottom: 16px !important;
            padding: 12px;
            width: 160px;
        }
    }
</style>
@endpush

@section('content')

<div class="toolbar">
    <label>
        Jenjang
        <select id="filter-jenjang">
            <option value="">Semua jenjang</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SMA">SMA</option>
            <option value="SMK">SMK</option>
        </select>
    </label>

    <span id="jumlah"></span>
</div>

<div id="peta" class="peta"></div>

<div class="panel"
     style="position:absolute; right:12px; bottom:24px; font-size:13px;">

    <strong>Jumlah sekolah</strong>

    <div class="legenda-item">
        <span class="legenda-warna" style="background:#dc2626;"></span>
        Terbanyak
    </div>

    <div class="legenda-item">
        <span class="legenda-warna" style="background:#ea580c;"></span>
        Banyak
    </div>

    <div class="legenda-item">
        <span class="legenda-warna" style="background:#f59e0b;"></span>
        Sedang
    </div>

    <div class="legenda-item">
        <span class="legenda-warna" style="background:#3b82f6;"></span>
        Sedikit
    </div>

    <small class="legenda-catatan">
        Relatif terhadap kecamatan terbanyak
    </small>
</div>

@endsection

@push('scripts')
<script>
    const map = Peta.buat('peta');

    let lapisan = null;

    function warna(rasio) {
        if (rasio >= 0.8) return '#dc2626';
        if (rasio >= 0.6) return '#ea580c';
        if (rasio >= 0.4) return '#f59e0b';
        return '#3b82f6';
    }

    async function muat(jenjang = '') {
        const url = `${window.PETA_URL.wilayah}?jenjang=${encodeURIComponent(jenjang)}`;

        const [responSekolah, responBatas] = await Promise.all([
            fetch(url),
            fetch('/data/batas-kecamatan.geojson')
        ]);

        if (!responSekolah.ok || !responBatas.ok) {
            throw new Error('Gagal memuat data wilayah');
        }

        const dataSekolah = await responSekolah.json();
        const dataBatas = await responBatas.json();

        // Menyiapkan data jumlah sekolah berdasarkan nama kecamatan
        const daftarSekolah = new Map(
            dataSekolah.features.map(f => [
                f.properties.kecamatan.trim().toLowerCase(),
                f.properties
            ])
        );

        // Menggabungkan batas BIG dengan data jumlah sekolah
        const fitur = dataBatas.features.map(f => {
            const nama = f.properties.WADMKC.trim().toLowerCase();
            const sekolah = daftarSekolah.get(nama) || {};

            return {
                ...f,
                properties: {
                    ...f.properties,
                    jumlah_sekolah: sekolah.jumlah_sekolah ?? 0,
                    jumlah_siswa: sekolah.jumlah_siswa ?? 0
                }
            };
        });

        if (lapisan) {
            map.removeLayer(lapisan);
        }

        const maks = Math.max(
            1,
            ...fitur.map(f => f.properties.jumlah_sekolah)
        );

        lapisan = L.geoJSON(fitur, {
            style: f => {
                const rasio = f.properties.jumlah_sekolah / maks;
                const w = warna(rasio);

                return {
                    color: w,
                    fillColor: w,
                    fillOpacity: 0.35,
                    weight: 2
                };
            },

            onEachFeature: (f, layer) => {
                const p = f.properties;

                layer.bindTooltip(
                    Peta.esc(p.WADMKC),
                    { sticky: true }
                );

                layer.bindPopup(`
                    <strong>${Peta.esc(p.WADMKC)}</strong><br>
                    Jumlah sekolah: ${p.jumlah_sekolah}<br>
                    Jumlah siswa: ${p.jumlah_siswa}
                `);
            }
        }).addTo(map);

        document.getElementById('jumlah').textContent =
            fitur.length + ' wilayah';

        if (fitur.length > 0) {
            map.fitBounds(lapisan.getBounds().pad(0.1));
        }
    }

    document.getElementById('filter-jenjang')
        .addEventListener('change', e => muat(e.target.value));

    muat().catch(error => {
        console.error(error);
        alert('Gagal memuat peta wilayah. Periksa Console.');
    });
</script>
@endpush
