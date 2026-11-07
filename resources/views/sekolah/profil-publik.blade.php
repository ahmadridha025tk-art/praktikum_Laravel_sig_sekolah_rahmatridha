
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
            background: #1e3a8a;
            color: white;
            padding: 25px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 26px;
        }

        header p {
            margin-bottom: 0;
            color: #dbeafe;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 16px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .identitas {
            text-align: center;
        }

        .logo {
            width: 130px;
            height: 130px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .tanpa-logo {
            width: 130px;
            height: 130px;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border-radius: 10px;
            color: #64748b;
        }

        h2 {
            color: #1e3a8a;
            margin-top: 0;
        }

        .data {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 12px;
        }

        .label {
            font-weight: bold;
        }

        .deskripsi {
            line-height: 1.7;
            white-space: pre-line;
        }

        .tombol {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
        }

        .tombol:hover {
            background: #1d4ed8;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #64748b;
        }

        @media (max-width: 600px) {
            .data {
                grid-template-columns: 1fr;
                gap: 5px;
            }

            .label:not(:first-child) {
                margin-top: 12px;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>Profil Sekolah</h1>
    <p>Informasi dan Data Sekolah</p>
</header>

<div class="container">

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

        <p>
            {{ $sekolah->jenjang }} -
            {{ $sekolah->status }}
        </p>

    </div>

    <div class="card">

        <h2>Identitas Sekolah</h2>

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

    <div class="card">

        <h2>Informasi Profil Sekolah</h2>

        @php
            $profil = $sekolah->profil;
        @endphp

        <div class="data">

            <div class="label">Kepala Sekolah</div>
            <div>{{ $profil?->kepala_sekolah ?: '-' }}</div>

            <div class="label">Email</div>
            <div>{{ $profil?->email ?: '-' }}</div>

            <div class="label">Website</div>
            <div>
                @if ($profil?->website)
                    <a href="{{ $profil->website }}" target="_blank" rel="noopener noreferrer">
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

            <div class="label">Jumlah Siswa</div>
            <div>{{ $profil?->jumlah_siswa ?? '-' }}</div>

            <div class="label">Jumlah Guru</div>
            <div>{{ $profil?->jumlah_guru ?? '-' }}</div>

        </div>

    </div>

    <div class="card">

        <h2>Deskripsi Sekolah</h2>

        <div class="deskripsi">
            {{ $profil?->deskripsi ?: 'Deskripsi sekolah belum tersedia.' }}
        </div>

    </div>

    <div style="text-align: center;">
        <a href="{{ route('peta.index') }}" class="tombol">
            Kembali ke Peta Sekolah
        </a>
    </div>

</div>

<footer>
    SIG Sekolah &copy; {{ date('Y') }}
</footer>

</body>
</html>
