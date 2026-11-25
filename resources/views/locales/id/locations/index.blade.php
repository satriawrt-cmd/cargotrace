@extends('layouts.app')

@section('title', 'Pelabuhan Dunia')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Hub Logistik & Pelabuhan Dunia</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pelabuhan laut dan hub kargo di seluruh 6 benua dengan koordinat maritim terverifikasi.</p>
        </div>

        @if(Auth::user()->canManageShipments())
        <button type="button" onclick="document.getElementById('addLocationModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-xl shadow-sm transition">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Pelabuhan</span>
        </button>
        @endif
    </div>

    @php
        $groupedLocations = $locations->groupBy('continent');
    @endphp

    <div class="space-y-6">
        @foreach($groupedLocations as $continent => $locs)
        <div class="space-y-3">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wider pb-1 border-b border-slate-200">
                <i class="fa-solid fa-compass text-blue-600"></i>
                <span>{{ $continent }} ({{ $locs->count() }} Pelabuhan)</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($locs as $loc)
                <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs hover:border-slate-300 transition space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $loc->country }}</span>
                            <h3 class="text-sm font-bold text-slate-900">{{ $loc->city }}</h3>
                            <p class="text-xs text-slate-500">{{ $loc->name }}</p>
                        </div>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $loc->code }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-1 text-[11px] text-slate-400 pt-2 border-t border-slate-100 font-mono">
                        <div>Lat: <span class="text-slate-700">{{ sprintf('%.4f', $loc->latitude) }}</span></div>
                        <div>Lng: <span class="text-slate-700">{{ sprintf('%.4f', $loc->longitude) }}</span></div>
                        <div>Tipe: <span class="text-slate-700">{{ $loc->type }}</span></div>
                        <div>Zona: <span class="text-slate-700 truncate">{{ $loc->timezone }}</span></div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Pelayaran:</span>
                        <span class="font-bold text-slate-700 font-mono">
                            {{ $loc->origin_shipments_count ?? 0 }} brkt / {{ $loc->destination_shipments_count ?? 0 }} tba
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Add Location Modal -->
    <div id="addLocationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 max-w-md w-full shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Tambah Pelabuhan Dunia</h3>
                <button type="button" onclick="document.getElementById('addLocationModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('locations.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Pelabuhan / Terminal *</label>
                    <input type="text" name="name" required placeholder="contoh: Port of Yokohama" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">UN/LOCODE *</label>
                        <input type="text" name="code" required placeholder="contoh: JPYOK" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Tipe Fasilitas *</label>
                        <select name="type" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                            <option value="SEAPORT">Pelabuhan Laut (Seaport)</option>
                            <option value="AIRPORT">Bandara Kargo (Airport)</option>
                            <option value="LOGISTICS_HUB">Hub Logistik Darat</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kota *</label>
                        <input type="text" name="city" required placeholder="Yokohama" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Negara *</label>
                        <input type="text" name="country" required placeholder="Japan" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Benua *</label>
                    <select name="continent" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                        <option value="Asia">Asia</option>
                        <option value="Europe">Eropa (Europe)</option>
                        <option value="North America">Amerika Utara (North America)</option>
                        <option value="South America">Amerika Selatan (South America)</option>
                        <option value="Africa">Afrika</option>
                        <option value="Oceania">Oseania (Oceania)</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Latitude (Garis Lintang) *</label>
                        <input type="number" step="any" name="latitude" required placeholder="35.4437" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Longitude (Garis Bujur) *</label>
                        <input type="number" step="any" name="longitude" required placeholder="139.6380" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addLocationModal').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-500">Batal</button>
                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-blue-600 text-white font-semibold text-xs">Simpan Pelabuhan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
