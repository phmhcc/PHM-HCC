
@extends('layouts.dashboard')

@section('title', 'Dashboard | PHM HCC')
@section('page-heading', 'Church Overview')

@section('content')

<header class="dashboard-heading dashboard-welcome">
    <div class="dashboard-welcome-copy">
        <span class="dashboard-welcome-eyebrow"><i class="fas fa-sun"></i> Church management portal</span>
        <h1>Welcome Back, {{ auth()->user()->name }}!</h1>
        <p>Here is your overview of church activities and administration.</p>
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
                <p class="card-label">Upcoming Events</p>
                <h2 class="card-number">{{ $upcomingEvents ?? 0 }}</h2>
            </div>
            <div class="card-icon icon-blue">
                <i class="fas fa-calendar-days"></i>
            </div>
        </div>
        <p class="card-description">Church activities and programs</p>
    </div>

    <div class="dashboard-card">
        <div class="card-top">
            <div>
                <p class="card-label">Scheduled Duties</p>
                <h2 class="card-number">{{ $scheduledDuties ?? 0 }}</h2>
            </div>
            <div class="card-icon icon-gold">
                <i class="fas fa-clipboard-check"></i>
            </div>
        </div>
        <p class="card-description">Upcoming duty assignments</p>
    </div>

    <div class="dashboard-card">
        <div class="card-top">
            <div>
                <p class="card-label">Pending Reports</p>
                <h2 class="card-number">{{ $pendingReports ?? 0 }}</h2>
            </div>
            <div class="card-icon icon-purple">
                <i class="fas fa-file-circle-exclamation"></i>
            </div>
        </div>
        <p class="card-description">Reports requiring attention</p>
    </div>

    <div class="dashboard-card">
        <div class="card-top">
            <div>
                <p class="card-label">Upcoming Meetings</p>
                <h2 class="card-number">{{ $upcomingMeetings ?? 0 }}</h2>
            </div>
            <div class="card-icon icon-green">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <p class="card-description">Scheduled church meetings</p>
    </div>

</div>

@if(auth()->user()->email === config('admin.email'))
<div class="dashboard-cards finance-summary-cards">
    <article class="dashboard-card">
        <p class="card-label">Recorded income</p>
        <h2 class="card-number">{{ number_format((float) ($financeTotals['income'] ?? 0), 2) }}</h2>
    </article>
    <article class="dashboard-card">
        <p class="card-label">Recorded expenses</p>
        <h2 class="card-number">{{ number_format((float) ($financeTotals['expense'] ?? 0), 2) }}</h2>
    </article>
</div>
@endif

<!-- ACTIVITIES AND NOTIFICATIONS -->
<div class="dashboard-grid overview-grid">

    <div class="content-box">

        <div class="box-heading">
            <h3><i class="fas fa-calendar-week"></i> Weekly Activities</h3>
            <a href="{{ route('dashboard.resource.index', 'events') }}" class="view-all">
                View All
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
                        {{ $event->venue ?? 'Church premises' }}
                    </p>
                </div>

                <span class="activity-status">
                    {{ ucfirst($event->status ?? 'Scheduled') }}
                </span>

            </div>

        @empty

            <div class="dashboard-empty-state">
                <span><i class="far fa-calendar"></i></span>
                <h2>No upcoming activities</h2>
                <p>New church events will appear here.</p>
            </div>

        @endforelse

    </div>

    <!-- NOTIFICATIONS -->
    <div class="content-box">

        <div class="box-heading">
            <h3><i class="far fa-bell"></i> Recent Notifications</h3>
            <a href="{{ route('dashboard.notifications') }}" class="view-all">
                View All
            </a>
        </div>

        @forelse(($notifications ?? collect()) as $notification)

            <div class="notification-item">

                <div class="notification-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>

                <div class="notification-text">
                    <h4>{{ $notification->data['title'] ?? 'Church Notification' }}</h4>
                    <p>
                        {{ $notification->data['message'] ?? 'You have a new notification.' }}
                    </p>
                    <p>
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>

            </div>

        @empty

            <div class="dashboard-empty-state">
                <span><i class="far fa-bell"></i></span>
                <h2>No new notifications</h2>
                <p>You’re all caught up.</p>
            </div>

        @endforelse

    </div>

</div>

<!-- DUTY SCHEDULE -->
<div class="content-box" style="margin-bottom:25px;">

    <div class="box-heading">
        <h3><i class="fas fa-tasks"></i> Upcoming Duty Schedule</h3>
        <a href="{{ route('dashboard.resource.index', 'duties') }}" class="view-all">
            View All
        </a>
    </div>

    <div class="table-responsive">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Duty</th>
                    <th>Assigned To</th>
                    <th>Date</th>
                    <th>Department</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                @forelse(($duties ?? collect()) as $duty)

                    <tr>
                        <td>{{ $duty->title }}</td>
                        <td>
                            {{ $duty->assignedUser->name ?? 'Not assigned' }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($duty->duty_date)->format('d M Y') }}
                        </td>
                        <td>
                            {{ $duty->department->name ?? 'General' }}
                        </td>
                        <td>
                            <span class="status-badge status-pending">
                                {{ ucfirst($duty->status ?? 'Pending') }}
                            </span>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="5" style="text-align:center;">
                            No upcoming duties available.
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
        <h3><i class="fas fa-bolt"></i> Quick Access</h3>
    </div>

    <div class="dashboard-cards">

        <a href="{{ route('dashboard.resource.index', 'events') }}" class="dashboard-card quick-access-card">
            <div class="card-icon icon-blue"><i class="fas fa-calendar-days"></i></div>
            <h2>Activities</h2>
            <p>Plan and manage church activities.</p>
        </a>

        <a href="{{ route('dashboard.resource.index', 'announcements') }}" class="dashboard-card quick-access-card">
            <div class="card-icon icon-gold"><i class="fas fa-bullhorn"></i></div>
            <h2>Announcements</h2>
            <p>Share updates with the church family.</p>
        </a>

        <a href="{{ route('dashboard.resource.index', 'duties') }}" class="dashboard-card quick-access-card">
            <div class="card-icon icon-purple"><i class="fas fa-clipboard-list"></i></div>
            <h2>Duties</h2>
            <p>Coordinate upcoming duty assignments.</p>
        </a>

        <a href="{{ route('dashboard.resource.index', 'reports') }}" class="dashboard-card quick-access-card">
            <div class="card-icon icon-blue">
                <i class="fas fa-file-alt"></i>
            </div>
            <h2>Reports</h2>
            <p>Submit and review church reports.</p>
        </a>

        <a href="{{ route('dashboard.resource.index', 'meetings') }}" class="dashboard-card quick-access-card">
            <div class="card-icon icon-gold">
                <i class="fas fa-users"></i>
            </div>
            <h2>Meetings</h2>
            <p>Manage church meetings and minutes.</p>
        </a>

        <a href="{{ route('dashboard.resource.index', 'departments') }}" class="dashboard-card quick-access-card">
            <div class="card-icon icon-purple">
                <i class="fas fa-sitemap"></i>
            </div>
            <h2>Departments</h2>
            <p>View church departments and leaders.</p>
        </a>

        @if(auth()->user()->email === config('admin.email'))
            <a href="{{ route('dashboard.resource.index', 'finance') }}" class="dashboard-card quick-access-card">
                <div class="card-icon icon-green">
                    <i class="fas fa-wallet"></i>
                </div>
                <h2>Finance</h2>
                <p>View authorized financial records.</p>
            </a>
        @endif

    </div>

</div>

@endsection
