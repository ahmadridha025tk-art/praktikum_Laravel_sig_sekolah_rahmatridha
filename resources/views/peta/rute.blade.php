@extends('layouts.public')

@section('title', 'Rute')

@push('styles')
<style>
    .daftar {
        list-style: none;
        margin: 10px 0;
        padding: 0;
        max-height: 36vh;
        overflow-y: auto;
    }

    .daftar li {
        padding: 9px 6px;
        border-top: 1px solid #e5e7eb;
        transition: background 0.2s;
    }

    .daftar li:hover {
        background: #eff6ff;
        border-radius: 8px;
    }

    .daftar label {
        cursor: pointer;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        color: #334155;
        font-size: 13px;
        line-height: 1.5;
    }

    .daftar input[type="checkbox"] {
        margin-top: 3px;
        accent-color: #2563eb;
        cursor: pointer;
    }

    .panel {
        z-index: 1000;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(12px);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
    }

    .panel strong {
        color: #0f172a;
        font-size: 15px;
    }

    .panel p {
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }

    #kata {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        margin: 8px 0;
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        background: #f8fafc;
        outline: none;
        transition: 0.2s;
    }

    #kata:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        background: #ffffff;
    }

    .btn {
        flex: 1;
        padding: 10px 12px;
        border: 0;
        border-radius: 10px;
        background: #2563eb;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-abu {
        background: #e2e8f0;
        color: #334155;
    }

    .btn-abu:hover {
        background: #cbd5e1;
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: wait;
        transform: none;
    }

    #urutan {
        color: #475569;
        font-size: 12px;
        line-height: 1.7;
        word-break: break-word;
    }

    #info {
        color: #334155;
        font-size: 13px;
        line-height: 1.8;
    }

    #info strong {
        display: inline-block;
        margin-bottom: 6px;
    }

    @media (max-width: 768px) {
        .panel {
            top: 12px !important;
            left: 12px !important;
            right: 12px !important;
            bottom: auto !important;
            width: auto !important;
            min-width: 0 !important;
            max-width: none !important;
            max-height: 48vh;
            overflow-y: auto;
        }

        .daftar {
            max-height: 20vh;
        }

        #info {
            top: auto !important;
            bottom: 12px !important;
            left: 12px !important;
            right: 12px !important;
            width: auto !important;
            min-width: 0 !important;
        }
    }
</style>
@endpush

@section('content')

<div id="peta" class="peta"></div>

<div class="panel"
     style="position:absolute; top:12px; left:60px; width:300px;">

    <strong>Rute kunjungan sekolah</strong>

    <p style="margin: 6px 0;">
        Pilih 2–10 sekolah. Urutan rute mengikuti urutan Anda memilih sekolah.
    </p>

    <input
        type="search"
        id="kata"
        placeholder="Cari sekolah"
    >

    <ul id="daftar" class="daftar"></ul>

    <div style="display:flex; gap:8px;">
        <button id="buat" class="btn" type="button">
            Buat Rute
        </button>

        <button id="reset" class="btn btn-abu" type="button">
            Reset
        </button>
    </div>

    <p id="urutan" style="margin:10px 0 0;"></p>
</div>

<div id="info" class="panel"
     style="position:absolute; right:12px; bottom:24px; min-width:220px; display:none;">
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
        tombol.textContent = 'Menghitung rute...';

        try {
            const rute = await ambilRute(titik);

            lapisanRute.clearLayers();

            const garis = L.polyline(rute.garis, {
                color: '#3b82f6',
                weight: 4,
                opacity: 0.85,
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
                <small>
                    Sumber: ${rute.sumber === 'OSRM'
                        ? 'mengikuti jalan (OSRM)'
                        : 'garis lurus (layanan rute tidak tersedia)'}
                </small>
            `;

            map.fitBounds(garis.getBounds().pad(0.1));

        } finally {
            tombol.disabled = false;
            tombol.textContent = 'Buat Rute';
        }
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
