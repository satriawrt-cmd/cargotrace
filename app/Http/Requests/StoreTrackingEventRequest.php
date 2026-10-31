<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrackingEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageShipments() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'max:50'],
            'location_name' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'event_time' => ['nullable', 'date'],
            'description' => ['required', 'string', 'max:1000'],
            'shipment_status' => ['nullable', 'string', 'in:BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED'],
        ];
    }
}
