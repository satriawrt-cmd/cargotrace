@extends('layouts.app')

@section('title', 'Edit Shipment ' . $shipment->shipment_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('shipments.show', $shipment->id) }}" class="p-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-white">Edit Consignment Details</h1>
            <p class="text-xs text-slate-400">{{ $shipment->shipment_number }} • {{ $shipment->origin->city }} → {{ $shipment->destination->city }}</p>
        </div>
    </div>

    <form action="{{ route('shipments.update', $shipment->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-brand-400"></i>
                <span>Editable Parameters</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Carrier</label>
                    <select name="carrier_id" required class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                        @foreach($carriers as $car)
                            <option value="{{ $car->id }}" {{ old('carrier_id', $shipment->carrier_id) == $car->id ? 'selected' : '' }}>
                                {{ $car->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Cargo Description</label>
                    <input type="text" name="cargo_type" required value="{{ old('cargo_type', $shipment->cargo_type) }}" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Gross Weight (kg)</label>
                    <input type="number" step="0.01" name="weight_kg" required value="{{ old('weight_kg', $shipment->weight_kg) }}" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Gross Volume (CBM)</label>
                    <input type="number" step="0.01" name="volume_cbm" required value="{{ old('volume_cbm', $shipment->volume_cbm) }}" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Expected Departure Date</label>
                    <input type="datetime-local" name="expected_departure" required value="{{ old('expected_departure', $shipment->expected_departure->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Expected Arrival Date</label>
                    <input type="datetime-local" name="expected_arrival" required value="{{ old('expected_arrival', $shipment->expected_arrival->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Estimated Arrival Date (ETA)</label>
                    <input type="datetime-local" name="estimated_arrival" required value="{{ old('estimated_arrival', $shipment->estimated_arrival->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Freight Cost ({{ $shipment->currency_code }})</label>
                    <input type="number" step="0.01" name="shipping_cost" required value="{{ old('shipping_cost', $shipment->shipping_cost) }}" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Special Instructions</label>
                    <textarea name="special_instructions" rows="2" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">{{ old('special_instructions', $shipment->special_instructions) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('shipments.show', $shipment->id) }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs shadow-lg shadow-brand-500/25 transition">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
