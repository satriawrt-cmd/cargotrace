<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration - CargoTrace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
        <a href="{{ route('public.track') }}" class="inline-flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center shadow-xl shadow-blue-500/20">
                <i class="fa-solid fa-earth-americas text-white text-2xl"></i>
            </div>
            <div class="text-left">
                <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-1.5">
                    Cargo<span class="text-blue-400">Trace</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium">Customer Portal</p>
            </div>
        </a>
        <h2 class="mt-4 text-xl font-bold tracking-tight text-slate-100">Register new account</h2>
        <p class="mt-1 text-xs text-slate-400">Already registered? <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-medium underline">Sign in</a></p>
    </div>

    <div class="w-full sm:max-w-md">
        <div class="bg-slate-900 p-6 sm:p-8 rounded-2xl border border-slate-800 shadow-2xl">
            <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-medium text-slate-300 mb-1">Full Name</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}" placeholder="e.g. Captain James Cook"
                           class="w-full px-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
                    @error('name') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-medium text-slate-300 mb-1">Business Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}" placeholder="you@company.com"
                           class="w-full px-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
                    @error('email') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="company_name" class="block text-xs font-medium text-slate-300 mb-1">Company / Organization</label>
                        <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" placeholder="Global Logistics Inc"
                               class="w-full px-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label for="preferred_currency" class="block text-xs font-medium text-slate-300 mb-1">Billing Currency</label>
                        <select id="preferred_currency" name="preferred_currency" class="w-full px-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-blue-500">
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="IDR">IDR (Rp)</option>
                            <option value="SGD">SGD (S$)</option>
                            <option value="GBP">GBP (£)</option>
                            <option value="JPY">JPY (¥)</option>
                            <option value="AUD">AUD (A$)</option>
                            <option value="AED">AED (AED)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-xs font-medium text-slate-300 mb-1">Password</label>
                        <input id="password" name="password" type="password" required
                               class="w-full px-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-medium text-slate-300 mb-1">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="w-full px-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                @error('password') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror

                <button type="submit" class="w-full py-2.5 px-4 mt-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm shadow-lg shadow-blue-500/25 transition">
                    Create Account
                </button>
            </form>
        </div>
    </div>

</body>
</html>
