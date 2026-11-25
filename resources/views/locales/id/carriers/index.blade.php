@extends('layouts.app')

@section('title', 'Operator Pengangkutan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Operator & Perusahaan Pelayaran</h1>
            <p class="text-xs text-slate-500 mt-0.5">Jalur pelayaran peti kemas laut internasional dan mitra kargo udara multimodal.</p>
        </div>

        @if(Auth::user()->canManageShipments())
        <button type="button" onclick="document.getElementById('addCarrierModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-xl shadow-sm transition">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Operator</span>
        </button>
        @endif
    </div>

    <!-- Carriers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($carriers as $carrier)
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div class="space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-sm">
                        @if($carrier->service_type === 'AIR_FREIGHT')
                            <i class="fa-solid fa-plane"></i>
                        @else
                            <i class="fa-solid fa-ship"></i>
                        @endif
                    </div>
                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                        {{ $carrier->code }}
                    </span>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-slate-900">{{ $carrier->name }}</h3>
                    <span class="text-[10px] uppercase font-bold text-slate-400">
                        {{ $carrier->service_type === 'SEA_FREIGHT' ? 'Kargo Laut' : ($carrier->service_type === 'AIR_FREIGHT' ? 'Kargo Udara' : 'Multimodal') }}
                    </span>
                </div>

                <div class="space-y-1 text-xs text-slate-500 pt-2 border-t border-slate-100">
                    <p><i class="fa-regular fa-envelope text-[11px] text-slate-400 mr-1.5"></i> {{ $carrier->contact_email ?: '-' }}</p>
                    <p><i class="fa-solid fa-phone text-[11px] text-slate-400 mr-1.5"></i> {{ $carrier->contact_phone ?: '-' }}</p>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Pengiriman Aktif:</span>
                <span class="font-bold text-slate-800 font-mono">{{ $carrier->shipments_count ?? 0 }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Add Carrier Modal -->
    <div id="addCarrierModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 max-w-md w-full shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Tambah Operator Pengangkutan</h3>
                <button type="button" onclick="document.getElementById('addCarrierModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('carriers.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Operator *</label>
                    <input type="text" name="name" required placeholder="contoh: Evergreen Marine" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kode SCAC *</label>
                    <input type="text" name="code" required placeholder="contoh: EGLV" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Jenis Layanan *</label>
                    <select name="service_type" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                        <option value="SEA_FREIGHT">Kargo Laut (Sea Freight)</option>
                        <option value="AIR_FREIGHT">Kargo Udara (Air Freight)</option>
                        <option value="MULTIMODAL">Multimodal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Email Kontak</label>
                    <input type="email" name="contact_email" placeholder="operations@carrier.com" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nomor Telepon</label>
                    <input type="text" name="contact_phone" placeholder="+62 21 555 0199" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addCarrierModal').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-500">Batal</button>
                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-blue-600 text-white font-semibold text-xs">Simpan Operator</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
