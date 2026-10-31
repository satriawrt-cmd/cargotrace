@extends('layouts.app')

@section('title', 'Notifications Center')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-3">
                <i class="fa-regular fa-bell text-brand-400"></i>
                <span>Notifications Center</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Real-time alerts, checkpoint updates, and incident alerts for your account.</p>
        </div>

        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition">
                <i class="fa-solid fa-check-double mr-1.5"></i> Mark All as Read
            </button>
        </form>
    </div>

    <div class="rounded-3xl bg-slate-900 border border-slate-800 shadow-xl divide-y divide-slate-800/80 overflow-hidden">
        @forelse($notifications as $notif)
        <div class="p-5 flex items-start justify-between gap-4 {{ $notif->isRead() ? 'bg-slate-900/40 text-slate-400' : 'bg-slate-900 text-white font-medium' }} hover:bg-slate-800/40 transition">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0
                    {{ $notif->type === 'EXCEPTION_ALERT' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-brand-500/10 text-brand-400 border border-brand-500/20' }}">
                    @if($notif->type === 'EXCEPTION_ALERT')
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    @else
                        <i class="fa-solid fa-ship"></i>
                    @endif
                </div>

                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-bold {{ $notif->isRead() ? 'text-slate-300' : 'text-white' }}">{{ $notif->title }}</h4>
                        @if(!$notif->isRead())
                        <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-300">{{ $notif->message }}</p>
                    <span class="text-[10px] text-slate-500 font-mono">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if(!$notif->isRead())
                <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 hover:text-white transition">
                        View & Dismiss
                    </button>
                </form>
                @elseif($notif->action_url)
                <a href="{{ $notif->action_url }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs text-slate-400 hover:text-white transition">
                    View
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="p-12 text-center text-slate-500 text-xs">
            <i class="fa-regular fa-bell-slash text-2xl mb-2 block"></i>
            You have no notifications at this time.
        </div>
        @endforelse
    </div>

    <div>
        {{ $notifications->links() }}
    </div>
</div>
@endsection
