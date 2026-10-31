<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CargoTrace') - Global Shipment Visibility & Tracking Platform</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        },
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .leaflet-popup-content-wrapper {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
            border-radius: 0.75rem !important;
            border: 1px solid #334155 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5) !important;
        }
        .leaflet-popup-tip {
            background-color: #0f172a !important;
        }
        .leaflet-container {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col bg-slate-950 text-slate-100 antialiased selection:bg-brand-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-slate-900/90 backdrop-blur-md border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Brand Logo -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-cyan-500 flex items-center justify-center shadow-lg shadow-brand-500/20 group-hover:scale-105 transition duration-200">
                            <i class="fa-solid fa-earth-americas text-white text-lg"></i>
                        </div>
                        <div>
                            <span class="text-xl font-bold tracking-tight text-white flex items-center gap-1.5">
                                Cargo<span class="text-brand-400">Trace</span>
                                <span class="px-1.5 py-0.5 text-[10px] uppercase font-bold tracking-wider rounded bg-brand-500/10 text-brand-400 border border-brand-500/20">Global</span>
                            </span>
                            <p class="text-[10px] text-slate-400 font-medium">Visibility & Tracking Platform</p>
                        </div>
                    </a>

                    <!-- Nav Links -->
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-brand-400 font-semibold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <i class="fa-solid fa-chart-pie mr-1.5 text-xs"></i> Dashboard
                        </a>
                        <a href="{{ route('shipments.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('shipments.*') ? 'bg-slate-800 text-brand-400 font-semibold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <i class="fa-solid fa-boxes-stacked mr-1.5 text-xs"></i> Shipments
                        </a>
                        <a href="{{ route('locations.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('locations.*') ? 'bg-slate-800 text-brand-400 font-semibold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <i class="fa-solid fa-globe mr-1.5 text-xs"></i> World Ports
                        </a>
                        <a href="{{ route('carriers.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('carriers.*') ? 'bg-slate-800 text-brand-400 font-semibold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <i class="fa-solid fa-ship mr-1.5 text-xs"></i> Carriers
                        </a>
                        <a href="{{ route('currencies.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('currencies.*') ? 'bg-slate-800 text-brand-400 font-semibold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <i class="fa-solid fa-coins mr-1.5 text-xs"></i> Currencies
                        </a>
                        @if(Auth::check() && Auth::user()->isAdmin())
                        <a href="{{ route('audit-logs.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('audit-logs.*') ? 'bg-slate-800 text-brand-400 font-semibold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <i class="fa-solid fa-shield-halved mr-1.5 text-xs"></i> Audit Logs
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Right: Currency Switcher, Notifications, User Menu -->
                <div class="flex items-center gap-3">
                    <!-- Global Currency Switcher Dropdown -->
                    <form action="{{ route('currency.switch') }}" method="POST" class="inline-flex items-center">
                        @csrf
                        <div class="relative inline-block text-left">
                            <label for="currencySelect" class="sr-only">Switch Currency</label>
                            <div class="flex items-center bg-slate-800 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 hover:border-slate-600 transition">
                                <i class="fa-solid fa-money-bill-transfer text-amber-400 mr-1.5"></i>
                                <select id="currencySelect" name="currency_code" onchange="this.form.submit()" class="bg-transparent text-xs font-semibold focus:outline-none cursor-pointer pr-1">
                                    @foreach($globalCurrencies ?? [] as $curr)
                                        <option value="{{ $curr->code }}" {{ ($activeCurrencyCode ?? 'USD') === $curr->code ? 'selected' : '' }} class="bg-slate-900 text-slate-100">
                                            {{ $curr->code }} ({{ $curr->symbol }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <!-- Public Track Link -->
                    <a href="{{ route('public.track') }}" target="_blank" title="Open Public Tracking Portal" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/60 transition">
                        <i class="fa-solid fa-magnifying-glass-location text-brand-400"></i> Track Code
                    </a>

                    @auth
                    <!-- Notifications Bell -->
                    <a href="{{ route('notifications.index') }}" class="relative p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition" title="Notifications">
                        <i class="fa-regular fa-bell text-base"></i>
                        @if(($unreadNotificationsCount ?? 0) > 0)
                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-slate-900">
                            {{ $unreadNotificationsCount }}
                        </span>
                        @endif
                    </a>

                    <!-- User Profile & Role Badge -->
                    <div class="flex items-center gap-3 pl-2 border-l border-slate-800">
                        <div class="text-right hidden lg:block">
                            <div class="text-xs font-semibold text-white leading-tight">{{ Auth::user()->name }}</div>
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold uppercase
                                {{ Auth::user()->role === 'admin' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : '' }}
                                {{ Auth::user()->role === 'operator' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : '' }}
                                {{ Auth::user()->role === 'customer' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : '' }}">
                                {{ Auth::user()->role }}
                            </span>
                        </div>

                        <!-- Logout Form -->
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Log out">
                                <i class="fa-solid fa-arrow-right-from-bracket text-base"></i>
                            </button>
                        </form>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-semibold rounded-lg bg-brand-600 hover:bg-brand-500 text-white transition">Sign In</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
        <div class="flex items-center gap-3 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm mb-4 animate-fade-in shadow-lg shadow-emerald-500/5">
            <i class="fa-solid fa-circle-check text-base"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('info'))
        <div class="flex items-center gap-3 p-3.5 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-400 text-sm mb-4">
            <i class="fa-solid fa-circle-info text-base"></i>
            <span class="font-medium">{{ session('info') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="p-3.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm mb-4">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <i class="fa-solid fa-triangle-exclamation text-base"></i>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-xs text-red-300 ml-4">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-800 bg-slate-900/60 py-6 text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-200">CargoTrace</span>
                <span>• Global Visibility & Tracking Platform • Web Programming 2</span>
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <span>Multi-Currency Engine: <strong class="text-brand-400">{{ $activeCurrencyCode ?? 'USD' }}</strong></span>
                <span>•</span>
                <span>Active Routing: <strong>Worldwide</strong></span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
