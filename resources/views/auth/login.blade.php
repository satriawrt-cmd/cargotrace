<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - CargoTrace</title>
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
                <p class="text-[11px] text-slate-400 font-medium">Platform Visibilitas Pengiriman</p>
            </div>
        </div>
        <h2 class="mt-4 text-lg font-bold text-slate-900">Masuk ke akun Anda</h2>
        <p class="mt-0.5 text-xs text-slate-500">Atau <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium underline">daftar akun pelanggan baru</a></p>
    </div>

    <div class="w-full sm:max-w-md space-y-4">
        @if(session('info'))
        <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-blue-700 text-xs">
            {{ session('info') }}
        </div>
        @endif

        @if($errors->any())
        <div class="p-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-600 text-xs">
            {{ $errors->first() }}
        </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/90 shadow-sm">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                    <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" placeholder="nama@perusahaan.com"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center text-slate-500 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded text-blue-600 border-slate-300">
                        <span class="ml-2 text-xs">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-sm transition">
                    Masuk
                </button>
            </form>
        </div>

        <div class="text-center">
            <a href="{{ route('public.track') }}" class="text-xs text-slate-500 hover:text-slate-800 transition">
                &larr; Portal Pelacakan Publik
            </a>
        </div>
    </div>

</body>
</html>
