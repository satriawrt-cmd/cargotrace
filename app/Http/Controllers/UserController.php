<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users (Administrator only).
     */
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Administrator access required.');
        }

        $query = User::query()->withCount('shipments');

        // Filter by role
        if ($request->filled('role') && in_array($request->role, ['admin', 'operator', 'customer'])) {
            $query->where('role', $request->role);
        }

        // Search by name, email, or company
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(12)->withQueryString();

        $roleCounts = [
            'all' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'operator' => User::where('role', 'operator')->count(),
            'customer' => User::where('role', 'customer')->count(),
        ];

        return view('users.index', compact('users', 'roleCounts'));
    }

    /**
     * Store a newly created user (Administrator only).
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Administrator access required.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(['admin', 'operator', 'customer'])],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'preferred_currency' => ['nullable', 'string', 'size:3'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['preferred_currency'] = $validated['preferred_currency'] ?? 'USD';

        $user = User::create($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'CREATE_USER',
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'new_values' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'company_name' => $user->company_name,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "User '{$user->name}' ({$user->role}) successfully created.");
    }

    /**
     * Update the specified user (Administrator only).
     */
    public function update(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Administrator access required.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(['admin', 'operator', 'customer'])],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'preferred_currency' => ['nullable', 'string', 'size:3'],
        ]);

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'company_name' => $user->company_name,
        ];

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'UPDATE_USER',
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'company_name' => $user->company_name,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Remove the specified user (Administrator only).
     */
    public function destroy(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Administrator access required.');
        }

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own administrator account.']);
        }

        $userName = $user->name;
        $userRole = $user->role;
        $userId = $user->id;

        $user->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'DELETE_USER',
            'entity_type' => 'User',
            'entity_id' => $userId,
            'old_values' => ['name' => $userName, 'role' => $userRole],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "User '{$userName}' successfully deleted.");
    }
}
