@extends('layouts.dashboard')

@section('title', 'Notifications')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-black text-slate-900">Notifications</h1>
            @if($unreadCount > 0)
                <span class="px-2.5 py-0.5 rounded-full bg-sky-600 text-white text-xs font-bold">🔔 {{ $unreadCount }} Unread</span>
            @endif
        </div>

        @if($unreadCount > 0)
            <form action="{{ route('dashboard.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-200 transition">
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        @if($notifications->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($notifications as $n)
                    <div class="p-4 sm:p-5 flex items-start justify-between gap-4 {{ is_null($n->read_at) ? 'bg-sky-50/40 font-semibold' : 'bg-white' }}">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase bg-slate-100 text-slate-600">{{ $n->data['type'] ?? 'System' }}</span>
                                <h3 class="text-sm font-bold text-slate-900">{{ $n->data['title'] ?? 'Notification' }}</h3>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $n->data['message'] ?? '' }}</p>
                            <span class="text-[11px] text-slate-400 block pt-1">{{ $n->created_at->diffForHumans() }}</span>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if(!empty($n->data['action_url']))
                                <form action="{{ route('dashboard.notifications.read', $n->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-sky-600 text-white font-bold text-xs rounded-xl shadow">View</button>
                                </form>
                            @elseif(is_null($n->read_at))
                                <form action="{{ route('dashboard.notifications.read', $n->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-slate-200 text-slate-700 font-bold text-xs rounded-xl">Mark Read</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
        @else
            <p class="text-xs text-slate-500 text-center py-8">You have no notifications at this time.</p>
        @endif
    </div>
</div>
@endsection
