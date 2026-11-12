
@extends('layouts.public')

@section('title', 'Rute')

@push('styles')
<style>
    .daftar {
        list-style: none;
        margin: 8px 0;
        padding: 0;
        max-height: 40vh;
        overflow: auto;
    }

    .daftar li {
        padding: 4px 0;
        border-top: 1px solid #e5e7eb;
    }

    .daftar label {
        cursor: pointer;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn {
        padding: 8px 12px;
        border: 0;
        border-radius: 6px;
        background: #1e3a8a;
        color: #fff;
        cursor: pointer;
    }

    .btn-abu {
        background: #6b7280;
    }

    .btn:disabled {
        opacity: .6;
        cursor: wait;
    }
</style>
@endpush


@section('content')

<div id="peta" class="peta"></div>

<div class="panel" style="top: 12px; left: 60px; width: 300px;">

    <strong>Rute kunjungan sekolah</strong>

    <p style="margin: 4px 0; color: #6b7280;">
        Pilih 2 sampai 10 sekolah.
        Urutan rute mengikuti urutan Anda mencentang.
    </p>

    <input
        type="search"
        id="kata"
        placeholder="Cari sekolah"
        style="width: 100%; box-sizing: border-box; padding: 6px;"
    >

    <ul id="daftar" class="daftar"></ul>

    <div style="display: flex; gap: 8px;">

        <button id="buat" class="btn" type="button">
            Buat Rute
        </button>

        <button id="reset" class="btn btn-abu" type="button">
            Reset
        </button>

    </div>

    <p id="urutan" style="margin: 8px 0 0; font-size: 13px;"></p>

</div>

<div id="info" class="panel"
     style="right: 12px; bottom: 24px; min-width: 220px; display: none;">
</div>

@endsection


@push('scripts')

<script src="https://unpkg.com/leaflet-polylinedecorator@1.6.0/dist/leaflet.polylineDecorator.js"></script>

<script>

    const MAKS = 10;

    const map = Peta.buat('peta');

    const lapisanRute = L.layerGroup().addTo(map);

    const semua = new Map();

    let terpilih = [];


    function gambarDaftar() {

        const kata = document.getElementById('kata')
            .value.trim().toLowerCase();

        const ul = document.getElementById('daftar');

        ul.innerHTML = '';


        [...semua.values()]

            .filter((f) =>
                f.properties.nama.toLowerCase().includes(kata)
            )

            .forEach((f) => {

                const npsn = f.properties.npsn;

                const cek = document.createElement('input');

                cek.type = 'checkbox';

                cek.checked = terpilih.includes(npsn);

                cek.addEventListener('change', () =>
                    ubahPilihan(npsn, cek)
                );


                const label = document.createElement('label');

                label.append(cek, ` ${f.properties.nama}`);


                const li = document.createElement('li');

                li.appendChild(label);

                ul.appendChild(li);

            });

    }


    function ubahPilihan(npsn, cek) {

        if (cek.checked) {

            if (terpilih.length >= MAKS) {

                cek.checked = false;

                alert(`Maksimal ${MAKS} sekolah.`);

                return;

            }

            terpilih.push(npsn);

        } else {

            terpilih = terpilih.filter((n) => n !== npsn);

        }

        tampilUrutan();

    }


    function tampilUrutan() {

        document.getElementById('urutan').textContent = terpilih

            .map((n, i) =>
                `${i + 1}. ${semua.get(n).properties.nama}`
            )

            .join(' → ');

    }


    // titik: [[lat, lng], ...]
    async function ambilRute(titik) {

        const koordinat = titik

            .map(([lat, lng]) => `${lng},${lat}`)

            .join(';');


        try {

            const respon = await fetch(

                `https://router.project-osrm.org/route/v1/driving/${koordinat}?overview=full&geometries=geojson`

            );

            if (!respon.ok) {
                throw new Error('HTTP ' + respon.status);
            }


            const rute = (await respon.json()).routes?.[0];

            if (!rute) {
                throw new Error('Rute tidak ditemukan');
            }


            return {

                sumber: 'OSRM',

                garis: rute.geometry.coordinates.map(
                    ([lng, lat]) => [lat, lng]
                ),

                jarak: rute.distance / 1000,

                durasi: rute.duration / 60,

                legs: rute.legs.map((l) => ({

                    jarak: l.distance / 1000,

                    durasi: l.duration / 60,

                })),

            };

        } catch (e) {

            console.error(e);

            // Cadangan: garis lurus dan jarak Haversine

            const legs = titik.slice(1).map((t, i) => ({

                jarak: Peta.jarakKm(titik[i], t),

                durasi: null,

            }));


            return {

                sumber: 'Garis lurus',

                garis: titik,

                jarak: legs.reduce((a, l) => a + l.jarak, 0),

                durasi: null,

                legs,

            };

        }

    }


    async function buatRute() {

        if (terpilih.length < 2) {

            alert('Pilih minimal 2 sekolah.');

            return;

        }


        const fitur = terpilih.map((n) => semua.get(n));

        const titik = fitur.map(Peta.latlng);


        const tombol = document.getElementById('buat');

        tombol.disabled = true;

        const rute = await ambilRute(titik);

        tombol.disabled = false;


        lapisanRute.clearLayers();


        const garis = L.polyline(rute.garis, {

            color: '#3b82f6',

            weight: 4,

            opacity: 0.8,

        }).addTo(lapisanRute);


        L.polylineDecorator(garis, {

            patterns: [{

                offset: '5%',

                repeat: 100,

                symbol: L.Symbol.arrowHead({

                    pixelSize: 12,

                    polygon: false,

                    pathOptions: {

                        stroke: true,

                        weight: 2,

                        color: '#1e40af',

                    },

                }),

            }],

        }).addTo(lapisanRute);


        fitur.forEach((f, i) => {

            const warna = i === 0 ? '#16a34a'

                : i === fitur.length - 1 ? '#dc2626'

                : '#2563eb';


            const leg = i > 0 ? rute.legs[i - 1] : null;

            const tambahan = leg

                ? `<br>Dari titik sebelumnya: ${leg.jarak.toFixed(2)} km`

                    + (leg.durasi
                        ? ` (${Math.round(leg.durasi)} menit)`
                        : '')

                : '';


            L.marker(titik[i], {

                icon: Peta.ikonAngka(i + 1, warna)

            })

            .bindPopup(`

                <strong>${i + 1}. ${Peta.esc(f.properties.nama)}</strong><br>

                ${Peta.esc(f.properties.alamat)}${tambahan}

            `)

            .addTo(lapisanRute);

        });


        const info = document.getElementById('info');

        info.style.display = 'block';

        info.innerHTML = `

            <strong>Informasi rute</strong><br>

            Jumlah titik: ${fitur.length}<br>

            Total jarak: ${rute.jarak.toFixed(2)} km<br>

            ${rute.durasi
                ? `Perkiraan waktu: ${Math.round(rute.durasi)} menit<br>`
                : ''}

            <small>Sumber: ${rute.sumber === 'OSRM'

                ? 'mengikuti jalan (OSRM)'

                : 'garis lurus (layanan rute tidak tersedia)'}

            </small>

        `;


        map.fitBounds(garis.getBounds().pad(0.1));

    }


    function reset() {

        terpilih = [];

        lapisanRute.clearLayers();

        document.getElementById('info').style.display = 'none';

        tampilUrutan();

        gambarDaftar();

    }


    document.getElementById('kata')
        .addEventListener('input', gambarDaftar);

    document.getElementById('buat')
        .addEventListener('click', buatRute);

    document.getElementById('reset')
        .addEventListener('click', reset);


    Peta.ambilSekolah().then((fitur) => {

        fitur.forEach((f) => {

            semua.set(f.properties.npsn, f);


            // Titik abu-abu sebagai konteks letak seluruh sekolah

            L.circleMarker(Peta.latlng(f), {

                radius: 5,

                color: '#6b7280',

                fillOpacity: 0.6,

            })

            .bindTooltip(Peta.esc(f.properties.nama))

            .addTo(map);

        });


        gambarDaftar();

        map.fitBounds(

            L.latLngBounds(fitur.map(Peta.latlng)).pad(0.1)

        );

    });

</script>

@endpush
