@extends('layouts.app')

@section('title', 'Edit Shipment ' . $shipment->shipment_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('shipments.show', $shipment->id) }}" class="p-2 rounded-xl bg-white hover:bg-slate-50 text-slate-500 border border-slate-200 shadow-xs transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Edit Shipment</h1>
            <p class="text-xs text-slate-500">{{ $shipment->shipment_number }} • {{ $shipment->origin->city }} → {{ $shipment->destination->city }}</p>
        </div>
    </div>

    <form action="{{ route('shipments.update', $shipment->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Consignment Parameters</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Carrier</label>
                    <select name="carrier_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        @foreach($carriers as $car)
                            <option value="{{ $car->id }}" {{ old('carrier_id', $shipment->carrier_id) == $car->id ? 'selected' : '' }}>
                                {{ $car->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Cargo Description</label>
                    <input type="text" name="cargo_type" required value="{{ old('cargo_type', $shipment->cargo_type) }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Weight (KG)</label>
                    <input type="number" step="0.01" name="weight_kg" required value="{{ old('weight_kg', $shipment->weight_kg) }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Volume (CBM)</label>
                    <input type="number" step="0.01" name="volume_cbm" required value="{{ old('volume_cbm', $shipment->volume_cbm) }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Expected Departure</label>
                    <input type="datetime-local" name="expected_departure" required value="{{ old('expected_departure', $shipment->expected_departure->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Expected Arrival</label>
                    <input type="datetime-local" name="expected_arrival" required value="{{ old('expected_arrival', $shipment->expected_arrival->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Estimated Arrival (ETA)</label>
                    <input type="datetime-local" name="estimated_arrival" required value="{{ old('estimated_arrival', $shipment->estimated_arrival->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Freight Cost ({{ $shipment->currency_code }})</label>
                    <input type="number" step="0.01" name="shipping_cost" required value="{{ old('shipping_cost', $shipment->shipping_cost) }}" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Special Instructions</label>
                    <textarea name="special_instructions" rows="2" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">{{ old('special_instructions', $shipment->special_instructions) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('shipments.show', $shipment->id) }}" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                Update Consignment
            </button>
        </div>
    </form>
</div>
@endsection
