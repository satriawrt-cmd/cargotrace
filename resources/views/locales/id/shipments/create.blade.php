@extends('layouts.app')

@section('title', Auth::user()->isCustomer() ? 'Pesan Pengiriman Kargo Baru' : 'Tambah Pengiriman')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('shipments.index') }}" class="p-2 rounded-xl bg-white hover:bg-slate-50 text-slate-500 border border-slate-200 shadow-xs transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-[#0F172A]">
                {{ Auth::user()->isCustomer() ? 'Pesan Pengiriman Kargo Baru' : 'Tambah Pengiriman Global' }}
            </h1>
            <p class="text-xs text-slate-500">
                {{ Auth::user()->isCustomer() ? 'Pilih rute pelayaran internasional, tentukan barang kargo yang ingin Anda kirim, dan dapatkan nomor pelacakan otomatis.' : 'Daftarkan konsinyasi baru antar pelabuhan internasional dengan penagihan multi-mata uang.' }}
            </p>
        </div>
    </div>

    <form action="{{ route('shipments.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- 1. Stakeholders & Worldwide Route -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#1E3A8A]"></span>
                <span>Rute Pelayaran & Pihak Terkait</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if(Auth::user()->isCustomer())
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Pengirim / Pemilik Kargo</label>
                    <div class="px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-[#1E3A8A]"></i>
                            <span>{{ Auth::user()->name }} ({{ Auth::user()->company_name ?: Auth::user()->email }})</span>
                        </div>
                        <span class="text-[10px] bg-blue-100 text-[#1E3A8A] font-bold px-2 py-0.5 rounded">Akun Anda</span>
                    </div>
                    <input type="hidden" name="customer_id" value="{{ Auth::id() }}">
                </div>
                @else
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Pelanggan / Penerima (Consignee) *</label>
                    <select name="customer_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                        <option value="">Pilih Pelanggan</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} ({{ $cust->company_name ?? $cust->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Jalur Operator / Pelayaran *</label>
                    <select name="carrier_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                        <option value="">Pilih Operator</option>
                        @foreach($carriers as $car)
                            <option value="{{ $car->id }}" {{ old('carrier_id') == $car->id ? 'selected' : '' }}>
                                {{ $car->name }} ({{ $car->service_type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Pelabuhan Asal Pengiriman *</label>
                    <select name="origin_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                        <option value="">Pilih Lokasi Asal</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('origin_id') == $loc->id ? 'selected' : '' }}>
                                [{{ $loc->continent }}] {{ $loc->city }}, {{ $loc->country }} - {{ $loc->name }} ({{ $loc->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Pelabuhan Tujuan Pengiriman *</label>
                    <select name="destination_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                        <option value="">Pilih Lokasi Tujuan</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('destination_id') == $loc->id ? 'selected' : '' }}>
                                [{{ $loc->continent }}] {{ $loc->city }}, {{ $loc->country }} - {{ $loc->name }} ({{ $loc->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Cargo Details & Schedule -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#1E3A8A]"></span>
                <span>Rincian Barang & Kargo yang Dikirim</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Barang Apa yang Ingin Anda Kirim? (Deskripsi Kargo) *</label>
                    <input type="text" name="cargo_type" required value="{{ old('cargo_type') }}" placeholder="contoh: Komponen Elektronik, Tekstil Busana, Mesin Industri, dsb."
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Total Berat Kargo (KG) *</label>
                    <input type="number" step="0.01" name="weight_kg" required value="{{ old('weight_kg', 1000) }}"
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Volume Kargo (CBM) *</label>
                    <input type="number" step="0.01" name="volume_cbm" required value="{{ old('volume_cbm', 10) }}"
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Jadwal Keberangkatan *</label>
                    <input type="datetime-local" name="expected_departure" required value="{{ old('expected_departure', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>

                <div class="sm:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Jadwal Kedatangan Diharapkan *</label>
                        <input type="datetime-local" name="expected_arrival" required value="{{ old('expected_arrival', now()->addDays(14)->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Estimasi Tiba (ETA)</label>
                        <input type="datetime-local" name="estimated_arrival" value="{{ old('estimated_arrival', now()->addDays(14)->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Multi-Currency Financial Ledger -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#1E3A8A]"></span>
                <span>Biaya & Mata Uang Pengiriman</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Mata Uang Transaksi *</label>
                    <select name="currency_code" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] font-bold focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                        @foreach($currencies as $curr)
                            <option value="{{ $curr->code }}" {{ old('currency_code', 'USD') === $curr->code ? 'selected' : '' }}>
                                {{ $curr->code }} ({{ $curr->symbol }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Biaya Kirim (Freight) *</label>
                    <input type="number" step="0.01" name="shipping_cost" required value="{{ old('shipping_cost', 2500.00) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] font-mono focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nilai Kargo Dinyatakan</label>
                    <input type="number" step="0.01" name="declared_value" value="{{ old('declared_value', 25000.00) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] font-mono focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Premi Asuransi</label>
                    <input type="number" step="0.01" name="insurance_fee" value="{{ old('insurance_fee', 100.00) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] font-mono focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Instruksi Khusus / Catatan Penanganan</label>
                <textarea name="special_instructions" rows="2" placeholder="Catatan penanganan barang, instruksi kontainer dingin, fragile, dsb." class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">{{ old('special_instructions') }}</textarea>
            </div>
        </div>

        <!-- 4. Initial Item Manifest -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#1E3A8A]"></span>
                <span>Daftar Item / Manifest Barang</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-6 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[10px] text-slate-500 mb-1">Nama / Deskripsi Item</label>
                    <input type="text" name="items[0][item_description]" value="{{ old('items.0.item_description', 'Muatan Kargo Terstandarisasi') }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A]">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">Jumlah</label>
                    <input type="number" name="items[0][quantity]" value="{{ old('items.0.quantity', 20) }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A]">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">Kemasan</label>
                    <select name="items[0][package_type]" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A]">
                        <option value="Box">Kardus (Box)</option>
                        <option value="Pallet">Palet (Pallet)</option>
                        <option value="Container">Kontainer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">Berat (kg)</label>
                    <input type="number" name="items[0][weight_kg]" value="{{ old('items.0.weight_kg', 1000) }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A]">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">HS Code</label>
                    <input type="text" name="items[0][hs_code]" value="{{ old('items.0.hs_code', '8471.50.00') }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] font-mono">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('shipments.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-[#1E3A8A] hover:bg-[#1D4ED8] text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2 border border-sky-400/30">
                <i class="fa-solid fa-paper-plane text-xs"></i>
                <span>{{ Auth::user()->isCustomer() ? 'Kirim & Pesan Kargo Sekarang' : 'Simpan Pengiriman' }}</span>
            </button>
        </div>
    </form>
</div>
@endsection