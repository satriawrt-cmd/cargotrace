<!DOCTYPE html>
<html lang="id" class="h-full bg-[#FAF6F0] text-[#423225]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pelanggan - CargoTrace</title>
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
                <p class="text-[11px] text-[#997E67] font-medium">Portal Pelanggan</p>
            </div>
        </div>
        <h2 class="mt-4 text-lg font-bold text-[#3D2A1A]">Buat akun pelanggan Anda</h2>
        <p class="mt-0.5 text-xs text-[#7A6350]">Sudah terdaftar? <a href="{{ route('login') }}" class="text-[#664930] hover:text-[#3D2A1A] font-semibold underline">Masuk</a></p>
    </div>

    <div class="w-full sm:max-w-md">
        <div class="bg-white/95 p-6 sm:p-8 rounded-3xl border border-[#EDE4DC] shadow-sm">
            <form action="{{ route('register.post') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-[#443224] mb-1">Nama Lengkap</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}" placeholder="Budi Santoso"
                           class="w-full px-3.5 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#997E67]">
                    @error('name') <p class="text-[#8C3A30] text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-[#443224] mb-1">Email Perusahaan / Bisnis</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}" placeholder="budi@perusahaan.com"
                           class="w-full px-3.5 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#997E67]">
                    @error('email') <p class="text-[#8C3A30] text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="company_name" class="block text-xs font-semibold text-[#443224] mb-1">Nama Perusahaan (Opsional)</label>
                    <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" placeholder="PT Nusantara Logistik"
                           class="w-full px-3.5 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#997E67]">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-[#443224] mb-1">Nomor Telepon</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" placeholder="+62 812-3456-7890"
                           class="w-full px-3.5 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#997E67]">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-[#443224] mb-1">Kata Sandi</label>
                    <input id="password" name="password" type="password" required placeholder="Minimal 8 karakter"
                           class="w-full px-3.5 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#997E67]">
                    @error('password') <p class="text-[#8C3A30] text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-[#443224] mb-1">Konfirmasi Kata Sandi</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Ulangi kata sandi"
                           class="w-full px-3.5 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#997E67]">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#664930] hover:bg-[#523A25] text-[#FFF8F2] font-semibold text-xs shadow-sm transition border border-[#523A25]">
                        Daftar Sebagai Pelanggan
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>