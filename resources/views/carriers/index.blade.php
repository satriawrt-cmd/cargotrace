@extends('layouts.app')

@section('title', 'Global Carriers')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-3">
                <i class="fa-solid fa-ship text-brand-400"></i>
                <span>Global Carriers & Lines</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Ocean freight shipping lines, multimodal freight forwarders, and air cargo carriers.</p>
        </div>

        @if(Auth::user()->canManageShipments())
        <button type="button" onclick="document.getElementById('addCarrierModal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs shadow-lg shadow-brand-500/20 transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Add Carrier
        </button>
        @endif
    </div>

    <!-- Carriers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($carriers as $carrier)
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl flex flex-col justify-between hover:border-slate-700 transition">
            <div class="space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="w-10 h-10 rounded-2xl bg-brand-500/10 border border-brand-500/20 text-brand-400 flex items-center justify-center text-lg">
                        @if($carrier->service_type === 'AIR_FREIGHT')
                            <i class="fa-solid fa-plane"></i>
                        @else
                            <i class="fa-solid fa-anchor"></i>
                        @endif
                    </div>
                    <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-slate-950 text-slate-300 border border-slate-800">
                        {{ $carrier->code }}
                    </span>
                </div>

                <div>
                    <h3 class="text-base font-bold text-white">{{ $carrier->name }}</h3>
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ str_replace('_', ' ', $carrier->service_type) }}</span>
                </div>

                <div class="space-y-1 text-xs text-slate-400 pt-2 border-t border-slate-800">
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-envelope text-slate-500 text-[11px]"></i>
                        <span>{{ $carrier->contact_email ?: 'N/A' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-phone text-slate-500 text-[11px]"></i>
                        <span>{{ $carrier->contact_phone ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-500">Active Consignments:</span>
                <span class="font-bold text-white font-mono text-sm">{{ $carrier->shipments_count ?? 0 }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Add Carrier Modal -->
    <div id="addCarrierModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white">Add International Carrier</h3>
                <button type="button" onclick="document.getElementById('addCarrierModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('carriers.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Carrier Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Evergreen Marine" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Carrier Code (SCAC / IATA) *</label>
                    <input type="text" name="code" required placeholder="e.g. EGLV" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Service Type *</label>
                    <select name="service_type" required class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                        <option value="SEA_FREIGHT">SEA FREIGHT</option>
                        <option value="AIR_FREIGHT">AIR FREIGHT</option>
                        <option value="MULTIMODAL">MULTIMODAL</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" placeholder="dispatch@carrier.com" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" placeholder="+1-800-555-0199" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addCarrierModal').classList.add('hidden')" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs">Save Carrier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
