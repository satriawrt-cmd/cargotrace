@extends('layouts.app')

@section('title', 'User Management (Admin Console)')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">System Administration</span>
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-bold border border-blue-200">
                    {{ $users->total() }} Total Accounts
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mt-0.5">User Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage system access roles across Administrators, Logistics Operators, and Enterprise Customers.</p>
        </div>

        <div>
            <button type="button" onclick="document.getElementById('createUserModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Add New User</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Role Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto text-xs pb-1 md:pb-0">
            <a href="{{ route('users.index', array_merge(request()->except('role', 'page'))) }}"
               class="px-3 py-1.5 rounded-xl font-semibold transition whitespace-nowrap {{ !request('role') ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
                All ({{ $roleCounts['all'] }})
            </a>
            <a href="{{ route('users.index', array_merge(request()->except('role', 'page'), ['role' => 'admin'])) }}"
               class="px-3 py-1.5 rounded-xl font-semibold transition whitespace-nowrap {{ request('role') === 'admin' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
                Administrators ({{ $roleCounts['admin'] }})
            </a>
            <a href="{{ route('users.index', array_merge(request()->except('role', 'page'), ['role' => 'operator'])) }}"
               class="px-3 py-1.5 rounded-xl font-semibold transition whitespace-nowrap {{ request('role') === 'operator' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
                Operators ({{ $roleCounts['operator'] }})
            </a>
            <a href="{{ route('users.index', array_merge(request()->except('role', 'page'), ['role' => 'customer'])) }}"
               class="px-3 py-1.5 rounded-xl font-semibold transition whitespace-nowrap {{ request('role') === 'customer' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
                Customers ({{ $roleCounts['customer'] }})
            </a>
        </div>

        <!-- Search Input -->
        <form action="{{ route('users.index') }}" method="GET" class="relative min-w-[240px]">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, company..."
                   class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:bg-white transition">
        </form>
    </div>

    <!-- Users Table -->
    <div class="rounded-2xl bg-white border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/70 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">User Details</th>
                        <th class="py-3 px-4">Assigned Role</th>
                        <th class="py-3 px-4">Company / Organization</th>
                        <th class="py-3 px-4">Consignments</th>
                        <th class="py-3 px-4">Preferred Currency</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- User Details -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0
                                    {{ $user->isAdmin() ? 'bg-blue-100 text-blue-700' : ($user->isOperator() ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700') }}">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block leading-tight">{{ $user->name }}</span>
                                    <span class="text-[11px] text-slate-400 font-mono">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Role Badge -->
                        <td class="py-3.5 px-4">
                            @if($user->isAdmin())
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="fa-solid fa-shield-halved text-[10px] mr-1"></i> Administrator
                                </span>
                            @elseif($user->isOperator())
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <i class="fa-solid fa-headset text-[10px] mr-1"></i> Logistics Operator
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-building text-[10px] mr-1"></i> Customer
                                </span>
                            @endif
                        </td>

                        <!-- Company -->
                        <td class="py-3.5 px-4">
                            <span class="text-slate-800 font-medium">{{ $user->company_name ?: '-' }}</span>
                            @if($user->phone)
                                <span class="block text-[10px] text-slate-400 font-mono mt-0.5">{{ $user->phone }}</span>
                            @endif
                        </td>

                        <!-- Shipments Count -->
                        <td class="py-3.5 px-4">
                            <span class="font-mono font-bold text-slate-800">{{ $user->shipments_count }}</span>
                            <span class="text-slate-400 text-[10px]"> consignments</span>
                        </td>

                        <!-- Currency -->
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-700">
                            {{ $user->preferred_currency ?: 'USD' }}
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" onclick="openEditModal({{ json_encode($user) }})"
                                        class="px-2.5 py-1 text-slate-600 hover:text-blue-600 bg-slate-50 hover:bg-blue-50 rounded-lg border border-slate-200 text-xs font-semibold transition" title="Edit User">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                </button>

                                @if($user->id !== Auth::id())
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Permanently delete user {{ $user->name }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-lg text-xs font-semibold transition" title="Delete User">
                                        <i class="fa-solid fa-trash text-[11px]"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                            No users found matching the selected criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create User Modal -->
<div id="createUserModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Add New User Account</h3>
                <p class="text-xs text-slate-400">Create login credentials and assign appropriate system role.</p>
            </div>
            <button type="button" onclick="document.getElementById('createUserModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Full Name</label>
                <input type="text" name="name" required placeholder="e.g. Budi Santoso" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Email Address</label>
                <input type="email" name="email" required placeholder="user@company.com" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 font-mono">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Role</label>
                    <select name="role" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
                        <option value="customer">Customer</option>
                        <option value="operator">Logistics Operator</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Currency</label>
                    <select name="preferred_currency" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 font-mono">
                        <option value="USD">USD ($)</option>
                        <option value="EUR">EUR (€)</option>
                        <option value="IDR">IDR (Rp)</option>
                        <option value="GBP">GBP (£)</option>
                        <option value="SGD">SGD (S$)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Company / Organization</label>
                <input type="text" name="company_name" placeholder="e.g. PT Global Trade Logistics" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Phone Number</label>
                <input type="text" name="phone" placeholder="+62 812 3456 7890" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Initial Password</label>
                <input type="password" name="password" required minlength="8" placeholder="At least 8 characters" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 font-mono">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('createUserModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-semibold transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-white bg-blue-600 hover:bg-blue-500 font-semibold shadow-xs transition">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Edit User Details</h3>
                <p class="text-xs text-slate-400">Modify profile attributes or assign different access role.</p>
            </div>
            <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="editUserForm" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Full Name</label>
                <input type="text" id="edit_name" name="name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Email Address</label>
                <input type="email" id="edit_email" name="email" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 font-mono">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Role</label>
                    <select id="edit_role" name="role" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
                        <option value="customer">Customer</option>
                        <option value="operator">Logistics Operator</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Currency</label>
                    <select id="edit_currency" name="preferred_currency" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 font-mono">
                        <option value="USD">USD ($)</option>
                        <option value="EUR">EUR (€)</option>
                        <option value="IDR">IDR (Rp)</option>
                        <option value="GBP">GBP (£)</option>
                        <option value="SGD">SGD (S$)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Company / Organization</label>
                <input type="text" id="edit_company" name="company_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Phone Number</label>
                <input type="text" id="edit_phone" name="phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Reset Password (Optional)</label>
                <input type="password" name="password" minlength="8" placeholder="Leave blank to keep current" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 font-mono">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200 font-semibold transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-white bg-blue-600 hover:bg-blue-500 font-semibold shadow-xs transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditModal(user) {
        document.getElementById('editUserForm').action = '/users/' + user.id;
        document.getElementById('edit_name').value = user.name || '';
        document.getElementById('edit_email').value = user.email || '';
        document.getElementById('edit_role').value = user.role || 'customer';
        document.getElementById('edit_currency').value = user.preferred_currency || 'USD';
        document.getElementById('edit_company').value = user.company_name || '';
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('editUserModal').classList.remove('hidden');
    }
</script>
@endpush
