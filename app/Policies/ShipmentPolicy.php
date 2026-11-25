<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    /**
     * Determine whether the user can view any shipments.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the shipment.
     */
    public function view(User $user, Shipment $shipment): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }

        return $shipment->customer_id === $user->id;
    }

    /**
     * Determine whether the user can create shipments.
     */
    public function create(User $user): bool
    {
        return true; // Admin, Operator, and Customer can all create/book shipments
    }

    /**
     * Determine whether the user can update the shipment.
     */
    public function update(User $user, Shipment $shipment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isOperator()) {
            // Operator cannot mutate a delivered or cancelled shipment
            return !$shipment->isTerminal();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the shipment.
     */
    public function delete(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can add tracking events or exceptions.
     */
    public function addTracking(User $user, Shipment $shipment): bool
    {
        return $user->canManageShipments() && !$shipment->isTerminal();
    }
}
