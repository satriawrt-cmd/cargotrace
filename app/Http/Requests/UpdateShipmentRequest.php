<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageShipments() ?? false;
    }

    public function rules(): array
    {
        return [
            'carrier_id' => ['sometimes', 'required', 'exists:carriers,id'],
            'cargo_type' => ['sometimes', 'required', 'string', 'max:255'],
            'weight_kg' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'volume_cbm' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'expected_departure' => ['sometimes', 'required', 'date'],
            'expected_arrival' => ['sometimes', 'required', 'date'],
            'estimated_arrival' => ['sometimes', 'required', 'date'],
            'shipping_cost' => ['sometimes', 'required', 'numeric', 'min:0'],
            'declared_value' => ['nullable', 'numeric', 'min:0'],
            'insurance_fee' => ['nullable', 'numeric', 'min:0'],
            'special_instructions' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
