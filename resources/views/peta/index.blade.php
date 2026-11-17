
@extends('layouts.public')

@section('title', 'Peta Sekolah')

@push('styles')
<link rel="stylesheet"
    href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css">

<link rel="stylesheet"
    href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css">
@endpush

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

    <label>Status
        <select id="filter-status">
            <option value="">Semua status</option>
            <option value="Negeri">Negeri</option>
            <option value="Swasta">Swasta</option>
        </select>
    </label>

    <label>Bobot Heatmap
        <select id="mode">
            <option value="sekolah">Jumlah sekolah</option>
            <option value="siswa">Jumlah siswa</option>
        </select>
    </label>

    <label>Radius
        <input type="range" id="radius"
            min="10" max="60" value="30">
    </label>

    <span id="jumlah"></span>
    <button type="button" id="unduh-geojson">
    Unduh GeoJSON
</button>
</div>

<div id="peta" class="peta"></div>

@endsection

@push('scripts')

<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>
    const map = Peta.buat('peta');

    // Lapisan marker biasa
    const lapisanMarker = L.layerGroup();

    // Lapisan klaster
    const lapisanKlaster = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 60,
        spiderfyOnMaxZoom: true,
        disableClusteringAtZoom: 17,
        chunkedLoading: true,
    });

    // Lapisan heatmap
    let lapisanHeatmap = L.heatLayer([], {
        radius: 30,
        blur: 25,
        maxZoom: 17,
        max: 1.0,
        gradient: {
            0.2: '#2563eb',
            0.4: '#06b6d4',
            0.6: '#84cc16',
            0.8: '#facc15',
            1.0: '#dc2626',
        },
    });

    // Kontrol pilihan lapisan
    const kontrolLapisan = L.control.layers(null, {
        'Marker Sekolah': lapisanMarker,
        'Klaster Sekolah': lapisanKlaster,
        'Heatmap Sekolah': lapisanHeatmap,
    }, {
        collapsed: false
    }).addTo(map);

    // Marker menjadi tampilan awal
    lapisanMarker.addTo(map);

    let dataSekolah = [];
    let sudahFitAwal = false;

    // Menggambar ulang seluruh lapisan
    function gambarLapisan() {
        lapisanMarker.clearLayers();
        lapisanKlaster.clearLayers();

        const mode = document.getElementById('mode').value;
        const radius = Number(
            document.getElementById('radius').value
        );

        const maks = Math.max(
            1,
            ...dataSekolah.map(f =>
                Number(f.properties.jumlah_siswa ?? 0)
            )
        );

        const titikHeatmap = [];

        dataSekolah.forEach(f => {
            const posisi = Peta.latlng(f);

            const marker = L.marker(posisi)
                .bindPopup(Peta.popup(f));

            // Marker biasa
            lapisanMarker.addLayer(marker);

            // Marker klaster
            lapisanKlaster.addLayer(
                L.marker(posisi).bindPopup(Peta.popup(f))
            );

            // Bobot heatmap
            const siswa = Number(
                f.properties.jumlah_siswa ?? 0
            );

            const bobot = mode === 'siswa'
                ? Math.max(siswa / maks, 0.1)
                : 0.35;

            titikHeatmap.push([
                posisi[0],
                posisi[1],
                bobot
            ]);
        });

        // Perbarui heatmap tanpa menghilangkan kontrol lapisan
        const heatmapAktif = map.hasLayer(lapisanHeatmap);

        kontrolLapisan.removeLayer(lapisanHeatmap);

        lapisanHeatmap = L.heatLayer(titikHeatmap, {
            radius: radius,
            blur: 25,
            maxZoom: 17,
            max: 1.0,
            gradient: {
                0.2: '#2563eb',
                0.4: '#06b6d4',
                0.6: '#84cc16',
                0.8: '#facc15',
                1.0: '#dc2626',
            },
        });

        kontrolLapisan.addOverlay(
            lapisanHeatmap,
            'Heatmap Sekolah'
        );

        if (heatmapAktif) {
            lapisanHeatmap.addTo(map);
        }

        document.getElementById('jumlah').textContent =
            dataSekolah.length + ' sekolah';
    }

   
// Mengambil data sekolah dengan filter Bounding Box
async function muat() {
    try {
        const jenjang =
            document.getElementById('filter-jenjang').value;

        const status =
            document.getElementById('filter-status').value;

        // Pemuatan pertama mengambil semua sekolah
        const bbox = sudahFitAwal
            ? map.getBounds().toBBoxString()
            : '';

        dataSekolah = await Peta.ambilSekolah(
            jenjang,
            status,
            bbox
        );

        gambarLapisan();

        // Atur tampilan peta hanya saat pemuatan pertama
        if (!sudahFitAwal && dataSekolah.length > 0) {
            sudahFitAwal = true;

            map.fitBounds(
                L.latLngBounds(
                    dataSekolah.map(Peta.latlng)
                ).pad(0.1)
            );
        }

    } catch (error) {
        console.error(error);

        document.getElementById('jumlah').textContent =
            'Gagal memuat data sekolah';
    }
}


    // Event filter
    document.getElementById('filter-jenjang')
        .addEventListener('change', muat);

    document.getElementById('filter-status')
        .addEventListener('change', muat);

    // Event heatmap
    document.getElementById('mode')
        .addEventListener('change', gambarLapisan);

    document.getElementById('radius')
        .addEventListener('input', gambarLapisan);

    // Muat ulang data ketika peta digeser atau diperbesar
map.on('moveend', () => {
    if (sudahFitAwal) {
        muat();
    }
});

// Jalankan pertama kali
muat();


    // Unduh data sekolah dalam format GeoJSON
document.getElementById('unduh-geojson')
    .addEventListener('click', () => {
        const geojson = {
            type: 'FeatureCollection',
            features: dataSekolah
        };

        const file = new Blob(
            [JSON.stringify(geojson, null, 2)],
            { type: 'application/geo+json' }
        );

        const url = URL.createObjectURL(file);
        const link = document.createElement('a');

        link.href = url;
        link.download = 'data-sekolah.geojson';
        link.click();

        URL.revokeObjectURL(url);
    });
</script>

@endpush
