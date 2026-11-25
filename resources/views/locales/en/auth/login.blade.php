<!DOCTYPE html>
<html lang="en" class="h-full bg-[#F8FAFC] text-[#0F172A]">
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
<body class="h-full flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-[#F8FAFC] relative">

    <!-- Top Right Language Switcher -->
    <div class="absolute top-4 right-4 sm:top-6 sm:right-6">
        <form action="{{ route('language.switch') }}" method="POST" class="inline-flex items-center">
            @csrf
            <div class="flex items-center bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-2.5 py-1.5 text-xs text-[#0F172A] shadow-2xs hover:border-[#94A3B8] transition">
                <i class="fa-solid fa-globe text-[#64748B] mr-1.5 text-xs"></i>
                <select name="locale" onchange="this.form.submit()" class="bg-transparent text-xs font-semibold focus:outline-none cursor-pointer pr-1 text-[#0F172A]">
                    <option value="id" {{ ($currentLocale ?? 'id') === 'id' ? 'selected' : '' }}>🇮🇩 ID</option>
                    <option value="en" {{ ($currentLocale ?? 'id') === 'en' ? 'selected' : '' }}>🇬🇧 EN</option>
                </select>
            </div>
        </form>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
        <div class="inline-flex items-center gap-3 mb-2">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#1E3A8A] via-[#1E3A8A] to-[#64748B] flex items-center justify-center text-[#38BDF8] shadow-md shadow-[#1E3A8A]/15">
                <i class="fa-solid fa-ship text-lg"></i>
            </div>
            <div class="text-left">
                <h1 class="text-xl font-bold tracking-tight text-[#0F172A]">CargoTrace</h1>
                <p class="text-[11px] text-[#64748B] font-medium">Shipment Visibility Platform</p>
            </div>
        </div>
        <h2 class="mt-4 text-lg font-bold text-[#0F172A]">Sign in to your account</h2>
        <p class="mt-0.5 text-xs text-[#64748B]">Or <a href="{{ route('register') }}" class="text-[#1E3A8A] hover:text-[#0F172A] font-semibold underline">register a new customer account</a></p>
    </div>

    <div class="w-full sm:max-w-md space-y-4">
        @if(session('info'))
        <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] text-[#1E3A8A] text-xs">
            {{ session('info') }}
        </div>
        @endif

        @if($errors->any())
        <div class="p-3.5 rounded-2xl bg-[#FEF2F2] border border-[#FECACA] text-[#DC2626] text-xs">
            {{ $errors->first() }}
        </div>
        @endif

        <!-- Form Card (Ocean Maritime Style) -->
        <div class="bg-white/95 p-6 sm:p-8 rounded-3xl border border-[#E2E8F0] shadow-sm">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-[#0F172A] mb-1">Email address</label>
                    <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" placeholder="name@company.com"
                           class="w-full px-3.5 py-2.5 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#64748B] transition">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-[#0F172A] mb-1">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••"
                           class="w-full px-3.5 py-2.5 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#64748B] transition">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center text-[#64748B] cursor-pointer">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded text-[#1E3A8A] border-[#94A3B8]">
                        <span class="ml-2 text-xs">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#1E3A8A] hover:bg-[#1D4ED8] text-[#FFF8F2] font-semibold text-xs shadow-sm transition border border-[#1D4ED8]">
                    Sign in
                </button>
            </form>
        </div>

        <div class="text-center">
            <a href="{{ route('public.track') }}" class="text-xs text-[#64748B] hover:text-[#0F172A] transition">
                &larr; Global Public Tracking Portal
            </a>
        </div>
    </div>

</body>
</html>