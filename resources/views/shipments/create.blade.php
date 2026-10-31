@extends('layouts.app')

@section('title', 'Register New Global Shipment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('shipments.index') }}" class="p-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-white">Register New Global Shipment</h1>
            <p class="text-xs text-slate-400">Initiate an international cargo voyage with worldwide ports and multi-currency billing.</p>
        </div>
    </div>

    <form action="{{ route('shipments.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- 1. Stakeholders & Route -->
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-route text-brand-400"></i>
                <span>Stakeholders & Worldwide Route</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Customer -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Client / Consignee *</label>
                    <select name="customer_id" required class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                        <option value="">Select Customer</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} ({{ $cust->company_name ?? $cust->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Carrier -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Carrier / Line *</label>
                    <select name="carrier_id" required class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                        <option value="">Select Carrier</option>
                        @foreach($carriers as $car)
                            <option value="{{ $car->id }}" {{ old('carrier_id') == $car->id ? 'selected' : '' }}>
                                {{ $car->name }} ({{ $car->service_type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Origin Port -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Origin Port / Hub *</label>
                    <select name="origin_id" required class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                        <option value="">Select Origin Location</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('origin_id') == $loc->id ? 'selected' : '' }}>
                                [{{ $loc->continent }}] {{ $loc->city }}, {{ $loc->country }} - {{ $loc->name }} ({{ $loc->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Destination Port -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Destination Port / Hub *</label>
                    <select name="destination_id" required class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
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
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-packing text-cyan-400"></i>
                <span>Cargo Specifications & Schedule</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Cargo General Description *</label>
                    <input type="text" name="cargo_type" required value="{{ old('cargo_type') }}" placeholder="e.g. Semiconductor Integrated Circuits"
                           class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Gross Weight (KG) *</label>
                    <input type="number" step="0.01" name="weight_kg" required value="{{ old('weight_kg', 1000) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Gross Volume (CBM) *</label>
                    <input type="number" step="0.01" name="volume_cbm" required value="{{ old('volume_cbm', 12.5) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Expected Departure Date *</label>
                    <input type="datetime-local" name="expected_departure" required value="{{ old('expected_departure', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>

                <div class="sm:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Expected Arrival Date *</label>
                        <input type="datetime-local" name="expected_arrival" required value="{{ old('expected_arrival', now()->addDays(14)->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Estimated Arrival Date (ETA)</label>
                        <input type="datetime-local" name="estimated_arrival" value="{{ old('estimated_arrival', now()->addDays(14)->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Global Multi-Currency Financial Ledger -->
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-coins text-amber-400"></i>
                    <span>Multi-Currency Transaction Ledger</span>
                </h3>
                <span class="text-[10px] text-slate-400 font-mono">World Currencies Supported</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <!-- Transaction Currency -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Currency Code *</label>
                    <select name="currency_code" required class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500 font-bold">
                        @foreach($currencies as $curr)
                            <option value="{{ $curr->code }}" {{ old('currency_code', 'USD') === $curr->code ? 'selected' : '' }}>
                                {{ $curr->code }} ({{ $curr->symbol }} - {{ $curr->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Shipping Freight Cost *</label>
                    <input type="number" step="0.01" name="shipping_cost" required value="{{ old('shipping_cost', 3500.00) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Declared Cargo Value</label>
                    <input type="number" step="0.01" name="declared_value" value="{{ old('declared_value', 50000.00) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Cargo Insurance Fee</label>
                    <input type="number" step="0.01" name="insurance_fee" value="{{ old('insurance_fee', 150.00) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500 font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Special Handling Instructions</label>
                <textarea name="special_instructions" rows="2" placeholder="e.g. Keep dry, stow away from boiler room..." class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">{{ old('special_instructions') }}</textarea>
            </div>
        </div>

        <!-- 4. Cargo Manifest Line Items -->
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-list text-emerald-400"></i>
                <span>Initial Cargo Manifest Item</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-6 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[11px] text-slate-400 mb-1">Description</label>
                    <input type="text" name="items[0][item_description]" value="Precision Electronics & Sensors" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-400 mb-1">Quantity</label>
                    <input type="number" name="items[0][quantity]" value="100" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-400 mb-1">Package</label>
                    <select name="items[0][package_type]" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                        <option value="Pallet">Pallet</option>
                        <option value="Box">Box</option>
                        <option value="Container">Container</option>
                        <option value="Crate">Crate</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] text-slate-400 mb-1">Weight (kg)</label>
                    <input type="number" name="items[0][weight_kg]" value="1000" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-400 mb-1">HS Tariff Code</label>
                    <input type="text" name="items[0][hs_code]" value="8542.31.00" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white font-mono">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('shipments.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs shadow-lg shadow-brand-500/25 transition">
                Create Consignment & Initialize Voyage
            </button>
        </div>
    </form>
</div>
@endsection
