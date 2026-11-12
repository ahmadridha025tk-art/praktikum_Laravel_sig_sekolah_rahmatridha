
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - Peta Sekolah</title>

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: system-ui, sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .bar {
            flex: 0 0 56px;
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 0 16px;
            background: #1e3a8a;
            color: #fff;
        }

        .brand {
            white-space: nowrap;
        }

        .menu {
            display: flex;
            gap: 4px;
            overflow-x: auto;
            flex: 1;
        }

        .menu a {
            color: #bfdbfe;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            white-space: nowrap;
            font-size: 14px;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, .12);
        }

        .menu a.aktif {
            background: #fff;
            color: #1e3a8a;
            font-weight: 600;
        }

        .masuk {
            color: #bfdbfe;
            white-space: nowrap;
            font-size: 14px;
        }

        main {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            padding: 10px 16px;
            font-size: 14px;
            background: #f3f4f6;
            border-bottom: 1px solid #d1d5db;
        }

        .toolbar input,
        .toolbar select,
        .toolbar button {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
        }

        .toolbar button {
            background: #1e3a8a;
            color: #fff;
            border-color: #1e3a8a;
            cursor: pointer;
        }

        .peta {
            flex: 1;
            min-height: 300px;
        }

        .panel {
            position: absolute;
            z-index: 1000;
            background: #fff;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .2);
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
                'peta.klaster' => 'Klaster',
                'peta.panas'   => 'Peta Panas',
                'peta.wilayah' => 'Wilayah',
                'peta.rute'    => 'Rute',
                'peta.alamat'  => 'Cek Lokasi',
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

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
      
        window.PETA_URL = {
            data: "{{ route('peta.data') }}",
            wilayah: "{{ route('peta.data.wilayah') }}",
        };

    </script>

    <script src="{{ asset('js/peta.js') }}"></script>

    @stack('scripts')

</body>
</html>
