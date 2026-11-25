<!DOCTYPE html>
<html lang="id" class="h-full bg-[#FAF6F0] text-[#423225]">
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
                        elegance: {
                            peach: '#FFDBBB',
                            'peach-light': '#FFF5EC',
                            'peach-subtle': '#FFFBF6',
                            sand: '#CCBEB1',
                            'sand-light': '#F4EFEA',
                            'sand-subtle': '#FAF7F4',
                            taupe: '#997E67',
                            'taupe-light': '#B59E8B',
                            'taupe-dark': '#7F654F',
                            espresso: '#664930',
                            'espresso-hover': '#523A25',
                            'espresso-dark': '#3D2A1A',
                        },
                        warm: {
                            50: '#FDFBF7',
                            100: '#FAF5EE',
                            200: '#F4ECE1',
                            300: '#EDE0D0',
                            400: '#CCBEB1',
                            500: '#997E67',
                            600: '#806550',
                            700: '#664930',
                            800: '#523A25',
                            900: '#3D2A1A',
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
            background-color: #FAF6F0;
            color: #423225;
        }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .leaflet-popup-content-wrapper {
            background-color: #FFFDFB !important;
            color: #423225 !important;
            border-radius: 0.75rem !important;
            border: 1px solid #EDE4DC !important;
            box-shadow: 0 10px 20px -3px rgba(102, 73, 48, 0.08) !important;
        }
        .leaflet-popup-tip {
            background-color: #FFFDFB !important;
        }
        .leaflet-container {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        /* Custom scrollbar in warm elegance tones */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #FAF5EE; }
        ::-webkit-scrollbar-thumb { background: #CCBEB1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #997E67; }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-[#FAF6F0] text-[#423225] antialiased flex flex-col md:flex-row">

    <!-- Sidebar Navigation (Neutral Elegance Style) -->
    <aside class="w-full md:w-64 bg-[#FDFBF7] border-r border-[#EDE4DC] shrink-0 flex flex-col justify-between h-auto md:min-h-screen">
        <div class="p-5">
            <!-- App Brand Header -->
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#664930] via-[#85654B] to-[#997E67] flex items-center justify-center text-[#FFDBBB] shadow-md shadow-[#664930]/15">
                    <i class="fa-solid fa-ship text-base"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-[#3D2A1A] leading-tight">CargoTrace</h1>
                    <p class="text-[11px] text-[#997E67] font-medium">Visibilitas Pengiriman</p>
                </div>
            </div>

            <!-- Navigation Links by Category -->
            <nav class="space-y-5">
                <!-- Group 1: OVERVIEW -->
                <div class="space-y-1">
                    <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-[#997E67] flex items-center gap-1.5 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#997E67]"></span>
                        <span>Ringkasan</span>
                    </p>
                    
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="group relative flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-[#FFDBBB]/45 to-[#F5ECE1]/40 text-[#423225] font-bold border border-[#E8D4C2] shadow-2xs' : 'text-[#6E5947] hover:bg-[#F6EFE8]/70 hover:text-[#3D2A1A]' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('dashboard') ? 'bg-[#664930] text-[#FFDBBB] shadow-xs' : 'bg-[#FFDBBB]/50 text-[#664930] border border-[#F0CCAC]/60 group-hover:bg-[#FFDBBB]' }}">
                            <i class="fa-solid fa-grip"></i>
                        </div>
                        <span>Dashboard</span>
                    </a>

                    <!-- Live Tracker -->
                    <a href="{{ route('public.track') }}" class="group relative flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('public.track*') ? 'bg-gradient-to-r from-[#FFDBBB]/45 to-[#F5ECE1]/40 text-[#423225] font-bold border border-[#E8D4C2] shadow-2xs' : 'text-[#6E5947] hover:bg-[#F6EFE8]/70 hover:text-[#3D2A1A]' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('public.track*') ? 'bg-[#997E67] text-white shadow-xs' : 'bg-[#F4E9DE] text-[#7D5A3C] border border-[#E2D0BF] group-hover:bg-[#EAD9C8]' }}">
                                <i class="fa-solid fa-earth-americas"></i>
                            </div>
                            <span>Pelacak Langsung</span>
                        </div>
                        <span class="relative flex h-2 w-2 mr-1">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FFDBBB] opacity-80"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#997E67]"></span>
                        </span>
                    </a>

                    @auth
                    <!-- Notifications -->
                    <a href="{{ route('notifications.index') }}" class="group relative flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('notifications.*') ? 'bg-gradient-to-r from-[#FFDBBB]/45 to-[#F5ECE1]/40 text-[#423225] font-bold border border-[#E8D4C2] shadow-2xs' : 'text-[#6E5947] hover:bg-[#F6EFE8]/70 hover:text-[#3D2A1A]' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('notifications.*') ? 'bg-[#8C4626] text-white shadow-xs' : 'bg-[#FCECE4] text-[#8C4626] border border-[#F5D4C3] group-hover:bg-[#F7DDD0]' }}">
                                <i class="fa-regular fa-bell"></i>
                            </div>
                            <span>Notifikasi</span>
                        </div>
                        @if(($unreadNotificationsCount ?? 0) > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-[#8C4626] text-white shadow-2xs">
                            {{ $unreadNotificationsCount }}
                        </span>
                        @endif
                    </a>
                    @endauth
                </div>

                <!-- Group 2: LOGISTICS & OPERATIONS -->
                <div class="space-y-1">
                    <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-[#997E67] flex items-center gap-1.5 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#CCBEB1]"></span>
                        <span>Logistik</span>
                    </p>
                    <a href="{{ route('shipments.index') }}" class="group relative flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('shipments.*') ? 'bg-gradient-to-r from-[#FFDBBB]/45 to-[#F5ECE1]/40 text-[#423225] font-bold border border-[#E8D4C2] shadow-2xs' : 'text-[#6E5947] hover:bg-[#F6EFE8]/70 hover:text-[#3D2A1A]' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('shipments.*') ? 'bg-[#664930] text-[#FFDBBB] shadow-xs' : 'bg-[#EFE8DF] text-[#63503F] border border-[#DDD0C2] group-hover:bg-[#E3D9CD]' }}">
                            <i class="fa-solid fa-box-archive"></i>
                        </div>
                        <span>{{ Auth::check() && Auth::user()->isCustomer() ? 'Pengiriman Saya' : 'Daftar Pengiriman' }}</span>
                    </a>
                </div>

                <!-- Group 3: MASTER DATA (Administrator only) -->
                @if(Auth::check() && Auth::user()->isAdmin())
                <div class="space-y-1">
                    <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-[#997E67] flex items-center gap-1.5 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#997E67]"></span>
                        <span>Data Master</span>
                    </p>
                    <a href="{{ route('users.index') }}" class="group relative flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-[#FFDBBB]/45 to-[#F5ECE1]/40 text-[#423225] font-bold border border-[#E8D4C2] shadow-2xs' : 'text-[#6E5947] hover:bg-[#F6EFE8]/70 hover:text-[#3D2A1A]' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('users.*') ? 'bg-[#7A5A41] text-white shadow-xs' : 'bg-[#F2EAE1] text-[#72543B] border border-[#DFD1C4] group-hover:bg-[#E6DACF]' }}">
                            <i class="fa-solid fa-users-gear"></i>
                        </div>
                        <span>Manajemen Pengguna</span>
                    </a>
                    <a href="{{ route('carriers.index') }}" class="group relative flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('carriers.*') ? 'bg-gradient-to-r from-[#FFDBBB]/45 to-[#F5ECE1]/40 text-[#423225] font-bold border border-[#E8D4C2] shadow-2xs' : 'text-[#6E5947] hover:bg-[#F6EFE8]/70 hover:text-[#3D2A1A]' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('carriers.*') ? 'bg-[#997E67] text-white shadow-xs' : 'bg-[#FCEAD8] text-[#8A562B] border border-[#F4D2B6] group-hover:bg-[#F7DFCA]' }}">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <span>Operator Pengangkutan</span>
                    </a>
                    <a href="{{ route('locations.index') }}" class="group relative flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('locations.*') ? 'bg-gradient-to-r from-[#FFDBBB]/45 to-[#F5ECE1]/40 text-[#423225] font-bold border border-[#E8D4C2] shadow-2xs' : 'text-[#6E5947] hover:bg-[#F6EFE8]/70 hover:text-[#3D2A1A]' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('locations.*') ? 'bg-[#664930] text-[#FFDBBB] shadow-xs' : 'bg-[#EAE4DC] text-[#594838] border border-[#D7CFC5] group-hover:bg-[#DDD5CA]' }}">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <span>Pelabuhan Dunia</span>
                    </a>
                </div>

                <!-- Group 4: CONFIGURATION & SYSTEM (Administrator only) -->
                <div class="space-y-1">
                    <p class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-[#997E67] flex items-center gap-1.5 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#FFDBBB]"></span>
                        <span>Sistem & Konfigurasi</span>
                    </p>
                    <a href="{{ route('currencies.index') }}" class="group relative flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('currencies.*') ? 'bg-gradient-to-r from-[#FFDBBB]/45 to-[#F5ECE1]/40 text-[#423225] font-bold border border-[#E8D4C2] shadow-2xs' : 'text-[#6E5947] hover:bg-[#F6EFE8]/70 hover:text-[#3D2A1A]' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('currencies.*') ? 'bg-[#664930] text-[#FFDBBB] shadow-xs' : 'bg-[#FFDBBB] text-[#664930] border border-[#F0CCAC] group-hover:bg-[#F8CEAA]' }}">
                                <i class="fa-solid fa-coins"></i>
                            </div>
                            <span>Kurs Mata Uang</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-[#FFDBBB]/40 text-[#664930] border border-[#E8D4C2]">
                            {{ $activeCurrencyCode ?? 'USD' }}
                        </span>
                    </a>
                    <a href="{{ route('audit-logs.index') }}" class="group relative flex items-center gap-3 px-2.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('audit-logs.*') ? 'bg-gradient-to-r from-[#FFDBBB]/45 to-[#F5ECE1]/40 text-[#423225] font-bold border border-[#E8D4C2] shadow-2xs' : 'text-[#6E5947] hover:bg-[#F6EFE8]/70 hover:text-[#3D2A1A]' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition-transform group-hover:scale-105 shrink-0 {{ request()->routeIs('audit-logs.*') ? 'bg-[#664930] text-white shadow-xs' : 'bg-[#E8E1D9] text-[#5C493B] border border-[#D5CABE] group-hover:bg-[#DDD4C9]' }}">
                            <i class="fa-solid fa-shield"></i>
                        </div>
                        <span>Log Audit</span>
                    </a>
                </div>
                @endif
            </nav>
        </div>

        <!-- User Profile Card at bottom of Sidebar -->
        <div class="p-4 border-t border-[#EDE4DC] bg-[#FAF5EE]/70">
            @auth
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#664930] via-[#85654B] to-[#997E67] text-[#FFDBBB] flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-[#3D2A1A] truncate leading-tight">{{ Auth::user()->name }}</p>
                        <span class="inline-block text-[9.5px] font-bold text-[#664930] bg-[#FFDBBB]/50 px-1.5 py-0.2 rounded border border-[#EED0B5] uppercase tracking-wide">
                            {{ Auth::user()->role === 'admin' ? 'Administrator' : (Auth::user()->role === 'operator' ? 'Operator Logistik' : 'Pelanggan') }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="Keluar" class="p-1.5 text-[#997E67] hover:text-[#8C4626] rounded-lg hover:bg-[#FBE9DC] transition">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
            @else
            <a href="{{ route('login') }}" class="w-full flex items-center justify-center py-2 px-3 text-xs font-semibold text-[#FFF8F2] bg-[#664930] hover:bg-[#523A25] rounded-xl transition shadow-sm">
                Masuk ke Konsol
            </a>
            @endauth
        </div>
    </aside>

    <!-- Main Content Area with Top Header Bar -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- Top Header Bar -->
        <header class="bg-[#FDFBF7]/90 backdrop-blur-md border-b border-[#EDE4DC] px-6 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-30">
            <!-- Breadcrumbs / Title Context -->
            <div class="flex items-center gap-2 text-xs text-[#997E67] font-medium">
                <i class="fa-regular fa-compass text-[#CCBEB1]"></i>
                <a href="{{ route('dashboard') }}" class="hover:text-[#664930] transition">Dashboard</a>
                <span class="text-[#CCBEB1]">&gt;</span>
                <span class="text-[#423225] font-semibold">@yield('title', 'Ringkasan')</span>
            </div>

            <!-- Header Right: Language, Global Currency Selector, Search bar, Notification Bell -->
            <div class="flex items-center gap-3">
                <!-- Global Language Switcher Dropdown (Neutral Elegance Style) -->
                <form action="{{ route('language.switch') }}" method="POST" class="inline-flex items-center">
                    @csrf
                    <div class="flex items-center bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl px-2.5 py-1.5 text-xs text-[#423225] hover:border-[#CCBEB1] transition shadow-2xs">
                        <i class="fa-solid fa-globe text-[#997E67] mr-1.5 text-xs"></i>
                        <select name="locale" onchange="this.form.submit()" class="bg-transparent text-xs font-semibold focus:outline-none cursor-pointer pr-1 text-[#423225]">
                            <option value="id" {{ ($currentLocale ?? 'id') === 'id' ? 'selected' : '' }}>🇮🇩 ID</option>
                            <option value="en" {{ ($currentLocale ?? 'id') === 'en' ? 'selected' : '' }}>🇬🇧 EN</option>
                        </select>
                    </div>
                </form>

                <!-- Global Currency Switcher Dropdown (Neutral Elegance Style) -->
                <form action="{{ route('currency.switch') }}" method="POST" class="inline-flex items-center">
                    @csrf
                    <div class="flex items-center bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl px-2.5 py-1.5 text-xs text-[#423225] hover:border-[#CCBEB1] transition shadow-2xs">
                        <i class="fa-solid fa-coins text-[#997E67] mr-1.5 text-xs"></i>
                        <select name="currency_code" onchange="this.form.submit()" class="bg-transparent text-xs font-semibold focus:outline-none cursor-pointer pr-1 text-[#423225]">
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
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 text-[#997E67]">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" placeholder="Cari pengiriman..."
                           class="w-48 pl-8 pr-8 py-1.5 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#423225] placeholder-[#B5A190] focus:outline-none focus:border-[#997E67] focus:bg-white transition shadow-2xs">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 text-[10px] font-mono text-[#B5A190]">⌘K</span>
                </form>

                <!-- Header Actions (Notification Bell) -->
                @auth
                <a href="{{ route('notifications.index') }}" class="relative p-2 text-[#7D6450] hover:text-[#3D2A1A] hover:bg-[#FAF5EE] rounded-xl border border-transparent hover:border-[#EDE4DC] transition" title="Notifikasi">
                    <i class="fa-regular fa-bell text-sm"></i>
                    @if(($unreadNotificationsCount ?? 0) > 0)
                    <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-[#8C4626] text-[10px] font-bold text-white shadow-xs">
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
            <div class="flex items-center gap-3 p-3.5 rounded-xl bg-[#F0F6F0] border border-[#CDE2CD] text-[#2F5831] text-xs shadow-2xs mb-4">
                <i class="fa-solid fa-circle-check text-[#3B663E] text-sm"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('info'))
            <div class="flex items-center gap-3 p-3.5 rounded-xl bg-[#FAF5EE] border border-[#EDE4DC] text-[#664930] text-xs shadow-2xs mb-4">
                <i class="fa-solid fa-circle-info text-[#997E67] text-sm"></i>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="p-3.5 rounded-xl bg-[#FDF2F0] border border-[#F5D2CC] text-[#8C3A30] text-xs shadow-2xs mb-4">
                <div class="flex items-center gap-2 font-bold mb-1 text-[#8C3A30]">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Harap periksa kesalahan di bawah ini:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[#963E32] ml-4">
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
        <footer class="mt-auto border-t border-[#EDE4DC] bg-[#FDFBF7] py-4 px-6 text-xs text-[#997E67] flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>CargoTrace &copy; 2026 • Platform Visibilitas & Pelacakan Pengiriman Global</span>
            <span>Mata Uang Tampilan: <strong class="text-[#664930] font-mono">{{ $activeCurrencyCode ?? 'USD' }}</strong> • Jangkauan 6 Benua</span>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>