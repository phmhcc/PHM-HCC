@extends('layouts.dashboard')

@section('title', 'Notifications | PHM HCC')
@section('page-heading', 'Notifications')

@section('content')
<section class="content-box">
    <div class="resource-records-heading">
        <div>
            <span class="role-eyebrow">Stay up to date</span>
            <h1 style="color:#123b64;font-size:22px;">Your notifications</h1>
        </div>
        <span>{{ $notifications->total() }} notifications</span>
    </div>
    @forelse($notifications as $notification)
        <article class="notification-page-item {{ $notification->read_at ? '' : 'is-unread' }}">
            <div class="notification-icon"><i class="fas fa-bullhorn"></i></div>
            <div class="notification-text">
                <h4>{{ $notification->data['title'] ?? 'Church notification' }}</h4>
                <p>{{ $notification->data['message'] ?? '' }}</p>
                <p><i class="far fa-clock"></i> {{ $notification->created_at->diffForHumans() }}</p>
                @if(! $notification->read_at)
                    <form method="POST" action="{{ route('dashboard.notifications.read', $notification->id) }}">
                        @csrf
                        @method('PATCH')
                        <button class="dashboard-action dashboard-action-secondary" type="submit">Mark as read</button>
                    </form>
                @endif
            </div>
            <span class="notification-status {{ $notification->read_at ? '' : 'unread' }}">{{ $notification->read_at ? 'Read' : 'Unread' }}</span>
        </article>
    @empty
        <div class="dashboard-empty-state">
            <span><i class="far fa-bell"></i></span>
            <h2>You’re all caught up</h2>
            <p>New church updates will appear here.</p>
        </div>
    @endforelse
    {{ $notifications->links() }}
</section>
@endsection
