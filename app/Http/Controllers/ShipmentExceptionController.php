<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResolveShipmentExceptionRequest;
use App\Http\Requests\StoreShipmentExceptionRequest;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ShipmentExceptionController extends Controller
{
    public function __construct(
        protected ExceptionService $exceptionService
    ) {}

    public function store(StoreShipmentExceptionRequest $request, Shipment $shipment)
    {
        Gate::authorize('addTracking', $shipment);

        try {
            $this->exceptionService->createException(
                $shipment,
                $request->validated(),
                Auth::user()
            );

            return back()->with('success', 'Incident exception logged and flagged on timeline.');
        } catch (\Throwable $e) {
            return back()->withErrors(['exception_error' => $e->getMessage()]);
        }
    }

    public function resolve(ResolveShipmentExceptionRequest $request, ShipmentException $exception)
    {
        Gate::authorize('addTracking', $exception->shipment);

        try {
            $this->exceptionService->resolveException(
                $exception,
                $request->validated(),
                Auth::user()
            );

            return back()->with('success', 'Shipment exception successfully marked as RESOLVED.');
        } catch (\Throwable $e) {
            return back()->withErrors(['resolve_error' => $e->getMessage()]);
        }
    }
}
