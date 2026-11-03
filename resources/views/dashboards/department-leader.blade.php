@extends('layouts.dashboard')

@section('title', __('Department Leader Dashboard').' | PHM HCC')
@section('page-heading', __('Department Leadership'))

@section('content')
<header class="role-hero">
    <div class="role-hero-content">
        <span class="role-eyebrow">{{ __('Team leadership portal') }}</span>
        <h1>{{ __('Department Leader Dashboard') }}</h1>
        <p>{{ __('Plan ministry activities, coordinate your team, and keep church leadership informed.') }}</p>
    </div>
    <div class="role-hero-icon"><i class="fas fa-people-group" aria-hidden="true"></i></div>
</header>
<h2 class="role-section-title">{{ __('Leadership responsibilities') }}</h2>
<section class="role-cards" aria-label="{{ __('Department leader responsibilities') }}">
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-people-group" aria-hidden="true"></i></div><h2>{{ __('Team coordination') }}</h2><p>{{ __('Organize your ministry team and share clear responsibilities.') }}</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-calendar-days" aria-hidden="true"></i></div><h2>{{ __('Ministry schedule') }}</h2><p>{{ __('Plan activities and coordinate dates with church leadership.') }}</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-chart-line" aria-hidden="true"></i></div><h2>{{ __('Ministry updates') }}</h2><p>{{ __('Share progress, needs, and outcomes with church leaders.') }}</p></article>
</section>
<section class="content-box">
    <h2 class="role-section-title">{{ __('Quick actions') }}</h2>
    <div class="role-tools">
        <!-- <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'departments') }}"><span><i class="fas fa-sitemap"></i> Departments</span><i class="fas fa-arrow-right"></i></a> -->
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'duties') }}"><span><i class="fas fa-list-check"></i> {{ __('Schedule duties') }}</span><i class="fas fa-arrow-right"></i></a>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'meetings') }}"><span><i class="fas fa-calendar-days"></i> {{ __('Schedule meetings') }}</span><i class="fas fa-arrow-right"></i></a>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'announcements') }}"><span><i class="fas fa-bullhorn"></i> {{ __('Church Announcements') }}</span><i class="fas fa-arrow-right"></i></a>
    </div>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'reports') }}"><span><i class="fas fa-file-lines"></i> {{ __('Submit report') }}</span><i class="fas fa-arrow-right"></i></a>
    </div>
 </section>
@endsection
