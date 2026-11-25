@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Notifications</h1>
            <p class="text-xs text-slate-500 mt-0.5">Real-time alerts, checkpoint telemetry updates, and incident alerts.</p>
        </div>

        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 shadow-xs transition">
                <i class="fa-solid fa-check-double mr-1 text-slate-400"></i> Mark All as Read
            </button>
        </form>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200/90 shadow-sm divide-y divide-slate-100 overflow-hidden">
        @forelse($notifications as $notif)
        <div class="p-4 flex items-start justify-between gap-4 {{ $notif->isRead() ? 'bg-white text-slate-500' : 'bg-blue-50/20 text-slate-800' }} hover:bg-slate-50/70 transition">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0
                    {{ $notif->type === 'EXCEPTION_ALERT' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                    @if($notif->type === 'EXCEPTION_ALERT')
                        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                    @else
                        <i class="fa-solid fa-ship text-xs"></i>
                    @endif
                </div>

                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs font-bold {{ $notif->isRead() ? 'text-slate-700' : 'text-slate-900' }}">{{ $notif->title }}</h4>
                        @if(!$notif->isRead())
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600">{{ $notif->message }}</p>
                    <span class="text-[10px] text-slate-400 font-mono">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if(!$notif->isRead())
                <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs text-slate-700 font-medium transition">
                        Dismiss
                    </button>
                </form>
                @elseif($notif->action_url)
                <a href="{{ $notif->action_url }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs text-slate-600 font-medium transition">
                    View
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-slate-400 text-xs">
            <i class="fa-regular fa-bell-slash text-xl mb-1.5 block text-slate-300"></i>
            No notifications at this time.
        </div>
        @endforelse
    </div>

    <div>
        {{ $notifications->links() }}
    </div>
</div>
@endsection
