
@extends('layouts.public')

@section('title', 'Pencarian')

@push('styles')
<style>
    #hasil {
        list-style: none;
        margin: 8px 0 0;
        padding: 0;
    }

    #hasil li {
        padding: 8px 4px;
        border-top: 1px solid #e5e7eb;
        cursor: pointer;
    }

    #hasil li:hover {
        background: #eff6ff;
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

<div class="panel"
     style="top: 64px; left: 60px; width: 280px; max-height: 55%; overflow: auto;">

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
