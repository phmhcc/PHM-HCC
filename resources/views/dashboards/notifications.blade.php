@extends('layouts.dashboard')

@section('title', __('Notifications').' | PHM HCC')

@section('content')
<section class="content-box">
    <div class="resource-records-heading">
        <div>
            <h1 style="color:#123b64;font-size:22px;">{{ __('Your notifications') }}</h1>
        </div>
        <span>{{ $notifications->total() }} {{ __('Notifications') }}</span>
    </div>
    @forelse($notifications as $notification)
        <article class="notification-page-item {{ $notification->read_at ? '' : 'is-unread' }}">
            <div class="notification-icon"><i class="fas fa-bullhorn"></i></div>
            <div class="notification-text">
                <h4>{{ \App\Notifications\DashboardInformationPublished::localizedTitle($notification->data) }}</h4>
                <p>{{ \App\Notifications\DashboardInformationPublished::localizedMessage($notification->data) }}</p>
                <p><i class="far fa-clock"></i> {{ $notification->created_at->locale(app()->getLocale())->diffForHumans() }}</p>
                @if(! $notification->read_at)
                    <form method="POST" action="{{ route('dashboard.notifications.read', $notification->id) }}">
                        @csrf
                        @method('PATCH')
                        <button class="dashboard-action dashboard-action-secondary" type="submit">{{ __('Mark as read') }}</button>
                    </form>
                @endif
            </div>
            <span class="notification-status {{ $notification->read_at ? '' : 'unread' }}">{{ $notification->read_at ? __('Read') : __('Unread') }}</span>
        </article>
    @empty
        <div class="dashboard-empty-state">
            <span><i class="far fa-bell"></i></span>
            <h2>{{ __('You’re all caught up.') }}</h2>
            <p>{{ __('New church updates will appear here.') }}</p>
        </div>
    @endforelse
    {{ $notifications->links() }}
</section>
@endsection
