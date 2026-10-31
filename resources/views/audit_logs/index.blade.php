@extends('layouts.app')

@section('title', 'System Audit Trail')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-3">
                <i class="fa-solid fa-shield-halved text-purple-400"></i>
                <span>Security & Operational Audit Logs</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Immutable audit trail of actions, state transitions, security events, and entity mutations.</p>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="rounded-3xl bg-slate-900 border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 uppercase text-[10px] tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Timestamp (UTC)</th>
                        <th class="py-3 px-4">Actor / User</th>
                        <th class="py-3 px-4">Action</th>
                        <th class="py-3 px-4">Target Resource</th>
                        <th class="py-3 px-4">Payload Changes</th>
                        <th class="py-3 px-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="py-3.5 px-4">
                            @if($log->user)
                                <span class="font-bold text-white">{{ $log->user->name }}</span>
                                <div class="text-[10px] text-slate-500 font-mono">{{ $log->user->role }}</div>
                            @else
                                <span class="text-slate-500 italic">System / Unauthenticated</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase font-mono
                                {{ str_contains($log->action, 'CREATE') ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : '' }}
                                {{ str_contains($log->action, 'UPDATE') ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : '' }}
                                {{ str_contains($log->action, 'EXCEPTION') ? 'bg-red-500/20 text-red-400 border border-red-500/30' : '' }}
                                {{ in_array($log->action, ['LOGIN', 'LOGOUT']) ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : '' }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-semibold text-slate-300">{{ $log->entity_type }}</span>
                            @if($log->entity_id)
                            <span class="text-[10px] font-mono text-slate-500">#{{ $log->entity_id }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-mono text-[11px] max-w-xs">
                            @if($log->new_values)
                                <div class="bg-slate-950 p-2 rounded-lg border border-slate-800 overflow-x-auto text-slate-300">
                                    {{ json_encode($log->new_values) }}
                                </div>
                            @else
                                <span class="text-slate-600">-</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]">
                            {{ $log->ip_address ?: '127.0.0.1' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">No audit logs recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            {{ $auditLogs->links() }}
        </div>
    </div>
</div>
@endsection
