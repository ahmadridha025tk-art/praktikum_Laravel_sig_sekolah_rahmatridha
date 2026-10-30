
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Sekolah</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <!-- MarkerCluster CSS -->
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: system-ui, sans-serif;
        }

        header {
            min-height: 56px;
            box-sizing: border-box;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            background: #1e3a8a;
            color: #fff;
        }

        header h1 {
            font-size: 18px;
            margin: 0;
        }

        header a {
            color: #bfdbfe;
            margin-left: auto;
        }

        input, select, button {
            padding: 7px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            cursor: pointer;
            background: #2563eb;
            color: white;
            border: none;
        }

        #map {
            height: calc(100% - 56px);
        }

        /* Ikon marker sekolah */
        .marker-sekolah {
            width: 18px;
            height: 18px;
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: 0 1px 5px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>

<header>
    <h1>Peta Sekolah</h1>

    <select id="filter-jenjang">
        <option value="">Semua jenjang</option>
        <option value="SD">SD</option>
        <option value="SMP">SMP</option>
        <option value="SMA">SMA</option>
        <option value="SMK">SMK</option>
    </select>

    <select id="filter-status">
        <option value="">Semua status</option>
        <option value="Negeri">Negeri</option>
        <option value="Swasta">Swasta</option>
    </select>

    <input
        type="text"
        id="cari-sekolah"
        placeholder="Cari nama sekolah..."
    >

    <button id="tombol-cari">Cari</button>

    <span id="jumlah"></span>

    <a href="{{ route('admin.sekolah.index') }}">Kelola Data</a>
</header>

<div id="map"></div>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- MarkerCluster JavaScript -->
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
    // Membuat peta
    // Membuat peta
const map = L.map('map').setView([-3.3194, 114.5908], 12);

// Peta dasar OpenStreetMap
const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
});

// Peta dasar Satelit
const satelit = L.tileLayer(
    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
    {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri'
    }
);

// Menampilkan peta OpenStreetMap sebagai default
osm.addTo(map);

// Pilihan peta dasar
const baseMaps = {
    "OpenStreetMap": osm,
    "Satelit": satelit
};

// Menambahkan kontrol pergantian peta
L.control.layers(baseMaps, null, {
    collapsed: false
}).addTo(map);

    // Mengamankan teks popup
    function esc(teks) {
        const d = document.createElement('div');
        d.textContent = teks ?? '';
        return d.innerHTML;
    }

    // Warna marker berdasarkan jenjang
    function warnaMarker(jenjang) {
        const warna = {
            'SD': 'green',
            'SMP': 'blue',
            'SMA': 'orange',
            'SMK': 'red'
        };

        return warna[jenjang] || 'gray';
    }

    // Membuat grup marker cluster
    const cluster = L.markerClusterGroup();

    map.addLayer(cluster);

    // Menyimpan lapisan GeoJSON
    let lapisan = L.geoJSON(null, {
        pointToLayer: function (fitur, latlng) {
            const warna = warnaMarker(fitur.properties.jenjang);

            // Ikon lingkaran berwarna
            const ikon = L.divIcon({
                className: '',
                html: `<div class="marker-sekolah"
                            style="background-color:${warna}">
                       </div>`,
                iconSize: [22, 22],
                iconAnchor: [11, 11]
            });

            return L.marker(latlng, {
                icon: ikon
            });
        },

        onEachFeature: function (fitur, layer) {
            const p = fitur.properties;
            const [lng, lat] = fitur.geometry.coordinates;

            layer.bindPopup(`
                <strong>${esc(p.nama)}</strong><br>
                NPSN: ${esc(p.npsn)}<br>
                ${esc(p.jenjang)} - ${esc(p.status)}<br>
                ${esc(p.alamat)}, ${esc(p.kecamatan)}<br>
                Telp: ${esc(p.telepon) || '-'}<br>
                <small>Koordinat: ${lat}, ${lng}</small>
            `);
        }
    });

    // Memuat data sekolah berdasarkan filter
    async function muatData(jenjang = '', status = '') {
        try {
            const params = new URLSearchParams({
                jenjang: jenjang,
                status: status
            });

            const url = `{{ route('peta.data') }}?${params}`;

            const respon = await fetch(url);
            const data = await respon.json();

            // Bersihkan marker sebelumnya
            cluster.clearLayers();

            // Buat ulang lapisan GeoJSON
            lapisan = L.geoJSON(data, {
                pointToLayer: function (fitur, latlng) {
                    const warna = warnaMarker(fitur.properties.jenjang);

                    const ikon = L.divIcon({
                        className: '',
                        html: `<div class="marker-sekolah"
                                    style="background-color:${warna}">
                               </div>`,
                        iconSize: [22, 22],
                        iconAnchor: [11, 11]
                    });

                    return L.marker(latlng, {
                        icon: ikon
                    });
                },

                onEachFeature: function (fitur, layer) {
                    const p = fitur.properties;
                    const [lng, lat] = fitur.geometry.coordinates;

                    layer.bindPopup(`
                        <strong>${esc(p.nama)}</strong><br>
                        NPSN: ${esc(p.npsn)}<br>
                        ${esc(p.jenjang)} - ${esc(p.status)}<br>
                        ${esc(p.alamat)}, ${esc(p.kecamatan)}<br>
                        Telp: ${esc(p.telepon) || '-'}<br>
                        <small>Koordinat: ${lat}, ${lng}</small>
                    `);
                }
            });

            // Masukkan marker ke dalam cluster
            cluster.addLayer(lapisan);

            document.getElementById('jumlah').textContent =
                data.features.length + ' sekolah';

            if (data.features.length > 0) {
                map.fitBounds(lapisan.getBounds().pad(0.1));
            }

        } catch (error) {
            console.error('Gagal memuat data sekolah:', error);
            alert('Gagal memuat data sekolah. Periksa koneksi atau endpoint GeoJSON.');
        }
    }

    // Filter jenjang dan status
    function terapkanFilter() {
        const jenjang = document.getElementById('filter-jenjang').value;
        const status = document.getElementById('filter-status').value;

        muatData(jenjang, status);
    }

    document.getElementById('filter-jenjang')
        .addEventListener('change', terapkanFilter);

    document.getElementById('filter-status')
        .addEventListener('change', terapkanFilter);

    // Pencarian sekolah
    function cariSekolah() {
        const kataKunci = document.getElementById('cari-sekolah')
            .value.trim().toLowerCase();

        if (!kataKunci) {
            alert('Masukkan nama sekolah terlebih dahulu.');
            return;
        }

        let ditemukan = false;

        lapisan.eachLayer(function (layer) {
            const nama = layer.feature.properties.nama.toLowerCase();

            if (nama.includes(kataKunci)) {
                map.setView(layer.getLatLng(), 17);

                // Buka popup setelah animasi zoom
                setTimeout(function () {
                    layer.openPopup();
                }, 300);

                ditemukan = true;
            }
        });

        if (!ditemukan) {
            alert('Sekolah tidak ditemukan pada data yang sedang ditampilkan.');
        }
    }

    document.getElementById('tombol-cari')
        .addEventListener('click', cariSekolah);

    document.getElementById('cari-sekolah')
        .addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                cariSekolah();
            }
        });

    // Jalankan saat halaman dibuka
    muatData();

</script>

</body>
</html>
