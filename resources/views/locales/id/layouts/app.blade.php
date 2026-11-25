<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC] text-[#0F172A]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CargoTrace') - Visibilitas & Pelacakan Pengiriman Global</title>

    <!-- Google Fonts: Plus Jakarta Sans / JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        ocean: {
                            50: '#F0F7FF',
                            100: '#E0F2FE',
                            200: '#BAE6FD',
                            300: '#7DD3FC',
                            400: '#38BDF8',
                            500: '#0EA5E9',
                            600: '#0284C7',
                            700: '#1D4ED8',
                            800: '#1E40AF',
                            900: '#163B65',
                            950: '#0B132B',
                        },
                        navy: {
                            800: '#1E293B',
                            850: '#15253E',
                            900: '#0F172A',
                            950: '#0B132B',
                            dark: '#080E1A',
                        }
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Leaflet CSS & JS CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            color: #0F172A;
        }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .leaflet-popup-content-wrapper {
            background-color: #0B132B !important;
            color: #FFFFFF !important;
            border-radius: 0.75rem !important;
            border: 1px solid #1E3A8A !important;
            box-shadow: 0 10px 25px -3px rgba(11, 19, 43, 0.4) !important;
        }
        .leaflet-popup-tip {
            background-color: #0B132B !important;
        }
        .leaflet-container {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        /* Custom scrollbar in Ocean Maritime tones */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0B132B; }
        ::-webkit-scrollbar-thumb { background: #1E3A8A; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #38BDF8; }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-[#F8FAFC] text-[#0F172A] antialiased flex flex-col md:flex-row">

    <!-- Sidebar Navigation (Ocean Maritime Style) -->
    <aside class="w-full md:w-64 bg-[#0B132B] border-r border-[#1E293B] shrink-0 flex flex-col justify-between h-auto md:min-h-screen text-slate-300">
        <div class="p-5">
            <!-- App Brand Header -->
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-[#1E3A8A] border border-[#38BDF8]/40 flex items-center justify-center text-white shadow-lg shadow-[#1E3A8A]/40">
                    <i class="fa-solid fa-ship text-base"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-white leading-tight">CargoTrace</h1>
                    <p class="text-[11px] text-slate-400 font-medium">Visibilitas Pengiriman</p>
                </div>
            </div>

            <!-- Navigation Links by Category -->
            <nav class="space-y-5">
                <!-- Group 1: OVERVIEW -->
                <div class="space-y-1">
                    <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                        <span>Ringkasan</span>
                    </p>
                    
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="group relative flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-[#1E293B] text-white font-bold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('dashboard') ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-white/5 text-slate-400 group-hover:bg-white/10 group-hover:text-white' }}">
                                <i class="fa-solid fa-grip"></i>
                            </div>
                            <span>Dashboard</span>
                        </div>
                        @if(request()->routeIs('dashboard'))
                        <span class="w-2 h-2 rounded-full bg-[#38BDF8] shadow-xs shadow-sky-400/50"></span>
                        @endif
                    </a>

                    <!-- Live Tracker -->
                    <a href="{{ route('public.track') }}" class="group relative flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('public.track*') ? 'bg-[#1E293B] text-white font-bold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('public.track*') ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-white/5 text-slate-400 group-hover:bg-white/10 group-hover:text-white' }}">
                                <i class="fa-solid fa-earth-americas"></i>
                            </div>
                            <span>Pelacak Langsung</span>
                        </div>
                        @if(request()->routeIs('public.track*'))
                        <span class="w-2 h-2 rounded-full bg-[#38BDF8] shadow-xs shadow-sky-400/50"></span>
                        @else
                        <span class="relative flex h-2 w-2 mr-1">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#38BDF8]"></span>
                        </span>
                        @endif
                    </a>

                    @auth
                    <!-- Notifications -->
                    <a href="{{ route('notifications.index') }}" class="group relative flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('notifications.*') ? 'bg-[#1E293B] text-white font-bold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('notifications.*') ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-white/5 text-slate-400 group-hover:bg-white/10 group-hover:text-white' }}">
                                <i class="fa-regular fa-bell"></i>
                            </div>
                            <span>Notifikasi</span>
                        </div>
                        @if(($unreadNotificationsCount ?? 0) > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EF4444] text-white shadow-xs">
                            {{ $unreadNotificationsCount }}
                        </span>
                        @endif
                    </a>
                    @endauth
                </div>

                <!-- Group 2: LOGISTICS & OPERATIONS -->
                <div class="space-y-1">
                    <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                        <span>Logistik</span>
                    </p>
                    <a href="{{ route('shipments.index') }}" class="group relative flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('shipments.*') ? 'bg-[#1E293B] text-white font-bold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('shipments.*') ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-white/5 text-slate-400 group-hover:bg-white/10 group-hover:text-white' }}">
                                <i class="fa-solid fa-box-archive"></i>
                            </div>
                            <span>{{ Auth::check() && Auth::user()->isCustomer() ? 'Pengiriman Saya' : 'Daftar Pengiriman' }}</span>
                        </div>
                        @if(request()->routeIs('shipments.*'))
                        <span class="w-2 h-2 rounded-full bg-[#38BDF8] shadow-xs shadow-sky-400/50"></span>
                        @endif
                    </a>
                </div>

                <!-- Group 3: MASTER DATA (Administrator only) -->
                @if(Auth::check() && Auth::user()->isAdmin())
                <div class="space-y-1">
                    <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                        <span>Data Master</span>
                    </p>
                    <a href="{{ route('users.index') }}" class="group relative flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('users.*') ? 'bg-[#1E293B] text-white font-bold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('users.*') ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-white/5 text-slate-400 group-hover:bg-white/10 group-hover:text-white' }}">
                                <i class="fa-solid fa-users-gear"></i>
                            </div>
                            <span>Manajemen Pengguna</span>
                        </div>
                        @if(request()->routeIs('users.*'))
                        <span class="w-2 h-2 rounded-full bg-[#38BDF8] shadow-xs shadow-sky-400/50"></span>
                        @endif
                    </a>
                    <a href="{{ route('carriers.index') }}" class="group relative flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('carriers.*') ? 'bg-[#1E293B] text-white font-bold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('carriers.*') ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-white/5 text-slate-400 group-hover:bg-white/10 group-hover:text-white' }}">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <span>Operator Pengangkutan</span>
                        </div>
                        @if(request()->routeIs('carriers.*'))
                        <span class="w-2 h-2 rounded-full bg-[#38BDF8] shadow-xs shadow-sky-400/50"></span>
                        @endif
                    </a>
                    <a href="{{ route('locations.index') }}" class="group relative flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('locations.*') ? 'bg-[#1E293B] text-white font-bold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('locations.*') ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-white/5 text-slate-400 group-hover:bg-white/10 group-hover:text-white' }}">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <span>Pelabuhan Dunia</span>
                        </div>
                        @if(request()->routeIs('locations.*'))
                        <span class="w-2 h-2 rounded-full bg-[#38BDF8] shadow-xs shadow-sky-400/50"></span>
                        @endif
                    </a>
                </div>

                <!-- Group 4: CONFIGURATION & SYSTEM (Administrator only) -->
                <div class="space-y-1">
                    <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                        <span>Sistem & Konfigurasi</span>
                    </p>
                    <a href="{{ route('currencies.index') }}" class="group relative flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('currencies.*') ? 'bg-[#1E293B] text-white font-bold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('currencies.*') ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-white/5 text-slate-400 group-hover:bg-white/10 group-hover:text-white' }}">
                                <i class="fa-solid fa-coins"></i>
                            </div>
                            <span>Kurs Mata Uang</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-[#1E293B] text-sky-300 border border-slate-700">
                            {{ $activeCurrencyCode ?? 'USD' }}
                        </span>
                    </a>
                    <a href="{{ route('audit-logs.index') }}" class="group relative flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('audit-logs.*') ? 'bg-[#1E293B] text-white font-bold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('audit-logs.*') ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-white/5 text-slate-400 group-hover:bg-white/10 group-hover:text-white' }}">
                                <i class="fa-solid fa-shield"></i>
                            </div>
                            <span>Log Audit</span>
                        </div>
                        @if(request()->routeIs('audit-logs.*'))
                        <span class="w-2 h-2 rounded-full bg-[#38BDF8] shadow-xs shadow-sky-400/50"></span>
                        @endif
                    </a>
                </div>
                @endif
            </nav>
        </div>

        <!-- User Profile Card at bottom of Sidebar -->
        <div class="p-4 border-t border-[#1E293B] bg-[#080E1A]">
            @auth
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-[#1E3A8A] text-white border border-sky-400/30 flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-white truncate leading-tight">{{ Auth::user()->name }}</p>
                        <span class="inline-block text-[9.5px] font-bold text-sky-300 bg-[#15253E] px-1.5 py-0.2 rounded border border-sky-500/30 uppercase tracking-wide">
                            {{ Auth::user()->role === 'admin' ? 'Administrator' : (Auth::user()->role === 'operator' ? 'Operator Logistik' : 'Pelanggan') }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="Keluar" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-white/5 transition">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
            @else
            <a href="{{ route('login') }}" class="w-full flex items-center justify-center py-2 px-3 text-xs font-semibold text-white bg-[#1E3A8A] hover:bg-[#1D4ED8] rounded-xl transition shadow-sm border border-sky-400/30">
                Masuk ke Konsol
            </a>
            @endauth
        </div>
    </aside>

    <!-- Main Content Area with Top Header Bar -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- Top Header Bar -->
        <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 px-6 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-30">
            <!-- Breadcrumbs / Title Context -->
            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <i class="fa-regular fa-compass text-slate-400"></i>
                <a href="{{ route('dashboard') }}" class="hover:text-blue-900 transition">Dashboard</a>
                <span class="text-slate-300">&gt;</span>
                <span class="text-[#0F172A] font-semibold">@yield('title', 'Ringkasan')</span>
            </div>

            <!-- Header Right: Language, Global Currency Selector, Search bar, Notification Bell -->
            <div class="flex items-center gap-3">
                <!-- Global Language Switcher Dropdown (Ocean Maritime Dark Pill) -->
                <form action="{{ route('language.switch') }}" method="POST" class="inline-flex items-center">
                    @csrf
                    <div class="flex items-center bg-[#0B132B] hover:bg-[#15253E] border border-[#1E293B] rounded-xl px-2.5 py-1.5 text-xs text-white transition shadow-xs">
                        <i class="fa-solid fa-globe text-[#38BDF8] mr-1.5 text-xs"></i>
                        <select name="locale" onchange="this.form.submit()" class="bg-transparent text-xs font-semibold focus:outline-none cursor-pointer pr-1 text-white">
                            <option value="id" {{ ($currentLocale ?? 'id') === 'id' ? 'selected' : '' }} class="bg-[#0B132B] text-white">🇮🇩 ID</option>
                            <option value="en" {{ ($currentLocale ?? 'id') === 'en' ? 'selected' : '' }} class="bg-[#0B132B] text-white">🇬🇧 EN</option>
                        </select>
                    </div>
                </form>

                <!-- Global Currency Switcher Dropdown (Ocean Maritime Dark Pill) -->
                <form action="{{ route('currency.switch') }}" method="POST" class="inline-flex items-center">
                    @csrf
                    <div class="flex items-center bg-[#0B132B] hover:bg-[#15253E] border border-[#1E293B] rounded-xl px-2.5 py-1.5 text-xs text-white transition shadow-xs">
                        <i class="fa-solid fa-coins text-[#38BDF8] mr-1.5 text-xs"></i>
                        <select name="currency_code" onchange="this.form.submit()" class="bg-transparent text-xs font-semibold focus:outline-none cursor-pointer pr-1 text-white">
                            @foreach($globalCurrencies ?? [] as $curr)
                                <option value="{{ $curr->code }}" {{ ($activeCurrencyCode ?? 'USD') === $curr->code ? 'selected' : '' }} class="bg-[#0B132B] text-white">
                                    {{ $curr->code }} ({{ $curr->symbol }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <!-- Search Input Shortcut (Ocean Maritime Dark Style) -->
                <form action="{{ route('shipments.index') }}" method="GET" class="relative hidden sm:block">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" placeholder="Cari pengiriman..."
                           class="w-48 pl-8 pr-8 py-1.5 text-xs bg-[#0B132B] border border-[#1E293B] rounded-xl text-white placeholder-slate-400 focus:outline-none focus:border-[#38BDF8] transition shadow-xs">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 text-[10px] font-mono text-slate-400">⌘K</span>
                </form>

                <!-- Header Actions (Notification Bell) -->
                @auth
                <a href="{{ route('notifications.index') }}" class="relative p-2 text-slate-600 hover:text-[#0F172A] hover:bg-slate-100 rounded-xl border border-transparent hover:border-slate-200 transition" title="Notifikasi">
                    <i class="fa-regular fa-bell text-sm"></i>
                    @if(($unreadNotificationsCount ?? 0) > 0)
                    <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-[#EF4444] text-[10px] font-bold text-white shadow-xs">
                        {{ $unreadNotificationsCount }}
                    </span>
                    @endif
                </a>
                @endauth
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-6 sm:px-8 pt-4 w-full">
            @if(session('success'))
            <div class="flex items-center gap-3 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs shadow-xs mb-4">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('info'))
            <div class="flex items-center gap-3 p-3.5 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 text-xs shadow-xs mb-4">
                <i class="fa-solid fa-circle-info text-sky-600 text-sm"></i>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs mb-4">
                <div class="flex items-center gap-2 font-bold mb-1 text-rose-900">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Harap periksa kesalahan di bawah ini:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-800 ml-4">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <!-- Main Page Body -->
        <main class="flex-1 px-6 sm:px-8 py-6 w-full max-w-7xl">
            @yield('content')
        </main>

        <!-- Minimal Footer -->
        <footer class="mt-auto border-t border-slate-200 bg-white py-4 px-6 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>CargoTrace &copy; 2026 • Platform Visibilitas & Pelacakan Pengiriman Global</span>
            <span>Mata Uang Tampilan: <strong class="text-[#0F172A] font-mono">{{ $activeCurrencyCode ?? 'USD' }}</strong> • Jangkauan 6 Benua</span>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>