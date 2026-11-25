<!DOCTYPE html>
<html lang="en" class="h-full bg-[#F8FAFC] text-[#0F172A]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration - CargoTrace</title>
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
                <p class="text-[11px] text-[#64748B] font-medium">Customer Portal</p>
            </div>
        </div>
        <h2 class="mt-4 text-lg font-bold text-[#0F172A]">Create your customer account</h2>
        <p class="mt-0.5 text-xs text-[#64748B]">Already registered? <a href="{{ route('login') }}" class="text-[#1E3A8A] hover:text-[#0F172A] font-semibold underline">Sign in</a></p>
    </div>

    <div class="w-full sm:max-w-md">
        <div class="bg-white/95 p-6 sm:p-8 rounded-3xl border border-[#E2E8F0] shadow-sm">
            <form action="{{ route('register.post') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-[#0F172A] mb-1">Full Name</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}" placeholder="John Doe"
                           class="w-full px-3.5 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#64748B]">
                    @error('name') <p class="text-[#DC2626] text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-[#0F172A] mb-1">Business / Company Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}" placeholder="john@company.com"
                           class="w-full px-3.5 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#64748B]">
                    @error('email') <p class="text-[#DC2626] text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="company_name" class="block text-xs font-semibold text-[#0F172A] mb-1">Company Name (Optional)</label>
                    <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" placeholder="Acme Logistics Corp"
                           class="w-full px-3.5 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#64748B]">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-[#0F172A] mb-1">Phone Number</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" placeholder="+1 555-0199"
                           class="w-full px-3.5 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#64748B]">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-[#0F172A] mb-1">Password</label>
                    <input id="password" name="password" type="password" required placeholder="Minimum 8 characters"
                           class="w-full px-3.5 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#64748B]">
                    @error('password') <p class="text-[#DC2626] text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-[#0F172A] mb-1">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Repeat password"
                           class="w-full px-3.5 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#64748B]">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#1E3A8A] hover:bg-[#1D4ED8] text-[#FFF8F2] font-semibold text-xs shadow-sm transition border border-[#1D4ED8]">
                        Register as Customer
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>