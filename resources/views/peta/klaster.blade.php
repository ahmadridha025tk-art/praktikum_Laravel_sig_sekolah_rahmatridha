@extends('layouts.public')

@section('title', 'Klaster')

@push('styles')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css">

<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css">

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
        gap: 12px;
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
        gap: 8px;
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

    .petunjuk {
        color: #64748b;
        font-size: 12px;
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

    /* Tampilan klaster */
    .marker-cluster-small,
    .marker-cluster-medium,
    .marker-cluster-large {
        background-color: rgba(59, 130, 246, 0.20);
    }

    .marker-cluster-small div,
    .marker-cluster-medium div,
    .marker-cluster-large div {
        background-color: #2563eb;
        color: white;
        font-weight: 700;
        border: 3px solid rgba(255, 255, 255, 0.85);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    @media (max-width: 768px) {
        .toolbar {
            padding: 12px;
            gap: 10px;
        }

        .toolbar label {
            flex: 1;
            min-width: 130px;
            flex-direction: column;
            align-items: flex-start;
        }

        .toolbar select {
            width: 100%;
        }

        .petunjuk {
            width: 100%;
        }

        .peta {
            height: 70vh;
            min-height: 400px;
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

    <label>
        Status
        <select id="filter-status">
            <option value="">Semua status</option>
            <option value="Negeri">Negeri</option>
            <option value="Swasta">Swasta</option>
        </select>
    </label>

    <span id="jumlah"></span>

    <span class="petunjuk">
        Klik angka pada klaster untuk memperbesar peta.
    </span>
</div>

<div id="peta" class="peta"></div>

@endsection

@push('scripts')

<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
    const map = Peta.buat('peta');

    const klaster = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 60,
        spiderfyOnMaxZoom: true,
        disableClusteringAtZoom: 17,
        chunkedLoading: true,
    });

    map.addLayer(klaster);

    async function muat(jenjang = '', status = '') {
        try {
            const fitur = await Peta.ambilSekolah(jenjang, status);

            klaster.clearLayers();

            klaster.addLayers(
                fitur.map((f) =>
                    L.marker(Peta.latlng(f))
                        .bindPopup(Peta.popup(f))
                )
            );

            document.getElementById('jumlah').textContent =
                fitur.length + ' sekolah';

            if (fitur.length > 0) {
                map.fitBounds(
                    L.latLngBounds(fitur.map(Peta.latlng)).pad(0.1)
                );
            }
        } catch (e) {
            console.error(e);

            document.getElementById('jumlah').textContent =
                'Gagal memuat data';
        }
    }

    function terapkanFilter() {
        const jenjang =
            document.getElementById('filter-jenjang').value;

        const status =
            document.getElementById('filter-status').value;

        muat(jenjang, status);
    }

    document.getElementById('filter-jenjang')
        .addEventListener('change', terapkanFilter);

    document.getElementById('filter-status')
        .addEventListener('change', terapkanFilter);

    muat();
</script>

@endpush
