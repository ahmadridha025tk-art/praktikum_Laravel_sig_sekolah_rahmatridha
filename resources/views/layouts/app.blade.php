<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') - SIG Sekolah</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-main: #080e1b;
            --bg-panel: #101a2b;
            --bg-panel-light: #162338;
            --accent: #38bdf8;
            --accent-blue: #3b82f6;
            --accent-gold: #f4b323;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border-color: rgba(148, 163, 184, 0.14);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-main);
            color: var(--text-main);
        }

        /* Background ambient glow */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background:
                radial-gradient(
                    circle at 10% 0%,
                    rgba(37, 99, 235, 0.13),
                    transparent 35%
                ),
                radial-gradient(
                    circle at 90% 10%,
                    rgba(56, 189, 248, 0.08),
                    transparent 30%
                );
        }

        /* Navbar */
        .sig-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(8, 14, 27, 0.88);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
        }

        .sig-navbar-accent {
            height: 2px;
            background: linear-gradient(
                90deg,
                transparent,
                #3b82f6,
                #38bdf8,
                #f4b323,
                #38bdf8,
                #3b82f6,
                transparent
            );
        }

        .sig-nav-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 700;
            color: #a9b8cc;
            transition: all 0.25s ease;
        }

        .sig-nav-link:hover {
            color: #ffffff;
            background: rgba(255,255,255,0.05);
            border-color: rgba(148,163,184,0.12);
            transform: translateY(-1px);
        }

        .sig-nav-link.active {
            color: #7dd3fc;
            background: rgba(56,189,248,0.10);
            border-color: rgba(56,189,248,0.22);
            box-shadow: inset 0 0 15px rgba(56,189,248,0.04);
        }

        /* Brand */
        .sig-brand-logo {
            background: #ffffff;
            border: 1px solid rgba(255,255,255,0.18);
            box-shadow: 0 0 20px rgba(59,130,246,0.13);
            transition: 0.3s ease;
        }

        .sig-brand:hover .sig-brand-logo {
            transform: translateY(-2px);
            box-shadow: 0 0 25px rgba(56,189,248,0.28);
        }

        /* Logout button */
        .sig-logout-btn {
            border: 1px solid rgba(59,130,246,0.28);
            background: linear-gradient(
                135deg,
                rgba(37,99,235,0.95),
                rgba(29,78,216,0.95)
            );
            transition: all 0.25s ease;
        }

        .sig-logout-btn:hover {
            box-shadow: 0 0 20px rgba(59,130,246,0.3);
            transform: translateY(-1px);
        }

        /* Content container */
        .sig-main {
            position: relative;
            min-height: calc(100vh - 75px);
        }

        /* Notifications */
        .sig-alert {
            border-radius: 16px;
            backdrop-filter: blur(12px);
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        ::-webkit-scrollbar-track {
            background: #0b1220;
        }

        ::-webkit-scrollbar-thumb {
            background: #263851;
            border-radius: 20px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3b82f6;
        }

        /* Responsive navbar */
        @media (max-width: 768px) {
            .sig-nav-link {
                padding: 8px 10px;
                font-size: 11px;
            }

            .sig-main {
                min-height: calc(100vh - 65px);
            }
        }
    </style>
</head>

<body class="min-h-screen antialiased selection:bg-sky-500 selection:text-white">

    {{-- NAVBAR UTAMA --}}
    <nav class="sig-navbar text-white">

        {{-- Accent Line --}}
        <div class="sig-navbar-accent"></div>

        <div class="mx-auto flex max-w-[1800px] flex-wrap items-center justify-between gap-4 px-5 py-3 lg:px-8">

            {{-- BRAND --}}
            <a href="{{ url('/') }}"
               class="sig-brand group flex items-center gap-3.5">

                <div class="sig-brand-logo flex h-11 w-11 items-center justify-center rounded-xl p-1.5">
                    <img
                        src="{{ asset('images/logo-poliban.png') }}"
                        alt="Logo Poliban"
                        class="h-full w-full object-contain"
                    >
                </div>

                <div class="flex flex-col">
                    <span class="text-base font-extrabold tracking-wide text-white">
                        SIG <span class="text-sky-400">SEKOLAH</span>
                    </span>

                    <span class="text-[9px] font-semibold uppercase tracking-[2px] text-slate-500">
                        Sistem Informasi Geografis
                    </span>
                </div>
            </a>

            {{-- NAVIGATION --}}
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">

                {{-- Peta Publik --}}
                <a href="{{ url('/') }}"
                   class="sig-nav-link {{ request()->is('/') ? 'active' : '' }}">
                    Peta Publik
                </a>

                @auth

                    {{-- Dashboard --}}
                    <a href="{{ route('dashboard') }}"
                       class="sig-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>

                    {{-- Ubah Kata Sandi --}}
                    <a href="{{ route('password.edit') }}"
                       class="sig-nav-link {{ request()->routeIs('password.*') ? 'active' : '' }}">
                        Kata Sandi
                    </a>

                    {{-- MENU DINAS --}}
                    @if (auth()->user()->isDinas())
                        <a href="{{ route('dinas.sekolah.index') }}"
                           class="sig-nav-link {{ request()->routeIs('dinas.sekolah.*') ? 'active' : '' }}">
                            Data Sekolah
                        </a>
                    @endif

                    {{-- MENU SEKOLAH --}}
                    @if (auth()->user()->isSekolah() && auth()->user()->sekolah_id)
                        <a href="{{ route('sekolah.edit', auth()->user()->sekolah_id) }}"
                           class="sig-nav-link {{ request()->routeIs('sekolah.edit') ? 'active' : '' }}">
                            Sekolah Saya
                        </a>
                    @endif

                    {{-- MENU ADMIN --}}
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}"
                           class="sig-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            Pengguna
                        </a>
                    @endif

                    {{-- USER PROFILE + LOGOUT --}}
                    <div class="ml-1 flex items-center gap-3 rounded-xl border border-white/[0.08] bg-white/[0.04] p-1.5 pl-3">

                        <div class="hidden flex-col sm:flex">
                            <span class="max-w-[140px] truncate text-xs font-bold text-slate-200">
                                {{ auth()->user()->name }}
                            </span>

                            <span class="text-[9px] font-bold uppercase tracking-wider text-sky-400">
                                {{ auth()->user()->roleLabel() }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit"
                                class="sig-logout-btn rounded-lg px-3.5 py-2 text-[11px] font-bold text-white active:scale-95">
                                Keluar
                            </button>
                        </form>
                    </div>

                @endauth

            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="sig-main mx-auto max-w-[1800px] px-4 py-6 sm:px-6 lg:px-8">

        {{-- SUCCESS NOTIFICATION --}}
        @if (session('success'))
            <div class="sig-alert mb-5 flex items-center gap-3 border border-emerald-400/20 bg-emerald-500/[0.08] p-4 text-emerald-300">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-emerald-400/20 bg-emerald-500/10 font-bold">
                    ✓
                </div>

                <div class="text-sm font-semibold">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- ERROR NOTIFICATION --}}
        @if (session('error'))
            <div class="sig-alert mb-5 flex items-center gap-3 border border-red-400/20 bg-red-500/[0.08] p-4 text-red-300">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-red-400/20 bg-red-500/10 font-bold">
                    !
                </div>

                <div class="text-sm font-semibold">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- PAGE CONTENT --}}
        @yield('content')

    </main>

    {{-- LEAFLET --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')

</body>
</html>