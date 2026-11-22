@extends('layouts.public')

@section('title', 'Peta Panas')

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

    .toolbar input[type="range"] {
        accent-color: #2563eb;
        cursor: pointer;
        width: 130px;
    }

    #jumlah {
        padding: 7px 12px;
        background: #eff6ff;
        color: #1d4ed8;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
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
        border-radius: 14px;
        padding: 14px;
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.12);
    }

    .legenda-gradien {
        height: 10px;
        border-radius: 10px;
        background: linear-gradient(
            to right,
            #2563eb,
            #06b6d4,
            #84cc16,
            #facc15,
            #dc2626
        );
    }

    .legenda-label {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #64748b;
        margin-top: 6px;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .toolbar {
            padding: 12px;
            gap: 12px;
        }

        .toolbar label {
            flex: 1;
            min-width: 140px;
            flex-wrap: wrap;
        }

        .toolbar select {
            width: 100%;
        }

        .toolbar input[type="range"] {
            width: 100%;
        }

        .peta {
            height: 70vh;
            min-height: 400px;
        }

        .panel {
            left: 12px !important;
            bottom: 16px !important;
            width: 160px !important;
        }
    }
</style>
@endpush

@section('content')

<div class="toolbar">

    <label>
        Bobot
        <select id="mode">
            <option value="sekolah">Jumlah sekolah</option>
            <option value="siswa">Jumlah siswa</option>
        </select>
    </label>

    <label>
        Jenjang
        <select id="jenjang">
            <option value="">Semua jenjang</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SMA">SMA</option>
            <option value="SMK">SMK</option>
        </select>
    </label>

    <label>
        Radius
        <input type="range" id="radius" min="10" max="60" value="30">
    </label>

    <span id="jumlah"></span>
</div>

<div id="peta" class="peta"></div>

<div class="panel"
     style="position:absolute; left:12px; bottom:24px; width:180px;">

    <div class="legenda-gradien"></div>

    <div class="legenda-label">
        <span>Rendah</span>
        <span>Tinggi</span>
    </div>

</div>

@endsection

@push('scripts')

<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>
    const map = Peta.buat('peta');

    let heat = null;
    let data = [];

    function gambar() {
        const mode = document.getElementById('mode').value;
        const radius = Number(document.getElementById('radius').value);

        const maks = Math.max(
            1,
            ...data.map((f) => f.properties.jumlah_siswa ?? 0)
        );

        const titik = data.map((f) => {
            const siswa = f.properties.jumlah_siswa ?? 0;

            const bobot = mode === 'siswa'
                ? Math.max(siswa / maks, 0.1)
                : 0.35;

            return [...Peta.latlng(f), bobot];
        });

        if (heat) {
            map.removeLayer(heat);
        }

        heat = L.heatLayer(titik, {
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
        }).addTo(map);

        document.getElementById('jumlah').textContent =
            data.length + ' sekolah';
    }

    async function muat() {
        data = await Peta.ambilSekolah(
            document.getElementById('jenjang').value
        );

        gambar();

        if (data.length > 0) {
            map.fitBounds(
                L.latLngBounds(data.map(Peta.latlng)).pad(0.1)
            );
        }
    }

    document.getElementById('jenjang')
        .addEventListener('change', muat);

    document.getElementById('mode')
        .addEventListener('change', gambar);

    document.getElementById('radius')
        .addEventListener('input', gambar);

    muat();
</script>

@endpush
