<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - CargoTrace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
        <div class="inline-flex items-center gap-3 mb-2">
            <div class="w-11 h-11 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-sm shadow-blue-500/20">
                <i class="fa-solid fa-ship text-lg"></i>
            </div>
            <div class="text-left">
                <h1 class="text-xl font-bold tracking-tight text-slate-900">CargoTrace</h1>
                <p class="text-[11px] text-slate-400 font-medium">Shipment Visibility Platform</p>
            </div>
        </div>
        <h2 class="mt-4 text-lg font-bold text-slate-900">Sign in to your account</h2>
        <p class="mt-0.5 text-xs text-slate-500">Or <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium underline">register a customer account</a></p>
    </div>

    <div class="w-full sm:max-w-md space-y-4">
        <!-- 1-Click Quick Demo Login Pills (Soft Minimal Card) -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <i class="fa-solid fa-bolt text-amber-500 mr-1"></i> Quick Demo Access
                </span>
                <span class="text-[10px] text-slate-400 font-mono">1-Click</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ route('quick-login', 'admin') }}" class="flex flex-col items-center justify-center p-2 rounded-xl bg-purple-50 hover:bg-purple-100 border border-purple-100 text-purple-700 text-xs font-semibold transition">
                    <span>Admin</span>
                    <span class="text-[9px] text-purple-500 font-normal">Full Control</span>
                </a>
                <a href="{{ route('quick-login', 'operator') }}" class="flex flex-col items-center justify-center p-2 rounded-xl bg-blue-50 hover:bg-blue-100 border border-blue-100 text-blue-700 text-xs font-semibold transition">
                    <span>Operator</span>
                    <span class="text-[9px] text-blue-500 font-normal">Shipments</span>
                </a>
                <a href="{{ route('quick-login', 'customer') }}" class="flex flex-col items-center justify-center p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 text-emerald-700 text-xs font-semibold transition">
                    <span>Customer</span>
                    <span class="text-[9px] text-emerald-500 font-normal">Track Own</span>
                </a>
            </div>
        </div>

        @if(session('info'))
        <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-xs">
            {{ session('info') }}
        </div>
        @endif

        @if($errors->any())
        <div class="p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs">
            {{ $errors->first() }}
        </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/90 shadow-sm">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email address</label>
                    <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email', 'admin@cargotrace.test') }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 transition">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" value="password"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 transition">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center text-slate-500 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded text-blue-600 border-slate-300">
                        <span class="ml-1.5 text-xs">Remember me</span>
                    </label>
                    <span class="text-slate-400 font-mono text-[11px]">Pass: password</span>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-sm transition">
                    Sign in
                </button>
            </form>
        </div>

        <div class="text-center">
            <a href="{{ route('public.track') }}" class="text-xs text-slate-500 hover:text-slate-800 transition">
                &larr; Public Tracking Portal
            </a>
        </div>
    </div>

</body>
</html>
