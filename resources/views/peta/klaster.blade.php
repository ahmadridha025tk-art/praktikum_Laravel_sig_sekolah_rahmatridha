
@extends('layouts.public')

@section('title', 'Klaster')

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

    <span id="jumlah"></span>

    <span style="color: #6b7280;">
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
