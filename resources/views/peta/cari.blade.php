@extends('layouts.public')

@section('title', 'Pencarian')
//
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
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        padding: 16px;
        margin-bottom: 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.05);
    }

    .toolbar input,
    .toolbar select {
        height: 44px;
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        padding: 0 14px;
        background: #f8fafc;
        color: #1e293b;
        outline: none;
        transition: 0.2s;
    }

    .toolbar input {
        flex: 1;
        min-width: 220px;
    }

    .toolbar input:focus,
    .toolbar select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        background: #ffffff;
    }

    .toolbar button {
        height: 44px;
        padding: 0 18px;
        border: none;
        border-radius: 10px;
        background: #e2e8f0;
        color: #334155;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .toolbar button:hover {
        background: #cbd5e1;
    }

    #jumlah {
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
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
        position: absolute;
        z-index: 1000;
        top: 150px;
        left: 32px;
        width: 300px;
        max-height: 55%;
        overflow-y: auto;
        background: rgba(255, 255, 255, 0.97);
        backdrop-filter: blur(12px);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
    }

    .panel strong {
        display: block;
        color: #0f172a;
        font-size: 15px;
        margin-bottom: 10px;
    }

    #hasil {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    #hasil li {
        padding: 10px;
        border-top: 1px solid #e5e7eb;
        border-radius: 8px;
        color: #334155;
        font-size: 13px;
        cursor: pointer;
        transition: 0.2s;
    }

    #hasil li:hover {
        background: #eff6ff;
        color: #1d4ed8;
    }

    @media (max-width: 768px) {
        .toolbar {
            padding: 12px;
            gap: 8px;
        }

        .toolbar input {
            min-width: 100%;
        }

        .toolbar select {
            flex: 1;
        }

        .peta {
            height: 70vh;
            min-height: 400px;
        }

        .panel {
            top: auto;
            left: 12px;
            bottom: 20px;
            width: min(280px, calc(100% - 24px));
            max-height: 40%;
        }
    }
</style>
@endpush

@section('content')

<div class="toolbar">
    <input type="search" id="kata" size="32"
           placeholder="Cari nama, NPSN, atau kecamatan">

    <select id="jenjang">
        <option value="">Semua jenjang</option>
        <option value="SD">SD</option>
        <option value="SMP">SMP</option>
        <option value="SMA">SMA</option>
        <option value="SMK">SMK</option>
    </select>

    <button id="hapus" type="button">Hapus</button>

    <span id="jumlah"></span>
</div>

<div id="peta" class="peta"></div>

<div class="panel">
    <strong>Hasil pencarian</strong>
    <ul id="hasil"></ul>
</div>

@endsection

@push('scripts')
<script>
    const map = Peta.buat('peta');
    const lapisan = L.layerGroup().addTo(map);

    let semua = [];

    function tampil(daftar) {
        lapisan.clearLayers();

        const ul = document.getElementById('hasil');
        ul.innerHTML = '';

        daftar.forEach((f) => {
            const marker = L.marker(Peta.latlng(f))
                .bindPopup(Peta.popup(f))
                .addTo(lapisan);

            const li = document.createElement('li');
            li.textContent = `${f.properties.nama} (${f.properties.kecamatan})`;

            li.addEventListener('click', () => {
                map.flyTo(Peta.latlng(f), 17);
                marker.openPopup();
            });

            ul.appendChild(li);
        });

        document.getElementById('jumlah').textContent =
            `${daftar.length} dari ${semua.length} sekolah`;

        if (daftar.length > 0) {
            map.fitBounds(
                L.latLngBounds(daftar.map(Peta.latlng)).pad(0.1)
            );
        }
    }

    function cari() {
        const kata = document.getElementById('kata').value.trim().toLowerCase();
        const jenjang = document.getElementById('jenjang').value;

        tampil(semua.filter(({ properties: p }) =>
            (!jenjang || p.jenjang === jenjang) &&
            (!kata || [p.nama, p.npsn, p.kecamatan]
                .some((t) => (t ?? '').toLowerCase().includes(kata)))
        ));
    }

    let tunda;

    document.getElementById('kata').addEventListener('input', () => {
        clearTimeout(tunda);
        tunda = setTimeout(cari, 300);
    });

    document.getElementById('jenjang').addEventListener('change', cari);

    document.getElementById('hapus').addEventListener('click', () => {
        document.getElementById('kata').value = '';
        document.getElementById('jenjang').value = '';
        cari();
    });

    Peta.ambilSekolah().then((fitur) => {
        semua = fitur;
        tampil(semua);
    });
</script>
@endpush
