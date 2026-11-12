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

        const url =
            `${window.PETA_URL.wilayah}?jenjang=${encodeURIComponent(jenjang)}`;

        const data = await (await fetch(url)).json();

        if (lapisan) {
            map.removeLayer(lapisan);
        }

        const maks = Math.max(1, ...data.features.map((f) =>
            f.properties.jumlah_sekolah
        ));

        lapisan = L.geoJSON(data, {

            style: (f) => {

                const w = warna(
                    f.properties.jumlah_sekolah / maks
                );

                return {
                    color: w,
                    fillColor: w,
                    fillOpacity: 0.3,
                    weight: 3
                };

            },

            onEachFeature: (f, layer) => {

                const p = f.properties;

                layer.bindTooltip(
                    Peta.esc(p.kecamatan),
                    { sticky: true }
                );

                layer.bindPopup(`
                    <strong>${Peta.esc(p.kecamatan)}</strong><br>
                    Jumlah sekolah: ${p.jumlah_sekolah}<br>
                    Jumlah siswa: ${p.jumlah_siswa}
                `);

            },

        }).addTo(map);

        document.getElementById('jumlah').textContent =
            data.features.length + ' wilayah';

        if (data.features.length > 0) {
            map.fitBounds(lapisan.getBounds().pad(0.1));
        }

    }

    document.getElementById('filter-jenjang')
        .addEventListener('change', (e) => muat(e.target.value));

    muat();

</script>

@endpush
