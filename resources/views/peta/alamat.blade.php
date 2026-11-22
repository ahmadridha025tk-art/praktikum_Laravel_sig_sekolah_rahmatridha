@extends('layouts.public')

@section('title', 'Cek Lokasi')

@push('styles')
<style>
    /* =========================================
       TOOLBAR
    ========================================= */

    .toolbar {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        right: auto !important;
        bottom: auto !important;

        width: 100% !important;
        max-width: 100% !important;

        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;

        padding: 16px 22px;

        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;

        border-radius: 0 !important;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04) !important;

        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;

        z-index: 10;
    }

    .toolbar > span {
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
    }

    /* =========================================
       BUTTON LOKASI
    ========================================= */

    #lokasi-saya {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;

        padding: 11px 18px;

        border: none;
        border-radius: 12px;

        background: linear-gradient(135deg, #2414d8, #4338f0);
        color: #ffffff;

        font-family: inherit;
        font-size: 13px;
        font-weight: 700;

        cursor: pointer;

        box-shadow: 0 5px 15px rgba(36, 20, 216, 0.22);

        transition: all 0.25s ease;
    }

    #lokasi-saya::before {
        content: "⌖";
        font-size: 19px;
        line-height: 1;
    }

    #lokasi-saya:hover {
        background: linear-gradient(135deg, #3829e8, #5147ff);
        transform: translateY(-2px);

        box-shadow: 0 8px 20px rgba(36, 20, 216, 0.28);
    }

    #lokasi-saya:active {
        transform: scale(0.97);
    }

    #lokasi-saya:disabled {
        opacity: 0.65;
        cursor: wait;
        transform: none;
    }

    /* =========================================
       AREA UTAMA
    ========================================= */

    .wadah-utama {
        display: flex;
        align-items: stretch;
        gap: 18px;

        flex: 1;
        min-height: 0;

        padding: 18px;

        margin: 0;
        background: #f4f6fb;
    }

    /* =========================================
       PETA
    ========================================= */

    #peta {
        position: relative !important;
        z-index: 1;

        flex: 1;
        min-width: 0;

        width: 100%;
        height: 100% !important;
        min-height: 400px !important;

        border-radius: 18px;
        overflow: hidden;

        border: 1px solid #e2e8f0;

        box-shadow:
            0 8px 24px rgba(15, 23, 42, 0.08);
    }

    .leaflet-container {
        position: relative;
        z-index: 1;

        width: 100% !important;
        height: 100% !important;

        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Memperbaiki tile Leaflet */

    .leaflet-container img,
    .leaflet-tile-pane img {
        max-width: none !important;
        max-height: none !important;

        padding: 0 !important;
        margin: 0 !important;

        border: none !important;
        outline: none !important;
        box-shadow: none !important;
    }

    .leaflet-tile {
        border: none !important;
        outline: none !important;
    }

    /* =========================================
       PANEL HASIL
    ========================================= */

    #hasil {
        position: relative !important;
        z-index: 2 !important;

        display: none;

        flex: 0 0 360px;
        width: 360px;
        max-width: 360px;

        height: 100%;
        min-height: 400px;

        overflow-y: auto;
        overflow-x: hidden;

        box-sizing: border-box;

        background: #ffffff;

        padding: 22px;

        border: 1px solid #e2e8f0;
        border-radius: 18px;

        box-shadow:
            0 8px 24px rgba(15, 23, 42, 0.06);

        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    /* Judul dan teks panel */

    #hasil strong {
        color: #172033;
        font-weight: 800;
    }

    #hasil p {
        color: #475569;
        line-height: 1.7;
        font-size: 13px;
    }

    #hasil table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }

    #hasil table td {
        vertical-align: top;
        padding: 7px 5px 7px 0 !important;

        font-size: 12.5px;
        color: #334155;

        border-bottom: 1px solid #f1f5f9;
    }

    #hasil table td:first-child {
        width: 42%;
        color: #64748b !important;
    }

    /* =========================================
       LIST SEKOLAH
    ========================================= */

    #hasil ul {
        list-style: none;
        margin: 8px 0 0;
        padding: 0;
    }

    #hasil li[data-npsn] {
        padding: 12px;

        border: 1px solid #e2e8f0;
        border-radius: 12px;

        margin-bottom: 8px;

        cursor: pointer;

        background: #ffffff;

        transition: all 0.2s ease;
    }

    #hasil li[data-npsn]:hover {
        background: #f5f7ff;

        border-color: #c7d2fe;

        transform: translateX(3px);
    }

    #hasil li strong {
        color: #2414d8;
        font-size: 13px;
    }

    #hasil li small {
        display: inline-block;
        margin-top: 5px;

        color: #64748b;
        font-size: 11.5px;
    }

    /* =========================================
       LEAFLET CONTROL
    ========================================= */

    .leaflet-top.leaflet-left {
        margin-top: 10px;
        margin-left: 10px;
    }

    .leaflet-bar {
        border: none !important;
        border-radius: 12px !important;

        overflow: hidden;

        box-shadow:
            0 8px 20px rgba(15, 23, 42, 0.12) !important;
    }

    .leaflet-bar a {
        background: rgba(255, 255, 255, 0.96) !important;
        color: #10182d !important;

        border-bottom: 1px solid #e2e8f0 !important;

        transition: all 0.2s ease;
    }

    .leaflet-bar a:hover {
        background: #2414d8 !important;
        color: #ffffff !important;
    }

    .leaflet-popup-content-wrapper {
        border-radius: 14px !important;

        box-shadow:
            0 12px 28px rgba(15, 23, 42, 0.15) !important;

        font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    /* =========================================
       RESPONSIVE TABLET
    ========================================= */

    @media (max-width: 900px) {

        .wadah-utama {
            flex-direction: column;
            gap: 14px;
        }

        #peta {
            flex: none;

            width: 100%;
            height: 60vh !important;
            min-height: 380px !important;
        }

        #hasil {
            flex: none;

            width: 100%;
            max-width: 100%;

            height: auto;
            min-height: auto;

            max-height: 450px;
        }
    }

    /* =========================================
       RESPONSIVE HP
    ========================================= */

    @media (max-width: 600px) {

        .toolbar {
            padding: 13px 15px;
            gap: 10px;
        }

        .toolbar > span {
            width: 100%;
            font-size: 12px;
            line-height: 1.6;
        }

        #lokasi-saya {
            width: 100%;
        }

        .wadah-utama {
            padding: 10px;
            gap: 12px;
        }

        #peta {
            height: 55vh !important;
            min-height: 350px !important;

            border-radius: 14px;
        }

        #hasil {
            padding: 16px;
            border-radius: 14px;
        }
    }
