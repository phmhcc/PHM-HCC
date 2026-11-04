
@extends('layouts.dashboard')

@section('title', __('Dashboard').' | PHM HCC')

@section('content')

<header class="dashboard-heading dashboard-welcome">
    <div class="dashboard-welcome-copy">
        <h1>{{ __('Welcome Back, :name!', ['name' => auth()->user()->name]) }}</h1>
        <p>{{ __('Here is your overview of church activities and administration.') }}</p>
    </div>
    <div class="dashboard-welcome-meta">
        <div class="dashboard-user-chip"><span><i class="fas fa-user"></i></span>{{ auth()->user()->name }}</div>
        <div class="date-display"><i class="far fa-calendar-alt"></i> {{ now()->format('l, d F Y') }}</div>
    </div>
</header>

<!-- SUMMARY CARDS -->
<div class="dashboard-cards overview-stats">

    <div class="dashboard-card">
        <div class="card-top">
            <div>
                <p class="card-label">{{ __('Upcoming Events') }}</p>
                <h2 class="card-number">{{ $upcomingEvents ?? 0 }}</h2>
            </div>
            <div class="card-icon icon-blue">
                <i class="fas fa-calendar-days"></i>
            </div>
        </div>
        <p class="card-description">{{ __('Church activities and programs') }}</p>
    </div>

    <div class="dashboard-card">
        <div class="card-top">
            <div>
                <p class="card-label">{{ __('Scheduled Duties') }}</p>
                <h2 class="card-number">{{ $scheduledDuties ?? 0 }}</h2>
            </div>
            <div class="card-icon icon-gold">
                <i class="fas fa-clipboard-check"></i>
            </div>
        </div>
        <p class="card-description">{{ __('Upcoming duty assignments') }}</p>
    </div>

    <div class="dashboard-card">
        <div class="card-top">
            <div>
                <p class="card-label">{{ __('Pending Reports') }}</p>
                <h2 class="card-number">{{ $pendingReports ?? 0 }}</h2>
            </div>
            <div class="card-icon icon-purple">
                <i class="fas fa-file-circle-exclamation"></i>
            </div>
        </div>
        <p class="card-description">{{ __('Reports requiring attention') }}</p>
    </div>

    <div class="dashboard-card">
        <div class="card-top">
            <div>
                <p class="card-label">{{ __('Upcoming Meetings') }}</p>
                <h2 class="card-number">{{ $upcomingMeetings ?? 0 }}</h2>
            </div>
            <div class="card-icon icon-green">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <p class="card-description">{{ __('Scheduled church meetings') }}</p>
    </div>

</div>

@if(auth()->user()->hasPermissionTo('finance.view'))
<div class="dashboard-cards finance-summary-cards">
    <article class="dashboard-card">
        <p class="card-label">{{ __('Recorded income') }}</p>
        <h2 class="card-number">{{ number_format((float) ($financeTotals['income'] ?? 0), 2) }}</h2>
    </article>
    <article class="dashboard-card">
        <p class="card-label">{{ __('Recorded expenses') }}</p>
        <h2 class="card-number">{{ number_format((float) ($financeTotals['expense'] ?? 0), 2) }}</h2>
    </article>
</div>
@endif

<!-- ACTIVITIES AND NOTIFICATIONS -->
<div class="dashboard-grid overview-grid">

    <div class="content-box">

        <div class="box-heading">
            <h3><i class="fas fa-calendar-week"></i> {{ __('Weekly Activities') }}</h3>
            <a href="{{ route('dashboard.resource.index', 'events') }}" class="view-all">
                {{ __('View All') }}
            </a>
        </div>

        @forelse(($events ?? collect()) as $event)

            <div class="activity-item">

                <div class="activity-date">
                    <strong>{{ \Carbon\Carbon::parse($event->start_date)->format('d') }}</strong>
                    <small>{{ \Carbon\Carbon::parse($event->start_date)->format('M') }}</small>
                </div>

                <div class="activity-details">
                    <h4>{{ $event->title }}</h4>
                    <p>
                        <i class="far fa-clock"></i>
                        {{ \Carbon\Carbon::parse($event->start_date)->format('h:i A') }}
                        &nbsp; | &nbsp;
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $event->venue ?? __('Church premises') }}
                    </p>
                </div>

                <span class="activity-status">
                    {{ __(ucfirst($event->status ?? 'Scheduled')) }}
                </span>

            </div>

        @empty

            <div class="dashboard-empty-state">
                <span><i class="far fa-calendar"></i></span>
                <h2>{{ __('No upcoming activities') }}</h2>
                <p>{{ __('New church events will appear here.') }}</p>
            </div>

        @endforelse

    </div>

    <!-- NOTIFICATIONS -->
    <div class="content-box">

        <div class="box-heading">
            <h3><i class="far fa-bell"></i> {{ __('Recent Notifications') }}</h3>
            <a href="{{ route('dashboard.notifications') }}" class="view-all">
                {{ __('View All') }}
            </a>
        </div>

        @forelse(($notifications ?? collect()) as $notification)

            <div class="notification-item">

                <div class="notification-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>

                <div class="notification-text">
                    <h4>{{ $notification->data['title'] ?? __('Church notification') }}</h4>
                    <p>
                        {{ $notification->data['message'] ?? __('You have a new notification.') }}
                    </p>
                    <p>
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>

            </div>

        @empty

            <div class="dashboard-empty-state">
                <span><i class="far fa-bell"></i></span>
                <h2>{{ __('No new notifications') }}</h2>
                <p>{{ __('You’re all caught up.') }}</p>
            </div>

        @endforelse

    </div>

