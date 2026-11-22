@extends('layouts.public')

@section('title', 'Peta Sekolah')

@push('styles')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css">

<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css">

<style>
/* =========================================
   FUTURISTIC GIS MAP - TOOLBAR
========================================= */

.toolbar {
    position: absolute !important;
    top: 24px !important;
    left: 24px !important;
    right: auto !important;

    width: 350px !important;
    max-width: calc(100% - 48px) !important;

    display: flex !important;
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 16px !important;

    padding: 24px 20px 20px !important;

    background: rgba(12, 20, 39, 0.94) !important;
    backdrop-filter: blur(22px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(22px) saturate(160%) !important;

    border: 1px solid rgba(94, 234, 212, 0.18) !important;
    border-radius: 22px !important;

    box-shadow:
        0 20px 60px rgba(2, 8, 23, 0.35),
        0 0 30px rgba(6, 182, 212, 0.08),
        inset 0 1px 0 rgba(255,255,255,0.06) !important;

    color: #f8fafc !important;
    z-index: 1000 !important;
}

/* Garis aksen atas */
.toolbar::before {
    content: "";
    position: absolute;
    top: 0;
    left: 22px;
    right: 22px;
    height: 2px;

    background: linear-gradient(
        90deg,
        transparent,
        #06b6d4,
        #3b82f6,
        transparent
    );

    border-radius: 10px;
}

/* Label */
.toolbar label {
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;

    color: #94a3b8 !important;
    font-size: 11px !important;
    font-weight: 700 !important;

    text-transform: uppercase;
    letter-spacing: 1.1px;
}

/* Input dan select */
.toolbar select,
.toolbar input[type="number"],
.toolbar input[type="text"] {
    width: 100% !important;
    min-height: 42px;

    padding: 10px 12px !important;

    color: #e2e8f0 !important;
    background: rgba(30, 41, 59, 0.85) !important;

    border: 1px solid rgba(148, 163, 184, 0.18) !important;
    border-radius: 12px !important;

    outline: none !important;
    font-size: 13px !important;

    transition: 0.25s ease !important;
}

.toolbar select:hover {
    border-color: rgba(34, 211, 238, 0.4) !important;
}

.toolbar select:focus {
    border-color: #06b6d4 !important;

    box-shadow:
        0 0 0 3px rgba(6, 182, 212, 0.12),
        0 0 15px rgba(6, 182, 212, 0.08) !important;
}

.toolbar select option {
    background: #0c1427;
    color: #e2e8f0;
}

/* Slider radius */
.toolbar input[type="range"] {
    width: 100% !important;
    margin: 8px 0 !important;

    accent-color: #06b6d4 !important;
    cursor: pointer;
}

/* Jumlah sekolah */
#jumlah {
    display: flex;
    align-items: center;
    justify-content: center;

    min-height: 44px;
    padding: 12px;

    color: #67e8f9 !important;
    background: rgba(6, 182, 212, 0.08);

    border: 1px solid rgba(6, 182, 212, 0.2);
    border-radius: 12px;

    font-size: 13px;
    font-weight: 800;
    letter-spacing: 0.5px;
}

/* Tombol download GeoJSON */
#unduh-geojson {
    width: 100% !important;
    min-height: 46px;

    background: linear-gradient(135deg, #0891b2, #2563eb) !important;
    color: #ffffff !important;

    border: none !important;
    border-radius: 12px !important;

    font-size: 13px !important;
    font-weight: 800 !important;
    letter-spacing: 0.3px;

    box-shadow: 0 8px 22px rgba(37, 99, 235, 0.22) !important;

    transition: all 0.25s ease !important;
}

#unduh-geojson:hover {
    transform: translateY(-2px);

    background: linear-gradient(135deg, #06b6d4, #3b82f6) !important;

    box-shadow: 0 12px 28px rgba(6, 182, 212, 0.3) !important;
}

/* Kontrol Leaflet */
.leaflet-top.leaflet-left {
    margin-top: 8px !important;
    margin-left: 8px !important;
}

/* Responsif tablet dan HP */
@media (max-width: 768px) {
    .toolbar {
        top: 12px !important;
        left: 12px !important;
        right: 12px !important;

        width: auto !important;
        max-width: none !important;

        padding: 16px !important;
        gap: 12px !important;

        border-radius: 18px !important;
    }

    .toolbar label {
        font-size: 10px !important;
    }

    .toolbar select {
        min-height: 38px !important;
        font-size: 12px !important;
    }

    #unduh-geojson {
        min-height: 42px !important;
    }
}
</style>
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
