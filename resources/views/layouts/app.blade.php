<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CargoTrace') - Global Shipment Visibility & Tracking Platform</title>

    <!-- Google Fonts: Inter / Plus Jakarta Sans -->
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
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        },
                        surface: '#f8fafc',
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .leaflet-popup-content-wrapper {
            background-color: #ffffff !important;
            color: #1e293b !important;
            border-radius: 0.75rem !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08) !important;
        }
        .leaflet-popup-tip {
            background-color: #ffffff !important;
        }
        .leaflet-container {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased flex flex-col md:flex-row">

    <!-- Sidebar Navigation (Matching User Reference Image) -->
    <aside class="w-full md:w-64 bg-white border-r border-slate-200/80 shrink-0 flex flex-col justify-between h-auto md:min-h-screen">
        <div class="p-5">
            <!-- App Brand Header -->
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-sm shadow-blue-500/20">
                    <i class="fa-solid fa-ship text-lg"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900 leading-tight">CargoTrace</h1>
                    <p class="text-[11px] text-slate-400 font-medium">Visibilitas Pengiriman</p>
                </div>
            </div>

            <!-- Navigation Links by Category -->
            <nav class="space-y-6">
                <!-- Group 1: OVERVIEW -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Ringkasan</p>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-grip text-sm w-4"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('public.track') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('public.track*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-earth-americas text-sm w-4 text-blue-500"></i>
                        <span>Pelacak Langsung</span>
                    </a>
                    @auth
                    <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('notifications.*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-regular fa-bell text-sm w-4"></i>
                            <span>Notifikasi</span>
                        </div>
                        @if(($unreadNotificationsCount ?? 0) > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-600">
                            {{ $unreadNotificationsCount }}
                        </span>
                        @endif
                    </a>
                    @endauth
                </div>

                <!-- Group 2: LOGISTICS & OPERATIONS -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Logistik</p>
                    <a href="{{ route('shipments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('shipments.*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-box-archive text-sm w-4"></i>
                        <span>{{ Auth::check() && Auth::user()->isCustomer() ? 'Pengiriman Saya' : 'Daftar Pengiriman' }}</span>
                    </a>
                </div>

                <!-- Group 3: MASTER DATA (Administrator only) -->
                @if(Auth::check() && Auth::user()->isAdmin())
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Data Master</p>
                    <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-users-gear text-sm w-4"></i>
                        <span>Manajemen Pengguna</span>
                    </a>
                    <a href="{{ route('carriers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('carriers.*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-truck-fast text-sm w-4"></i>
                        <span>Operator Pengangkutan</span>
                    </a>
                    <a href="{{ route('locations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('locations.*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-location-dot text-sm w-4"></i>
                        <span>Pelabuhan Dunia</span>
                    </a>
                </div>

                <!-- Group 4: CONFIGURATION & SYSTEM (Administrator only) -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Sistem & Konfigurasi</p>
                    <a href="{{ route('currencies.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('currencies.*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-coins text-sm w-4 text-amber-500"></i>
                            <span>Kurs Mata Uang</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $activeCurrencyCode ?? 'USD' }}
                        </span>
                    </a>
                    <a href="{{ route('audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('audit-logs.*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-shield text-sm w-4"></i>
                        <span>Log Audit</span>
                    </a>
                </div>
                @endif
            </nav>
        </div>

        <!-- User Profile Card at bottom of Sidebar -->
        <div class="p-4 border-t border-slate-200/80 bg-slate-50/50">
            @auth
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-900 truncate leading-tight">{{ Auth::user()->name }}</p>
                        <span class="inline-block text-[10px] font-medium text-slate-400 uppercase tracking-wider">
                            {{ Auth::user()->role === 'admin' ? 'Administrator' : (Auth::user()->role === 'operator' ? 'Operator Logistik' : 'Pelanggan') }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="Keluar" class="p-1.5 text-slate-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
            @else
            <a href="{{ route('login') }}" class="w-full flex items-center justify-center py-2 px-3 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition shadow-sm">
                Masuk ke Konsol
            </a>
            @endauth
        </div>
    </aside>

    <!-- Main Content Area with Top Header Bar -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- Top Header Bar -->
        <header class="bg-white border-b border-slate-200/80 px-6 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-30">
            <!-- Breadcrumbs / Title Context -->
            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
                <i class="fa-regular fa-square text-slate-300"></i>
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700">Dashboard</a>
                <span>&gt;</span>
                <span class="text-slate-800 font-semibold">@yield('title', 'Ringkasan')</span>
            </div>

            <!-- Header Right: Language, Global Currency Selector, Search bar, Notification Bell -->
            <div class="flex items-center gap-3">
                <!-- Global Language Switcher Dropdown (Soft Style) -->
                <form action="{{ route('language.switch') }}" method="POST" class="inline-flex items-center">
                    @csrf
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-700 hover:border-slate-300 transition">
                        <i class="fa-solid fa-globe text-blue-500 mr-1.5 text-xs"></i>
                        <select name="locale" onchange="this.form.submit()" class="bg-transparent text-xs font-semibold focus:outline-none cursor-pointer pr-1 text-slate-800">
                            <option value="id" {{ ($currentLocale ?? 'id') === 'id' ? 'selected' : '' }}>🇮🇩 ID</option>
                            <option value="en" {{ ($currentLocale ?? 'id') === 'en' ? 'selected' : '' }}>🇬🇧 EN</option>
                        </select>
                    </div>
                </form>

                <!-- Global Currency Switcher Dropdown (Soft Style) -->
                <form action="{{ route('currency.switch') }}" method="POST" class="inline-flex items-center">
                    @csrf
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-700 hover:border-slate-300 transition">
                        <i class="fa-solid fa-coins text-amber-500 mr-1.5 text-xs"></i>
                        <select name="currency_code" onchange="this.form.submit()" class="bg-transparent text-xs font-semibold focus:outline-none cursor-pointer pr-1 text-slate-800">
                            @foreach($globalCurrencies ?? [] as $curr)
                                <option value="{{ $curr->code }}" {{ ($activeCurrencyCode ?? 'USD') === $curr->code ? 'selected' : '' }}>
                                    {{ $curr->code }} ({{ $curr->symbol }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <!-- Search Input Shortcut -->
                <form action="{{ route('shipments.index') }}" method="GET" class="relative hidden sm:block">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" placeholder="Cari pengiriman..."
                           class="w-48 pl-8 pr-8 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:bg-white transition">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 text-[10px] font-mono text-slate-400">⌘K</span>
                </form>

                <!-- Header Actions (Notification Bell) -->
                @auth
                <a href="{{ route('notifications.index') }}" class="relative p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition" title="Notifikasi">
                    <i class="fa-regular fa-bell text-sm"></i>
                    @if(($unreadNotificationsCount ?? 0) > 0)
                    <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
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
            <div class="flex items-center gap-3 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs shadow-sm mb-4">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('info'))
            <div class="flex items-center gap-3 p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs shadow-sm mb-4">
                <i class="fa-solid fa-circle-info text-blue-600 text-sm"></i>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs shadow-sm mb-4">
                <div class="flex items-center gap-2 font-bold mb-1 text-red-700">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Harap periksa kesalahan di bawah ini:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-red-600 ml-4">
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
        <footer class="mt-auto border-t border-slate-200/80 bg-white py-4 px-6 text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>CargoTrace &copy; 2026 • Platform Visibilitas & Pelacakan Pengiriman Global</span>
            <span>Mata Uang Tampilan: <strong class="text-slate-700 font-mono">{{ $activeCurrencyCode ?? 'USD' }}</strong> • Jangkauan 6 Benua</span>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
