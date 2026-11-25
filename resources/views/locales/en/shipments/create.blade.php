@extends('layouts.app')

@section('title', Auth::user()->isCustomer() ? 'Book New Cargo Shipment' : 'New Shipment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('shipments.index') }}" class="p-2 rounded-xl bg-white hover:bg-slate-50 text-slate-500 border border-slate-200 shadow-xs transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-[#0F172A]">
                {{ Auth::user()->isCustomer() ? 'Book New Cargo Shipment' : 'Register New Global Shipment' }}
            </h1>
            <p class="text-xs text-slate-500">
                {{ Auth::user()->isCustomer() ? 'Select international shipping route, specify the cargo and goods you want to ship, and get an automated tracking number.' : 'Register new international consignment with multi-currency financial ledger.' }}
            </p>
        </div>
    </div>

    <form action="{{ route('shipments.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- 1. Stakeholders & Worldwide Route -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#1E3A8A]"></span>
                <span>Shipping Corridor & Stakeholders</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if(Auth::user()->isCustomer())
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Consignor / Cargo Owner</label>
                    <div class="px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-[#1E3A8A]"></i>
                            <span>{{ Auth::user()->name }} ({{ Auth::user()->company_name ?: Auth::user()->email }})</span>
                        </div>
                        <span class="text-[10px] bg-blue-100 text-[#1E3A8A] font-bold px-2 py-0.5 rounded">Your Account</span>
                    </div>
                    <input type="hidden" name="customer_id" value="{{ Auth::id() }}">
                </div>
                @else
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Customer / Consignee *</label>
                    <select name="customer_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                        <option value="">Select Customer</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} ({{ $cust->company_name ?? $cust->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Freight Carrier & Shipping Line *</label>
                    <select name="carrier_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                        <option value="">Select Carrier</option>
                        @foreach($carriers as $car)
                            <option value="{{ $car->id }}" {{ old('carrier_id') == $car->id ? 'selected' : '' }}>
                                {{ $car->name }} ({{ $car->service_type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Port of Origin *</label>
                    <select name="origin_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                        <option value="">Select Origin Location</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('origin_id') == $loc->id ? 'selected' : '' }}>
                                [{{ $loc->continent }}] {{ $loc->city }}, {{ $loc->country }} - {{ $loc->name }} ({{ $loc->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Destination Port *</label>
                    <select name="destination_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                        <option value="">Select Destination Location</option>
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
                <span>Cargo Specifications & Scheduled Dates</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">What Goods Are You Shipping? (Cargo Description) *</label>
                    <input type="text" name="cargo_type" required value="{{ old('cargo_type') }}" placeholder="e.g. Industrial Automation Equipment, Solar Panels, Textiles"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Total Weight (KG) *</label>
                    <input type="number" step="0.01" name="weight_kg" required value="{{ old('weight_kg', 1000) }}"
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Cargo Volume (CBM) *</label>
                    <input type="number" step="0.01" name="volume_cbm" required value="{{ old('volume_cbm', 10) }}"
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Expected Departure Date *</label>
                    <input type="datetime-local" name="expected_departure" required value="{{ old('expected_departure', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>

                <div class="sm:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Expected Arrival Date *</label>
                        <input type="datetime-local" name="expected_arrival" required value="{{ old('expected_arrival', now()->addDays(14)->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Estimated Arrival (ETA)</label>
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
                <span>Billing & Currency</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Billing Currency *</label>
                    <select name="currency_code" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] font-bold focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                        @foreach($currencies as $curr)
                            <option value="{{ $curr->code }}" {{ old('currency_code', 'USD') === $curr->code ? 'selected' : '' }}>
                                {{ $curr->code }} ({{ $curr->symbol }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Freight Shipping Cost *</label>
                    <input type="number" step="0.01" name="shipping_cost" required value="{{ old('shipping_cost', 2500.00) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] font-mono focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Declared Cargo Value</label>
                    <input type="number" step="0.01" name="declared_value" value="{{ old('declared_value', 25000.00) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] font-mono focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Insurance Premium</label>
                    <input type="number" step="0.01" name="insurance_fee" value="{{ old('insurance_fee', 100.00) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] font-mono focus:outline-none focus:bg-white focus:border-[#1E3A8A]">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Special Handling Instructions</label>
                <textarea name="special_instructions" rows="2" placeholder="Cold-chain temperature control, fragile handling, customs pre-clearance notes..." class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] focus:outline-none focus:bg-white focus:border-[#1E3A8A]">{{ old('special_instructions') }}</textarea>
            </div>
        </div>

        <!-- 4. Initial Item Manifest -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#1E3A8A]"></span>
                <span>Cargo Item Manifest</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-6 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[10px] text-slate-500 mb-1">Item Description</label>
                    <input type="text" name="items[0][item_description]" value="{{ old('items.0.item_description', 'Standard Commercial Freight Goods') }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A]">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">Quantity</label>
                    <input type="number" name="items[0][quantity]" value="{{ old('items.0.quantity', 20) }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A]">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">Package Type</label>
                    <select name="items[0][package_type]" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A]">
                        <option value="Box">Box / Carton</option>
                        <option value="Pallet">Pallet</option>
                        <option value="Container">Container</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">Weight (kg)</label>
                    <input type="number" name="items[0][weight_kg]" value="{{ old('items.0.weight_kg', 1000) }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A]">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">HS Code</label>
                    <input type="text" name="items[0][hs_code]" value="{{ old('items.0.hs_code', '8471.50.00') }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] font-mono">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('shipments.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-[#1E3A8A] hover:bg-[#1D4ED8] text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2 border border-sky-400/30">
                <i class="fa-solid fa-paper-plane text-xs"></i>
                <span>{{ Auth::user()->isCustomer() ? 'Book Shipment Now' : 'Save Shipment' }}</span>
            </button>
        </div>
    </form>
</div>
@endsection