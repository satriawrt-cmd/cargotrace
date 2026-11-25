@extends('layouts.app')

@section('title', 'Log Audit')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Log Audit Keamanan & Operasional</h1>
        <p class="text-xs text-slate-500 mt-0.5">Rekam jejak audit yang tidak dapat diubah atas aksi sistem, transisi status, peristiwa keamanan, dan mutasi data.</p>
    </div>

    <!-- Audit Logs Table (Soft Minimal Style) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-semibold text-slate-500">
                        <th class="py-3 px-5">Waktu (UTC)</th>
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Aksi / Tindakan</th>
                        <th class="py-3 px-4">Sumber Daya</th>
                        <th class="py-3 px-4">Perubahan Data</th>
                        <th class="py-3 px-4">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3 px-5 font-mono text-slate-500 text-[11px]">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="py-3 px-4">
                            @if($log->user)
                                <span class="font-bold text-slate-800">{{ $log->user->name }}</span>
                                <span class="block text-[10px] text-slate-400 font-mono">{{ $log->user->role }}</span>
                            @else
                                <span class="text-slate-400 italic">Sistem</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase font-mono
                                {{ str_contains($log->action, 'CREATE') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                {{ str_contains($log->action, 'UPDATE') ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                                {{ str_contains($log->action, 'EXCEPTION') ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                                {{ in_array($log->action, ['LOGIN', 'LOGOUT']) ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-medium text-slate-800">{{ $log->entity_type }}</span>
                            @if($log->entity_id)
                            <span class="text-[10px] text-slate-400 font-mono">#{{ $log->entity_id }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] max-w-xs">
                            @if($log->new_values)
                                <div class="bg-slate-50 p-2 rounded-lg border border-slate-200 text-slate-700 overflow-x-auto text-[10px]">
                                    {{ json_encode($log->new_values) }}
                                </div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-500 text-[11px]">
                            {{ $log->ip_address ?: '127.0.0.1' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">Belum ada riwayat audit log tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-slate-200/80 bg-slate-50/40">
            {{ $auditLogs->links() }}
        </div>
    </div>
</div>
@endsection
