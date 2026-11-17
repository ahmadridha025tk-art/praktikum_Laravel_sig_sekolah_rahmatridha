@extends('layouts.public')

@section('title', 'Wilayah')

@section('content')

<div class="toolbar">

    <label>Jenjang

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

<div class="panel" style="right: 12px; bottom: 24px; font-size: 13px;">

    <strong>Jumlah sekolah</strong>

    <div>
        <span style="color: #dc2626;">&#9632;</span>
        Terbanyak
    </div>

    <div>
        <span style="color: #ea580c;">&#9632;</span>
        Banyak
    </div>

    <div>
        <span style="color: #f59e0b;">&#9632;</span>
        Sedang
    </div>

    <div>
        <span style="color: #3b82f6;">&#9632;</span>
        Sedikit
    </div>

    <small style="color: #6b7280;">
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
                    fillOpacity: 0.3,
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

