<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
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
<body class="h-full flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
        <div class="inline-flex items-center gap-3 mb-2">
            <div class="w-11 h-11 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-sm shadow-blue-500/20">
                <i class="fa-solid fa-ship text-lg"></i>
            </div>
            <div class="text-left">
                <h1 class="text-xl font-bold tracking-tight text-slate-900">CargoTrace</h1>
                <p class="text-[11px] text-slate-400 font-medium">Portal Pelanggan</p>
            </div>
        </div>
        <h2 class="mt-4 text-lg font-bold text-slate-900">Buat akun pelanggan Anda</h2>
        <p class="mt-0.5 text-xs text-slate-500">Sudah terdaftar? <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium underline">Masuk</a></p>
    </div>

    <div class="w-full sm:max-w-md">
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/90 shadow-sm">
            <form action="{{ route('register.post') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}" placeholder="Budi Santoso"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                    @error('name') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email Perusahaan / Bisnis</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}" placeholder="budi@perusahaan.com"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                    @error('email') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="company_name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan</label>
                        <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" placeholder="PT Nusantara Logistik"
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                    </div>
                    <div>
                        <label for="preferred_currency" class="block text-xs font-semibold text-slate-700 mb-1">Mata Uang Pilihan</label>
                        <select id="preferred_currency" name="preferred_currency" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                            <option value="IDR">IDR (Rp)</option>
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="SGD">SGD (S$)</option>
                            <option value="GBP">GBP (£)</option>
                            <option value="JPY">JPY (¥)</option>
                            <option value="AUD">AUD (A$)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                        <input id="password" name="password" type="password" required
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                    </div>
                </div>
                @error('password') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror

                <button type="submit" class="w-full py-2.5 px-4 mt-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-sm transition">
                    Daftar Akun
                </button>
            </form>
        </div>
    </div>

</body>
</html>
