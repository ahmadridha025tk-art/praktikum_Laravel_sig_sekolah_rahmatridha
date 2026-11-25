<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - Peta Sekolah</title>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- Leaflet -->
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        :root {
            --navy-dark: #10182D;
            --navy-soft: #1B2742;

            --royal-blue: #2414D8;
            --royal-hover: #3829E8;

            --gold-accent: #F4B323;

            --glass-bg: rgba(255, 255, 255, 0.92);

            --text-main: #0F172A;
            --text-muted: #64748B;

            --border-color: #E2E8F0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;

            font-family: 'Plus Jakarta Sans',
                system-ui,
                -apple-system,
                sans-serif;

            background: #F4F6FB;
            color: var(--text-main);

            -webkit-font-smoothing: antialiased;
        }

        body {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* =========================
           NAVBAR
        ========================= */

        .bar {
            flex: 0 0 66px;

            display: flex;
            align-items: center;
            gap: 20px;

            padding: 0 24px;

            background: var(--navy-dark);
            color: #ffffff;

            position: relative;
            z-index: 1100;

            box-shadow:
                0 10px 25px -5px rgba(16, 24, 45, 0.25);
        }

        .bar::before {
            content: "";

            position: absolute;
            top: 0;
            left: 0;
            right: 0;

            height: 3px;

            background: linear-gradient(
                90deg,
                var(--royal-blue),
                var(--gold-accent),
                var(--royal-blue)
            );
        }

        /* BRAND */

        .brand {
            white-space: nowrap;

            font-size: 17px;
            font-weight: 800;
            letter-spacing: 0.4px;

            display: flex;
            align-items: center;
            gap: 10px;

            color: #ffffff;
        }

        .brand::before {
            content: "📍";

            display: flex;
            align-items: center;
            justify-content: center;

            width: 34px;
            height: 34px;

            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);

            border-radius: 10px;

            font-size: 16px;
        }

        /* NAVIGATION */

        .menu {
            display: flex;
            align-items: center;
            gap: 6px;

            flex: 1;

            overflow-x: auto;
            scrollbar-width: none;

            padding: 4px 0;
        }

        .menu::-webkit-scrollbar {
            display: none;
        }

        .menu a {
            color: #CBD5E1;
            text-decoration: none;

            padding: 8px 14px;

            border-radius: 10px;

            white-space: nowrap;

            font-size: 13.5px;
            font-weight: 600;

            transition: all 0.2s ease;

            border: 1px solid transparent;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--gold-accent);
        }

        .menu a.aktif {
            background: var(--royal-blue);
            color: #ffffff;

            font-weight: 700;

            border-color: rgba(255, 255, 255, 0.1);

            box-shadow:
                0 4px 14px rgba(36, 20, 216, 0.35);
        }

        /* LOGIN / DASHBOARD */

        .masuk {
            color: #ffffff;

            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);

            text-decoration: none;

            padding: 8px 16px;

            border-radius: 10px;

            white-space: nowrap;

            font-size: 13.5px;
            font-weight: 700;

            transition: all 0.2s ease;
        }

        .masuk:hover {
            background: var(--gold-accent);
            color: var(--navy-dark);

            border-color: var(--gold-accent);

            transform: translateY(-1px);
        }

        /* =========================
           MAIN CONTENT
        ========================= */

        main {
            flex: 1;
            min-height: 0;

            display: flex;
            flex-direction: column;

            position: relative;
        }

        /* =========================
           TOOLBAR MAP
        ========================= */

        .toolbar {
            position: absolute;

            top: 16px;
            left: 60px;
            right: 16px;

            z-index: 1000;

            display: flex;
            flex-wrap: wrap;
            align-items: center;

            gap: 10px 14px;

            padding: 12px 18px;

            font-size: 13.5px;
            font-weight: 600;

            color: var(--text-main);

            background: var(--glass-bg);

            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);

            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 16px;

            box-shadow:
                0 12px 32px -4px rgba(16, 24, 45, 0.14),
                0 2px 6px -1px rgba(16, 24, 45, 0.06);

            width: fit-content;
            max-width: calc(100% - 76px);
        }

        .toolbar label {
            color: var(--text-muted);

            font-size: 12.5px;
            font-weight: 700;
        }

        .toolbar input[type="text"],
        .toolbar input[type="number"],
        .toolbar input[type="search"],
        .toolbar select {
            background: #ffffff;
            color: var(--text-main);

            border: 1px solid #CBD5E1;
            border-radius: 10px;

            padding: 8px 12px;

            font-family: inherit;
            font-size: 13.5px;
            font-weight: 600;

            outline: none;

            transition: all 0.2s ease;
        }

        .toolbar input:focus,
        .toolbar select:focus {
            border-color: var(--royal-blue);

            box-shadow:
                0 0 0 3px rgba(36, 20, 216, 0.12);
        }

        .toolbar input[type="range"] {
            accent-color: var(--royal-blue);

            cursor: pointer;

            height: 6px;

            border-radius: 999px;

            background: #E2E8F0;

            padding: 0;
            border: none;
        }

        .toolbar button {
            background: var(--royal-blue);
            color: #ffffff;

            border: none;
            border-radius: 10px;

            padding: 9px 16px;

            font-family: inherit;
            font-size: 13.5px;
            font-weight: 700;

            cursor: pointer;

            transition: all 0.2s ease;

            box-shadow:
                0 4px 12px rgba(36, 20, 216, 0.20);
        }

        .toolbar button:hover {
            background: var(--royal-hover);

            transform: translateY(-1px);

            box-shadow:
                0 6px 16px rgba(36, 20, 216, 0.30);
        }

        /* =========================
           MAP
        ========================= */

        .peta {
            flex: 1;

            width: 100%;
            height: 100%;

            min-height: 300px;

            z-index: 1;
        }

        /* =========================
           FLOATING PANEL
        ========================= */

        .panel {
            position: absolute;

            z-index: 1000;

            background: var(--glass-bg);

            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);

            border: 1px solid rgba(255, 255, 255, 0.85);
            border-radius: 16px;

            padding: 16px 18px;

            font-size: 13.5px;

            color: var(--text-main);

            box-shadow:
                0 12px 32px -4px rgba(16, 24, 45, 0.16);
        }

        /* =========================
           LEAFLET CONTROL
        ========================= */

        .leaflet-top.leaflet-left {
            margin-top: 8px;
            margin-left: 8px;
        }

        .leaflet-bar {
            border: none !important;
            border-radius: 12px !important;

            overflow: hidden;

            box-shadow:
                0 8px 24px rgba(16, 24, 45, 0.14) !important;
        }

        .leaflet-bar a {
            background: rgba(255, 255, 255, 0.96) !important;

            backdrop-filter: blur(10px);

            color: var(--navy-dark) !important;

            border-bottom: 1px solid #E2E8F0 !important;

            width: 36px !important;
            height: 36px !important;
            line-height: 36px !important;

            font-weight: 700 !important;

            transition: all 0.15s ease;
        }

        .leaflet-bar a:hover {
            background: var(--royal-blue) !important;
            color: #ffffff !important;
        }

        /* LAYER CONTROL */

        .leaflet-control-layers {
            background: rgba(255, 255, 255, 0.96) !important;

            backdrop-filter: blur(12px);

            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            border-radius: 14px !important;

            padding: 10px 14px !important;

            box-shadow:
                0 10px 28px rgba(16, 24, 45, 0.14) !important;

            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 13px !important;
            font-weight: 600 !important;

            color: var(--text-main) !important;
        }

        /* POPUP MAP */

        .leaflet-popup-content-wrapper {
            border-radius: 14px !important;

            box-shadow:
                0 12px 28px rgba(16, 24, 45, 0.18) !important;

            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 768px) {

            .bar {
                flex: 0 0 auto;

                min-height: 62px;

                padding: 8px 14px;

                gap: 12px;

                flex-wrap: wrap;
            }

            .brand {
                font-size: 15px;
            }

            .brand::before {
                width: 30px;
                height: 30px;
            }

            .menu {
                gap: 4px;
            }

            .menu a {
                padding: 7px 10px;
                font-size: 12px;
            }

            .masuk {
                padding: 7px 12px;
                font-size: 12px;
            }

            .toolbar {
                left: 12px;
                right: 12px;

                top: auto;
                bottom: 24px;

                max-width: calc(100% - 24px);
                width: auto;

                padding: 12px;

                gap: 8px;
            }

        }

        @media (max-width: 480px) {

            .bar {
                gap: 8px;
            }

            .brand {
                font-size: 14px;
            }

            .menu a {
                font-size: 11.5px;
                padding: 7px 9px;
            }

            .toolbar {
                border-radius: 14px;
            }

        }

    </style>

    @stack('styles')

