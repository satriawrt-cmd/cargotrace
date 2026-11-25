<!DOCTYPE html>
<html lang="id" class="h-full bg-[#FAF6F0] text-[#423225]">
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
<body class="h-full flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-[#FAF6F0] relative">

    <!-- Top Right Language Switcher -->
    <div class="absolute top-4 right-4 sm:top-6 sm:right-6">
        <form action="{{ route('language.switch') }}" method="POST" class="inline-flex items-center">
            @csrf
            <div class="flex items-center bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl px-2.5 py-1.5 text-xs text-[#423225] shadow-2xs hover:border-[#CCBEB1] transition">
                <i class="fa-solid fa-globe text-[#997E67] mr-1.5 text-xs"></i>
                <select name="locale" onchange="this.form.submit()" class="bg-transparent text-xs font-semibold focus:outline-none cursor-pointer pr-1 text-[#423225]">
                    <option value="id" {{ ($currentLocale ?? 'id') === 'id' ? 'selected' : '' }}>🇮🇩 ID</option>
                    <option value="en" {{ ($currentLocale ?? 'id') === 'en' ? 'selected' : '' }}>🇬🇧 EN</option>
                </select>
            </div>
        </form>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
        <div class="inline-flex items-center gap-3 mb-2">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#664930] via-[#85654B] to-[#997E67] flex items-center justify-center text-[#FFDBBB] shadow-md shadow-[#664930]/15">
                <i class="fa-solid fa-ship text-lg"></i>
            </div>
            <div class="text-left">
                <h1 class="text-xl font-bold tracking-tight text-[#3D2A1A]">CargoTrace</h1>
                <p class="text-[11px] text-[#997E67] font-medium">Platform Visibilitas Pengiriman</p>
            </div>
        </div>
        <h2 class="mt-4 text-lg font-bold text-[#3D2A1A]">Masuk ke akun Anda</h2>
        <p class="mt-0.5 text-xs text-[#7A6350]">Atau <a href="{{ route('register') }}" class="text-[#664930] hover:text-[#3D2A1A] font-semibold underline">daftar akun pelanggan baru</a></p>
    </div>

    <div class="w-full sm:max-w-md space-y-4">
        @if(session('info'))
        <div class="p-3.5 rounded-2xl bg-[#FAF5EE] border border-[#EDE4DC] text-[#664930] text-xs">
            {{ session('info') }}
        </div>
        @endif

        @if($errors->any())
        <div class="p-3.5 rounded-2xl bg-[#FDF2F0] border border-[#F5D2CC] text-[#8C3A30] text-xs">
            {{ $errors->first() }}
        </div>
        @endif

        <!-- Form Card (Neutral Elegance Style) -->
        <div class="bg-white/95 p-6 sm:p-8 rounded-3xl border border-[#EDE4DC] shadow-sm">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-[#443224] mb-1">Alamat Email</label>
                    <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" placeholder="nama@perusahaan.com"
                           class="w-full px-3.5 py-2.5 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#997E67] transition">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-[#443224] mb-1">Kata Sandi</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••"
                           class="w-full px-3.5 py-2.5 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#997E67] transition">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center text-[#7A6350] cursor-pointer">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded text-[#664930] border-[#CCBEB1]">
                        <span class="ml-2 text-xs">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#664930] hover:bg-[#523A25] text-[#FFF8F2] font-semibold text-xs shadow-sm transition border border-[#523A25]">
                    Masuk
                </button>
            </form>
        </div>

        <div class="text-center">
            <a href="{{ route('public.track') }}" class="text-xs text-[#997E67] hover:text-[#3D2A1A] transition">
                &larr; Portal Pelacakan Publik
            </a>
        </div>
    </div>

</body>
</html>