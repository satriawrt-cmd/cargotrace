<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageShipments() ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:users,id'],
            'carrier_id' => ['required', 'exists:carriers,id'],
            'origin_id' => ['required', 'different:destination_id', 'exists:locations,id'],
            'destination_id' => ['required', 'different:origin_id', 'exists:locations,id'],
            'cargo_type' => ['required', 'string', 'max:255'],
            'weight_kg' => ['required', 'numeric', 'min:0.01'],
            'volume_cbm' => ['required', 'numeric', 'min:0.01'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date', 'after_or_equal:expected_departure'],
            'estimated_arrival' => ['nullable', 'date', 'after_or_equal:expected_departure'],
            'currency_code' => ['required', 'string', 'size:3', 'exists:currencies,code'],
            'shipping_cost' => ['required', 'numeric', 'min:0'],
            'declared_value' => ['nullable', 'numeric', 'min:0'],
            'insurance_fee' => ['nullable', 'numeric', 'min:0'],
            'special_instructions' => ['nullable', 'string', 'max:1000'],
            'items' => ['nullable', 'array'],
            'items.*.item_description' => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.package_type' => ['nullable', 'string'],
            'items.*.weight_kg' => ['nullable', 'numeric', 'min:0'],
            'items.*.declared_value' => ['nullable', 'numeric', 'min:0'],
            'items.*.hs_code' => ['nullable', 'string', 'max:50'],
        ];
    }
}