</div>

<!-- DUTY SCHEDULE -->
<div class="content-box" style="margin-bottom:25px;">

    <div class="box-heading">
        <h3><i class="fas fa-tasks"></i> {{ __('Upcoming Duty Schedule') }}</h3>
        <a href="{{ route('dashboard.resource.index', 'duties') }}" class="view-all">
            {{ __('View All') }}
        </a>
    </div>

    <div class="table-responsive">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>{{ __('Duty') }}</th>
                    <th>{{ __('Assigned to') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Department') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>

            <tbody>

                @forelse(($duties ?? collect()) as $duty)

                    <tr>
                        <td>{{ $duty->title }}</td>
                        <td>
                            {{ $duty->assignedUser->name ?? __('Not assigned') }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($duty->duty_date)->format('d M Y') }}
                        </td>
                        <td>
                            {{ $duty->department->name ?? __('General') }}
                        </td>
                        <td>
                            <span class="status-badge status-pending">
                                {{ __(ucfirst($duty->status ?? 'Pending')) }}
                            </span>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="5" style="text-align:center;">
                            {{ __('No upcoming duties available.') }}
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>

</div>

<!-- QUICK ACCESS -->
<div class="content-box">

    <div class="box-heading">
        <h3><i class="fas fa-bolt"></i> {{ __('Quick Access') }}</h3>
    </div>

    <div class="dashboard-cards">

        <a href="{{ route('dashboard.resource.index', 'events') }}" class="dashboard-card quick-access-card">
            <div class="card-icon icon-blue"><i class="fas fa-calendar-days"></i></div>
            <h2>{{ __('Activities') }}</h2>
            <p>{{ __('Plan and manage church activities.') }}</p>
        </a>

        @if(auth()->user()->hasPermissionTo('announcements.view'))
        <a href="{{ route('dashboard.resource.index', 'announcements') }}" class="dashboard-card quick-access-card">
            <div class="card-icon icon-gold"><i class="fas fa-bullhorn"></i></div>
            <h2>{{ __('Announcements') }}</h2>
            <p>{{ __('Share updates with the church family.') }}</p>
        </a>
        @endif

        <a href="{{ route('dashboard.resource.index', 'duties') }}" class="dashboard-card quick-access-card">
            <div class="card-icon icon-purple"><i class="fas fa-clipboard-list"></i></div>
            <h2>{{ __('Duties') }}</h2>
            <p>{{ __('Coordinate upcoming duty assignments.') }}</p>
        </a>

        <a href="{{ route('dashboard.resource.index', 'reports') }}" class="dashboard-card quick-access-card">
            <div class="card-icon icon-blue">
                <i class="fas fa-file-alt"></i>
            </div>
            <h2>{{ __('Reports') }}</h2>
            <p>{{ __('Submit and review church reports.') }}</p>
        </a>

        <a href="{{ route('dashboard.resource.index', 'meetings') }}" class="dashboard-card quick-access-card">
            <div class="card-icon icon-gold">
                <i class="fas fa-users"></i>
            </div>
            <h2>{{ __('Meetings') }}</h2>
            <p>{{ __('Manage church meetings and minutes.') }}</p>
        </a>

        <a href="{{ route('dashboard.resource.index', 'departments') }}" class="dashboard-card quick-access-card">
            <div class="card-icon icon-purple">
                <i class="fas fa-sitemap"></i>
            </div>
            <h2>{{ __('Departments') }}</h2>
            <p>{{ __('View church departments and leaders.') }}</p>
        </a>

        @if(auth()->user()->hasPermissionTo('finance.view'))
            <a href="{{ route('dashboard.resource.index', 'finance') }}" class="dashboard-card quick-access-card">
                <div class="card-icon icon-green">
                    <i class="fas fa-wallet"></i>
                </div>
                <h2>{{ __('Finance') }}</h2>
                <p>{{ __('View authorized financial records.') }}</p>
            </a>
        @endif

    </div>

</div>

@endsection