</head>

<body>

    <header class="bar">

        <strong class="brand">Peta Sekolah</strong>

        @php
            $menu = [
                'peta.index'   => 'Peta Sekolah',
                'peta.cari'    => 'Pencarian',
                'peta.klaster'  => 'Klaster',
                'peta.panas'   => 'Peta Panas',
                'peta.wilayah' => 'Wilayah',
                'peta.rute'    => 'Rute',
                'peta.alamat'  => 'Cek Lokasi',
                'health.check' => 'Health', // <-- BARIS 500 (BARU)
            ];
        @endphp

        <nav class="menu">

            @foreach ($menu as $nama => $label)

                @if (Route::has($nama))

                    <a href="{{ route($nama) }}"
                       @class(['aktif' => request()->routeIs($nama)])>

                        {{ $label }}

                    </a>

                @endif

            @endforeach

        </nav>

        @auth

            <a class="masuk" href="{{ route('dashboard') }}">
                Dashboard
            </a>

        @else

            <a class="masuk" href="{{ route('login') }}">
                Masuk
            </a>

        @endauth

    </header>

    <main>

        @yield('content')

    </main>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- URL API PETA -->
    <script>

        window.PETA_URL = {
            data: "{{ route('peta.data') }}",
            wilayah: "{{ route('peta.data.wilayah') }}",
        };

    </script>

    <!-- Script Peta -->
    <script src="{{ asset('js/peta.js') }}"></script>

    @stack('scripts')

</body>
</html>


