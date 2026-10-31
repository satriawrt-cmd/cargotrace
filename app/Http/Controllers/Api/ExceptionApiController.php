<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResolveShipmentExceptionRequest;
use App\Http\Requests\StoreShipmentExceptionRequest;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExceptionApiController extends Controller
{
    public function __construct(
        protected ExceptionService $exceptionService
    ) {}

    public function index(Request $request, Shipment $shipment): JsonResponse
    {
        Gate::authorize('view', $shipment);

        $exceptions = $shipment->exceptions()
            ->with(['reportedBy:id,name', 'resolvedBy:id,name'])
            ->get();

        return response()->json(['data' => $exceptions], 200);
    }

    public function store(StoreShipmentExceptionRequest $request, Shipment $shipment): JsonResponse
    {
        Gate::authorize('addTracking', $shipment);

        $exception = $this->exceptionService->createException(
            $shipment,
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'message' => 'Incident exception recorded.',
            'data' => $exception,
        ], 201);
    }

    public function update(ResolveShipmentExceptionRequest $request, ShipmentException $exception): JsonResponse
    {
        Gate::authorize('addTracking', $exception->shipment);

        $resolved = $this->exceptionService->resolveException(
            $exception,
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'message' => 'Exception marked as RESOLVED.',
            'data' => $resolved,
        ], 200);
    }
}
