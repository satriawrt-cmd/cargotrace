@extends('layouts.app')

@section('title', 'Add Shipment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('shipments.index') }}" class="p-2 rounded-xl bg-white hover:bg-slate-50 text-slate-500 border border-slate-200 shadow-xs transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Add Global Shipment</h1>
            <p class="text-xs text-slate-500">Register consignment with international ports and multi-currency billing.</p>
        </div>
    </div>

    <form action="{{ route('shipments.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- 1. Stakeholders & Worldwide Route -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Stakeholders & Worldwide Route</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Consignee / Client *</label>
                    <select name="customer_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        <option value="">Select Customer</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} ({{ $cust->company_name ?? $cust->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Carrier Line *</label>
                    <select name="carrier_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        <option value="">Select Carrier</option>
                        @foreach($carriers as $car)
                            <option value="{{ $car->id }}" {{ old('carrier_id') == $car->id ? 'selected' : '' }}>
                                {{ $car->name }} ({{ $car->service_type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Origin Port *</label>
                    <select name="origin_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
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
                    <select name="destination_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
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
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Cargo & Schedule</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">General Description *</label>
                    <input type="text" name="cargo_type" required value="{{ old('cargo_type') }}" placeholder="e.g. Precision Robotics & Equipment"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Weight (KG) *</label>
                    <input type="number" step="0.01" name="weight_kg" required value="{{ old('weight_kg', 1000) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Volume (CBM) *</label>
                    <input type="number" step="0.01" name="volume_cbm" required value="{{ old('volume_cbm', 10) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Expected Departure *</label>
                    <input type="datetime-local" name="expected_departure" required value="{{ old('expected_departure', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div class="sm:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Expected Arrival *</label>
                        <input type="datetime-local" name="expected_arrival" required value="{{ old('expected_arrival', now()->addDays(14)->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Estimated Arrival (ETA)</label>
                        <input type="datetime-local" name="estimated_arrival" value="{{ old('estimated_arrival', now()->addDays(14)->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Multi-Currency Financial Ledger -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Multi-Currency Transaction Ledger</h3>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Transaction Currency *</label>
                    <select name="currency_code" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-bold focus:outline-none focus:bg-white focus:border-blue-500">
                        @foreach($currencies as $curr)
                            <option value="{{ $curr->code }}" {{ old('currency_code', 'USD') === $curr->code ? 'selected' : '' }}>
                                {{ $curr->code }} ({{ $curr->symbol }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Freight Cost *</label>
                    <input type="number" step="0.01" name="shipping_cost" required value="{{ old('shipping_cost', 3000.00) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Declared Value</label>
                    <input type="number" step="0.01" name="declared_value" value="{{ old('declared_value', 45000.00) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Insurance Fee</label>
                    <input type="number" step="0.01" name="insurance_fee" value="{{ old('insurance_fee', 135.00) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono focus:outline-none focus:bg-white focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Special Instructions</label>
                <textarea name="special_instructions" rows="2" placeholder="Handling notes..." class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">{{ old('special_instructions') }}</textarea>
            </div>
        </div>

        <!-- 4. Initial Item Manifest -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Initial Manifest Line Item</h3>

            <div class="grid grid-cols-1 sm:grid-cols-6 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[10px] text-slate-500 mb-1">Description</label>
                    <input type="text" name="items[0][item_description]" value="Consolidated Industrial Hardware" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">Quantity</label>
                    <input type="number" name="items[0][quantity]" value="50" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">Package</label>
                    <select name="items[0][package_type]" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                        <option value="Pallet">Pallet</option>
                        <option value="Box">Box</option>
                        <option value="Container">Container</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">Weight (kg)</label>
                    <input type="number" name="items[0][weight_kg]" value="1000" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-1">HS Code</label>
                    <input type="text" name="items[0][hs_code]" value="8471.50.00" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('shipments.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                Create Consignment
            </button>
        </div>
    </form>
</div>
@endsection
