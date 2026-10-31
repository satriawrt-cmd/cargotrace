<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - CargoTrace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-8">
        <a href="{{ route('public.track') }}" class="inline-flex items-center gap-3 group">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center shadow-xl shadow-blue-500/20 group-hover:scale-105 transition">
                <i class="fa-solid fa-earth-americas text-white text-2xl"></i>
            </div>
            <div class="text-left">
                <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-1.5">
                    Cargo<span class="text-blue-400">Trace</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium">Global Shipment Visibility Platform</p>
            </div>
        </a>
        <h2 class="mt-6 text-xl font-bold tracking-tight text-slate-100">Sign in to your account</h2>
        <p class="mt-1 text-xs text-slate-400">Or <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 font-medium underline">create a customer account</a></p>
    </div>

    <div class="w-full sm:max-w-md">
        <!-- Quick Demo Login Switcher (Evaluator & Recruiter Friendly) -->
        <div class="mb-6 p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-bolt text-amber-400"></i> Quick Demo Access
                </span>
                <span class="text-[10px] text-slate-400 font-mono">1-Click Login</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ route('quick-login', 'admin') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 text-purple-300 text-xs font-semibold transition group">
                    <i class="fa-solid fa-user-shield mb-1 text-sm group-hover:scale-110 transition"></i>
                    <span>Admin</span>
                    <span class="text-[9px] text-purple-400/80 font-normal">Full Control</span>
                </a>
                <a href="{{ route('quick-login', 'operator') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/30 text-blue-300 text-xs font-semibold transition group">
                    <i class="fa-solid fa-headset mb-1 text-sm group-hover:scale-110 transition"></i>
                    <span>Operator</span>
                    <span class="text-[9px] text-blue-400/80 font-normal">Shipments & ETA</span>
                </a>
                <a href="{{ route('quick-login', 'customer') }}" class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-semibold transition group">
                    <i class="fa-solid fa-building mb-1 text-sm group-hover:scale-110 transition"></i>
                    <span>Customer</span>
                    <span class="text-[9px] text-emerald-400/80 font-normal">Track Own</span>
                </a>
            </div>
        </div>

        @if(session('info'))
        <div class="p-3 mb-4 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs">
            {{ session('info') }}
        </div>
        @endif

        @if($errors->any())
        <div class="p-3 mb-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs">
            {{ $errors->first() }}
        </div>
        @endif

        <div class="bg-slate-900 p-6 sm:p-8 rounded-2xl border border-slate-800 shadow-2xl">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-300 mb-1">Email address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <i class="fa-regular fa-envelope text-xs"></i>
                        </span>
                        <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email', 'admin@cargotrace.test') }}"
                               class="w-full pl-9 pr-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-slate-300 mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                        <input id="password" name="password" type="password" required autocomplete="current-password" value="password"
                               class="w-full pl-9 pr-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-900">
                        <span class="ml-2">Remember me</span>
                    </label>
                    <span class="text-slate-500 font-mono text-[11px]">Default: password</span>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm shadow-lg shadow-blue-500/25 transition duration-150">
                    Sign in to Console
                </button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('public.track') }}" class="text-xs text-slate-400 hover:text-slate-200 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Public Tracking Search
            </a>
        </div>
    </div>

</body>
</html>
