<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Sekolah</title>

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: system-ui, sans-serif;
        }

        header {
            height: 56px;
            box-sizing: border-box;
            padding: 0 16px;
            display: flex;
            align-items: center;
            gap: 16px;
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

        #map {
            height: calc(100% - 56px);
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

        <span id="jumlah"></span>

        <a href="{{ route('admin.sekolah.index') }}">Kelola Data</a>
    </header>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const map = L.map('map').setView([-3.3194, 114.5908], 12);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Mencegah teks dari database dibaca sebagai HTML (XSS)
        function esc(teks) {
            const d = document.createElement('div');
            d.textContent = teks ?? '';
            return d.innerHTML;
        }

        const lapisan = L.geoJSON(null, {
            onEachFeature: (fitur, layer) => {
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
        }).addTo(map);

        async function muatData(jenjang = '') {
            const url = `{{ route('peta.data') }}?jenjang=${jenjang}`;
            const respon = await fetch(url);
            const data = await respon.json();

            lapisan.clearLayers();
            lapisan.addData(data);

            document.getElementById('jumlah').textContent =
                data.features.length + ' sekolah';

            if (data.features.length > 0) {
                map.fitBounds(lapisan.getBounds().pad(0.1));
            }
        }

        document.getElementById('filter-jenjang')
            .addEventListener('change', (e) => muatData(e.target.value));

        muatData();
    </script>
</body>
</html>
