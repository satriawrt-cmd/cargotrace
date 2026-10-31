<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'LOGIN',
                'entity_type' => 'User',
                'entity_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'preferred_currency' => ['nullable', 'string', 'size:3', 'exists:currencies,code'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'preferred_currency' => $validated['preferred_currency'] ?? 'USD',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Account registered successfully! Welcome to CargoTrace.');
    }

    public function quickLogin(Request $request, string $role)
    {
        $emailMap = [
            'admin' => 'admin@cargotrace.test',
            'operator' => 'operator@cargotrace.test',
            'customer' => 'customer@globaltrade.test',
        ];

        $targetEmail = $emailMap[$role] ?? null;
        if (!$targetEmail) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid quick login role.']);
        }

        $user = User::where('email', $targetEmail)->first();
        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('dashboard')
                ->with('success', "Logged in as {$user->name} ({$user->role})");
        }

        return redirect()->route('login')->withErrors(['email' => 'User not found.']);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'LOGOUT',
                'entity_type' => 'User',
                'entity_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been logged out.');
    }
}
