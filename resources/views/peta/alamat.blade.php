@extends('layouts.public')

@section('title', 'Cek Lokasi')

@push('styles')
<style>
    /* Area toolbar */
    .toolbar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        padding: 12px;
        background: #f3f4f6;
    }

    /* Tombol lokasi */
    #lokasi-saya {
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        background: #1e3a8a;
        color: white;
        cursor: pointer;
    }

    #lokasi-saya:disabled {
        opacity: 0.65;
        cursor: wait;
    }

    /* Wadah pembungkus Peta (Kiri) dan Hasil (Kanan) */
    .wadah-utama {
        display: flex;
        align-items: stretch;
        gap: 16px;
        margin-top: 12px;
        margin-bottom: 20px;
    }

    /* Area peta di sebelah kiri */
    #peta {
        position: relative !important;
        z-index: 1;
        flex: 1;
        width: 100%;
        height: 78vh !important;
        min-height: 650px !important;
        border-radius: 8px;
        overflow: hidden;
    }

    /* Panel hasil berada di sebelah kanan peta */
    #hasil {
        position: relative !important;
        z-index: 2 !important;
        display: none;
        flex: 0 0 380px;
        max-width: 380px;
        height: 78vh;
        min-height: 650px;
        overflow-y: auto;
        box-sizing: border-box;
        background: white;
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }

    /* Responsif untuk layar HP/Tablet kecil */
    @media (max-width: 900px) {
        .wadah-utama {
            flex-direction: column;
        }
        #hasil {
            flex: none;
            width: 100%;
            max-width: 100%;
            height: auto;
            min-height: auto;
        }
    }

    /* Pastikan kontrol Leaflet tetap terlihat */
    .leaflet-container {
        position: relative;
        z-index: 1;
        width: 100% !important;
        height: 100% !important;
    }

    /* Memperbaiki gambar tile Leaflet */
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

    #hasil ul {
        list-style: none;
        margin: 4px 0 0;
        padding: 0;
    }

    #hasil li[data-npsn] {
        padding: 8px 0;
        border-top: 1px solid #e5e7eb;
        cursor: pointer;
    }

    #hasil li[data-npsn]:hover {
        background: #f3f4f6;
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

        // Tampilkan panel di sebelah kanan peta & sesuaikan ukuran peta
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

    // Sesuaikan ukuran peta setelah halaman selesai dibuat
    setTimeout(function () {
        map.invalidateSize();
    }, 300);

    window.addEventListener('resize', function () {
        map.invalidateSize();
    });

});
</script>
@endpush