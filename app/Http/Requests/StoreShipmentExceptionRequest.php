<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentExceptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageShipments() ?? false;
    }

    public function rules(): array
    {
        return [
            'exception_type' => ['required', 'string', 'in:CUSTOMS_HOLD,WEATHER_DELAY,PORT_CONGESTION,DAMAGED,MISROUTED,DOCUMENTATION_ISSUE'],
            'severity' => ['required', 'string', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'detected_at' => ['nullable', 'date'],
            'root_cause' => ['required', 'string', 'max:1000'],
            'action_plan' => ['nullable', 'string', 'max:1000'],
            'delay_impact_hours' => ['nullable', 'integer', 'min:0', 'max:720'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