</style>
@endpush

@section('content')

<div class="toolbar">

    <button type="button" id="lokasi-saya">
        Lokasi Saya
    </button>

    <span>
        Klik di mana saja pada peta untuk melihat alamat
        dan sekolah terdekat.
    </span>

</div>

<div class="wadah-utama">

    <div id="peta"></div>

    <div id="hasil"></div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // 1. MEMBUAT PETA
    // ==========================================

    const map = Peta.buat('peta');

    const lapisanLokasiSaya = L.layerGroup().addTo(map);
    const lapisan = L.layerGroup().addTo(map);

    const panel = document.getElementById('hasil');
    const tombolLokasi = document.getElementById('lokasi-saya');

    let semua = [];
    let terakhir = 0;

    // ==========================================
    // 2. MEMUAT DATA SEKOLAH
    // ==========================================

    const dataSekolahSiap = Peta.ambilSekolah()
        .then(function (fitur) {
            semua = fitur;

            console.log(
                'Data sekolah berhasil dimuat:',
                semua.length
            );

            return semua;
        })
        .catch(function (error) {
            console.error('Gagal memuat sekolah:', error);

            panel.style.display = 'block';
            panel.textContent =
                'Gagal memuat data sekolah. Periksa koneksi dan server Laravel.';
            map.invalidateSize();

            throw error;
        });

    // ==========================================
    // 3. MENCARI SEKOLAH TERDEKAT
    // ==========================================

    function terdekat(titik, n = 3) {

        return semua
            .filter(function (f) {
                return f.geometry &&
                    f.geometry.coordinates &&
                    f.properties;
            })
            .map(function (f) {
                return {
                    fitur: f,
                    jarak: Peta.jarakKm(
                        titik,
                        Peta.latlng(f)
                    )
                };
            })
            .sort(function (a, b) {
            return a.jarak - b.jarak;
        })
        .slice(0, n);
    }

    // ==========================================
    // 4. MENGAMBIL ALAMAT DARI NOMINATIM
    // ==========================================

    async function ambilAlamat(lat, lng) {

        const url =
            'https://nominatim.openstreetmap.org/reverse' +
            '?format=jsonv2&addressdetails=1&accept-language=id' +
            `&lat=${lat}&lon=${lng}`;

        const respon = await fetch(url);

        if (!respon.ok) {
            throw new Error('HTTP ' + respon.status);
        }

        return await respon.json();
    }

    // ==========================================
    // 5. MEMPROSES TITIK PETA
    // ==========================================

    async function prosesTitik(titik) {

        // Pastikan data sekolah sudah tersedia
        try {
            await dataSekolahSiap;
        } catch (error) {
            return;
        }

        const lat = titik.lat;
        const lng = titik.lng;

        // Bersihkan tampilan sebelumnya
        lapisan.clearLayers();

        // ======================================
        // ANALISIS CAKUPAN SEKOLAH RADIUS 1 KM
        // ======================================

        L.circle(titik, {
            radius: 1000,
            color: '#16a34a',
            weight: 2,
            fillColor: '#22c55e',
            fillOpacity: 0.12
        }).addTo(lapisan);

        // Hitung sekolah dalam radius 1 km
        const sekolahDalamRadius = semua.filter(function (f) {

            const jarak = Peta.jarakKm(
                [lat, lng],
                Peta.latlng(f)
            );

            return jarak <= 1;

        });

        const jumlahSekolahDalamRadius =
            sekolahDalamRadius.length;

        // ======================================
        // PENANDA TITIK YANG DIPILIH
        // ======================================

        L.circleMarker(titik, {
            radius: 9,
            color: '#ffffff',
            weight: 3,
            fillColor: '#ef4444',
            fillOpacity: 1
        }).addTo(lapisan);

        // ======================================
        // SEKOLAH TERDEKAT
        // ======================================

        const dekat = terdekat([lat, lng]);

        // Gambar garis menuju tiga sekolah terdekat
        dekat.forEach(function (d) {

            L.polyline(
                [
                    titik,
                    Peta.latlng(d.fitur)
                ],
                {
                    color: '#2563eb',
                    weight: 2,
                    dashArray: '6 6'
                }
            ).addTo(lapisan);

        });

        // Tampilkan panel di sebelah kanan peta
        panel.style.display = 'block';
        panel.textContent = 'Sedang mencari alamat...';
        map.invalidateSize();

        // ======================================
        // MENGAMBIL INFORMASI ALAMAT
        // ======================================

        let bagianAlamat =
            '<em>Alamat tidak dapat dimuat.</em>';

        const sekarang = Date.now();

        if (sekarang - terakhir < 1200) {

            bagianAlamat =
                '<em>Tunggu sebentar sebelum meminta alamat lagi.</em>';

        } else {

            terakhir = sekarang;

            try {

                const data = await ambilAlamat(lat, lng);
                const a = data.address || {};

                const baris = [
                    ['Jalan', a.road],
                    [
                        'Kelurahan/Desa',
                        a.suburb || a.neighbourhood || a.village
                    ],
                    [
                        'Kota/Kabupaten',
                        a.city || a.town ||
                        a.municipality || a.county
                    ],
                    ['Provinsi', a.state],
                    ['Kode pos', a.postcode]
                ];

                const tabel = baris.map(function ([k, v]) {

                    return `
                        <tr>
                            <td style="padding: 2px 8px 2px 0; color: #6b7280;">
                                ${k}
                            </td>
                            <td>${Peta.esc(v || '-')}</td>
                        </tr>
                    `;

                }).join('');

                bagianAlamat = `
                    <p style="margin: 6px 0;">
                        ${Peta.esc(data.display_name || '-')}
                    </p>

                    <table style="font-size: 13px;">
                        ${tabel}
                    </table>
                `;

            } catch (error) {

                console.error('Gagal mengambil alamat:', error);

            }
        }

        // ======================================
        // DAFTAR SEKOLAH TERDEKAT
        // ======================================

        let daftar = '';

        if (dekat.length === 0) {

            daftar =
                '<li>Belum ada data sekolah yang ditemukan.</li>';

        } else {

            daftar = dekat.map(function (d) {

                const npsn =
                    d.fitur.properties.npsn || '';

                const nama =
                    d.fitur.properties.nama ||
                    'Sekolah tanpa nama';

                return `
                    <li data-npsn="${Peta.esc(npsn)}">
                        <strong>${Peta.esc(nama)}</strong>
                        <br>
                        <small>
                            ${d.jarak.toFixed(2)} km (garis lurus)
                        </small>
                    </li>
                `;

            }).join('');

        }

        // ======================================
        // MENAMPILKAN HASIL KESELURUHAN
        // ======================================

        panel.innerHTML = `
            <strong>Koordinat</strong>

            <div style="font-family: monospace; margin: 4px 0 10px;">
                ${lat.toFixed(6)}, ${lng.toFixed(6)}
            </div>

            <strong>Alamat</strong>

            ${bagianAlamat}

            <strong style="display: block; margin-top: 12px;">
                Analisis Cakupan Sekolah
            </strong>

            <div style="
                background: #f0fdf4;
                border: 1px solid #bbf7d0;
                padding: 10px;
                margin-top: 6px;
                border-radius: 6px;
            ">
                Radius cakupan: <strong>1 km</strong>
                <br>
                Jumlah sekolah dalam cakupan:
                <strong>${jumlahSekolahDalamRadius} sekolah</strong>
            </div>

            <strong style="display: block; margin-top: 12px;">
                Sekolah Terdekat
            </strong>

            <ul>
                ${daftar}
            </ul>
        `;

        setTimeout(function () {
            map.invalidateSize();
        }, 100);

    }

    // ==========================================
    // 6. KLIK PETA UNTUK MENCARI ALAMAT
    // ==========================================

    map.on('click', function (e) {

        prosesTitik(e.latlng);

    });

    // ==========================================
    // 7. KLIK SEKOLAH PADA DAFTAR
    // ==========================================

    panel.addEventListener('click', function (e) {

        const li = e.target.closest('[data-npsn]');

        if (!li) return;

        const f = semua.find(function (x) {

            return String(x.properties.npsn || '') ===
                li.dataset.npsn;

        });

        if (f) {

            L.popup()
                .setLatLng(Peta.latlng(f))
                .setContent(Peta.popup(f))
                .openOn(map);

            map.flyTo(Peta.latlng(f), 17);

        }

    });

    // ==========================================
    // 8. FITUR LOKASI SAYA (GPS)
    // ==========================================

    tombolLokasi.addEventListener('click', function () {

        if (!navigator.geolocation) {

            alert('Browser Anda tidak mendukung fitur lokasi.');
            return;

        }

        tombolLokasi.disabled = true;
        tombolLokasi.textContent = 'Mencari lokasi...';

        navigator.geolocation.getCurrentPosition(

            async function (posisi) {

                try {

                    const lat = posisi.coords.latitude;
                    const lng = posisi.coords.longitude;

                    const titik = L.latLng(lat, lng);

                    // Bersihkan lokasi GPS sebelumnya
                    lapisanLokasiSaya.clearLayers();

                    // Tampilkan penanda lokasi pengguna
                    L.marker(titik)
                        .bindPopup('Lokasi Saya')
                        .addTo(lapisanLokasiSaya)
                        .openPopup();

                    // Pindahkan peta ke lokasi pengguna
                    map.setView(titik, 15);

                    // Cari alamat dan sekolah terdekat
                    await prosesTitik(titik);

                } catch (error) {

                    console.error(
                        'Kesalahan pemrosesan GPS:',
                        error
                    );

                    alert(
                        'Lokasi ditemukan, tetapi gagal menampilkan hasil.'
                    );

                } finally {

                    tombolLokasi.disabled = false;
                    tombolLokasi.textContent = 'Lokasi Saya';

                }

            },

            function (error) {

                let pesan = 'Gagal mendapatkan lokasi.';

                if (error.code === 1) {
                    pesan =
                        'Izin lokasi ditolak. Izinkan akses lokasi pada browser.';
                } else if (error.code === 2) {
                    pesan =
                        'Lokasi tidak tersedia. Periksa GPS atau koneksi perangkat.';
                } else if (error.code === 3) {
                    pesan =
                        'Waktu pencarian lokasi habis. Silakan coba lagi.';
                }

                alert(pesan);

                tombolLokasi.disabled = false;
                tombolLokasi.textContent = 'Lokasi Saya';

            },

            {
                enableHighAccuracy: true,
                timeout: 20000,
                maximumAge: 0
            }

        );

    });

    // ==========================================
    // 9. MENYESUAIKAN UKURAN PETA
    // ==========================================

    setTimeout(function () {
        map.invalidateSize();
    }, 300);

    window.addEventListener('resize', function () {
        map.invalidateSize();
    });

});
</script>
@endpush
