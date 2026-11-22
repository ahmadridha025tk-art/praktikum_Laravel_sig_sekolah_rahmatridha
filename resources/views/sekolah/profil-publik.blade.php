<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Profil {{ $sekolah->nama }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        header {
            background: linear-gradient(135deg, #1e40af, #2563eb, #0891b2);
            color: white;
            padding: 50px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 800;
        }

        header p {
            margin: 10px 0 0;
            color: #dbeafe;
            font-size: 15px;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 28px;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 5px 20px rgba(15, 23, 42, 0.04);
            margin-bottom: 22px;
        }

        .identitas {
            text-align: center;
            padding: 35px 25px;
        }

        .logo {
            width: 125px;
            height: 125px;
            object-fit: contain;
            margin-bottom: 15px;
            padding: 8px;
            background: #f8fafc;
            border-radius: 20px;
        }

        .tanpa-logo {
            width: 125px;
            height: 125px;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border-radius: 20px;
            color: #64748b;
            font-size: 13px;
        }

        .identitas h2 {
            margin: 5px 0 12px;
            font-size: 25px;
            color: #1e3a8a;
        }

        .identitas p {
            color: #64748b;
            margin: 6px 0;
        }

        .badge {
            display: inline-block;
            padding: 7px 13px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 600;
            margin: 4px;
        }

        h2.section-title {
            color: #1e3a8a;
            margin: 0 0 22px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h2.section-title::before {
            content: "";
            width: 4px;
            height: 22px;
            background: #2563eb;
            border-radius: 10px;
        }

        .data {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 16px;
        }

        .label {
            font-weight: 600;
            color: #64748b;
        }

        .data > div:not(.label) {
            color: #1e293b;
            overflow-wrap: anywhere;
        }

        .data a {
            color: #2563eb;
            text-decoration: none;
        }

        .data a:hover {
            text-decoration: underline;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-top: 25px;
        }

        .stat {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
        }

        .stat .angka {
            font-size: 28px;
            font-weight: 800;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .stat .nama {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
        }

        .deskripsi {
            line-height: 1.8;
            white-space: pre-line;
            color: #475569;
        }

        .tombol-container {
            text-align: center;
            margin: 30px 0;
        }

        .tombol {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #2563eb;
            color: white;
            padding: 13px 22px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .tombol:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        footer {
            text-align: center;
            padding: 25px 15px;
            color: #94a3b8;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            header {
                padding: 35px 15px;
            }

            header h1 {
                font-size: 25px;
            }

            .container {
                margin: 20px auto;
                padding: 0 12px;
            }

            .card {
                padding: 20px;
                border-radius: 15px;
            }

            .identitas h2 {
                font-size: 21px;
            }

            .data {
                grid-template-columns: 1fr;
                gap: 6px;
            }

            .data .label:not(:first-child) {
                margin-top: 12px;
            }

            .stat-grid {
                gap: 10px;
            }

            .stat {
                padding: 15px 10px;
            }

            .stat .angka {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>Profil Sekolah</h1>
    <p>Informasi lengkap dan identitas sekolah</p>
</header>

<div class="container">

    {{-- Identitas Utama --}}
    <div class="card identitas">

        @if ($sekolah->profil && $sekolah->profil->logo)
            <img
                src="{{ asset('storage/' . $sekolah->profil->logo) }}"
                alt="Logo {{ $sekolah->nama }}"
                class="logo"
            >
        @else
            <div class="tanpa-logo">
                Logo belum tersedia
            </div>
        @endif

        <h2>{{ $sekolah->nama }}</h2>

        <p>NPSN: {{ $sekolah->npsn }}</p>

        <div>
            <span class="badge">{{ $sekolah->jenjang }}</span>
            <span class="badge">{{ $sekolah->status }}</span>
        </div>

    </div>

    {{-- Identitas Sekolah --}}
    <div class="card">

        <h2 class="section-title">Identitas Sekolah</h2>

        <div class="data">
            <div class="label">Nama Sekolah</div>
            <div>{{ $sekolah->nama }}</div>

            <div class="label">NPSN</div>
            <div>{{ $sekolah->npsn }}</div>

            <div class="label">Jenjang</div>
            <div>{{ $sekolah->jenjang }}</div>

            <div class="label">Status</div>
            <div>{{ $sekolah->status }}</div>

            <div class="label">Alamat</div>
            <div>{{ $sekolah->alamat }}</div>

            <div class="label">Kecamatan</div>
            <div>{{ $sekolah->kecamatan }}</div>

            <div class="label">Telepon</div>
            <div>{{ $sekolah->telepon ?: '-' }}</div>
        </div>

    </div>

    {{-- Profil Sekolah --}}
    @php
        $profil = $sekolah->profil;
    @endphp

    <div class="card">

        <h2 class="section-title">Informasi Profil Sekolah</h2>

        <div class="data">

            <div class="label">Kepala Sekolah</div>
            <div>{{ $profil?->kepala_sekolah ?: '-' }}</div>

            <div class="label">Email</div>
            <div>{{ $profil?->email ?: '-' }}</div>

            <div class="label">Website</div>
            <div>
                @if ($profil?->website)
                    <a
                        href="{{ $profil->website }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        {{ $profil->website }}
                    </a>
                @else
                    -
                @endif
            </div>

            <div class="label">Tahun Berdiri</div>
            <div>{{ $profil?->tahun_berdiri ?: '-' }}</div>

            <div class="label">Akreditasi</div>
            <div>{{ $profil?->akreditasi ?: '-' }}</div>

        </div>

        {{-- Statistik Sekolah --}}
        <div class="stat-grid">

            <div class="stat">
                <div class="angka">
                    {{ $profil?->jumlah_siswa ?? '-' }}
                </div>
                <div class="nama">Jumlah Siswa</div>
            </div>

            <div class="stat">
                <div class="angka">
                    {{ $profil?->jumlah_guru ?? '-' }}
                </div>
                <div class="nama">Jumlah Guru</div>
            </div>

        </div>

    </div>

    {{-- Deskripsi --}}
    <div class="card">

        <h2 class="section-title">Deskripsi Sekolah</h2>

        <div class="deskripsi">
            {{ $profil?->deskripsi ?: 'Deskripsi sekolah belum tersedia.' }}
        </div>

    </div>

    {{-- Tombol Kembali --}}
    <div class="tombol-container">
        <a href="{{ route('peta.index') }}" class="tombol">
            ← Kembali ke Peta Sekolah
        </a>
    </div>

</div>

<footer>
    SIG Sekolah &copy; {{ date('Y') }}
</footer>

</body>
</html>
