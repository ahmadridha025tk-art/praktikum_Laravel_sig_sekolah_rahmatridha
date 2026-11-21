<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - SIG Sekolah</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#F4F6FB] text-[#10182D] antialiased">

    {{-- NAVBAR UTAMA --}}
    <nav class="relative z-50 border-b border-white/10 bg-[#10182D] text-white shadow-xl shadow-[#10182D]/10">

        {{-- Gold accent line --}}
        <div class="h-1 w-full bg-gradient-to-r from-[#2414D8] via-[#F4B323] to-[#2414D8]"></div>

        <div class="mx-auto flex max-w-[1600px] flex-wrap items-center justify-between gap-4 px-5 py-4 lg:px-8">

            {{-- BRAND + LOGO --}}
            <a href="{{ url('/') }}" class="group flex items-center gap-3.5">

                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white p-1.5 shadow-lg shadow-black/10 transition duration-300 group-hover:scale-105">
                    <img
                        src="{{ asset('images/logo-poliban.png') }}"
                        alt="Logo Poliban"
                        class="h-full w-full object-contain"
                    >
                </div>

                <div class="flex flex-col">
                    <span class="text-lg font-extrabold tracking-wide text-white">
                        SIG <span class="text-[#F4B323]">SEKOLAH</span>
                    </span>

                    <span class="text-[10px] font-medium uppercase tracking-[2px] text-slate-400 sm:text-xs">
                        Sistem Informasi Geografis
                    </span>
                </div>
            </a>

            {{-- NAVIGATION --}}
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">

                {{-- Peta Publik --}}
                <a href="{{ url('/') }}"
                   class="rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200
                   hover:bg-white/10 hover:text-[#F4B323]">
                    Peta Publik
                </a>

                @auth

                    {{-- Dashboard --}}
                    <a href="{{ route('dashboard') }}"
                       class="rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200
                       hover:bg-white/10 hover:text-[#F4B323]">
                        Dashboard
                    </a>

                    {{-- Ubah Kata Sandi --}}
                    <a href="{{ route('password.edit') }}"
                       class="rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200
                       hover:bg-white/10 hover:text-[#F4B323]">
                        Kata Sandi
                    </a>

                    {{-- MENU DINAS --}}
                    @if (auth()->user()->isDinas())
                        <a href="{{ route('dinas.sekolah.index') }}"
                           class="rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200
                           hover:bg-white/10 hover:text-[#F4B323]">
                            Data Sekolah
                        </a>
                    @endif

                    {{-- MENU SEKOLAH --}}
                    @if (auth()->user()->isSekolah() && auth()->user()->sekolah_id)
                        <a href="{{ route('sekolah.edit', auth()->user()->sekolah_id) }}"
                           class="rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200
                           hover:bg-white/10 hover:text-[#F4B323]">
                            Sekolah Saya
                        </a>
                    @endif

                    {{-- MENU ADMIN --}}
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}"
                           class="rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200
                           hover:bg-white/10 hover:text-[#F4B323]">
                            Pengguna
                        </a>
                    @endif

                    {{-- USER PROFILE + LOGOUT --}}
                    <div class="ml-1 flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-1.5 pl-3">

                        <div class="hidden flex-col sm:flex">
                            <span class="max-w-[140px] truncate text-xs font-bold text-white">
                                {{ auth()->user()->name }}
                            </span>
                            <span class="text-[10px] text-[#F4B323]">
                                {{ auth()->user()->roleLabel() }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit"
                                class="rounded-xl bg-[#2414D8] px-4 py-2 text-xs font-bold text-white
                                       transition duration-200 hover:bg-[#3829E8] hover:shadow-lg hover:shadow-[#2414D8]/30">
                                Keluar
                            </button>
                        </form>
                    </div>

                @endauth

            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="mx-auto min-h-[calc(100vh-100px)] max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8">

        {{-- SUCCESS NOTIFICATION --}}
        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100">
                    ✓
                </div>

                <div class="pt-1 text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- ERROR NOTIFICATION --}}
        @if (session('error'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-red-100">
                    !
                </div>

                <div class="pt-1 text-sm font-medium">
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