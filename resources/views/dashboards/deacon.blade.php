@extends('layouts.dashboard')

@section('title', __('Deacon Dashboard').' | PHM HCC')
@section('page-heading', __('Deacon Services'))

@section('content')
<header class="role-hero">
    <div class="role-hero-content">
        <span class="role-eyebrow">{{ __('Service portal') }}</span>
        <h1>{{ __('Deacon Dashboard') }}</h1>
        <p>{{ __('Support worship, care for the congregation, and help church services run smoothly.') }}</p>
    </div>
    <div class="role-hero-icon"><i class="fas fa-hands-holding-circle" aria-hidden="true"></i></div>
</header>
<h2 class="role-section-title">{{ __('Areas of service') }}</h2>
<section class="role-cards" aria-label="{{ __('Deacon responsibilities') }}">
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-hand-holding-heart" aria-hidden="true"></i></div><h2>{{ __('Congregational care') }}</h2><p>{{ __('Coordinate practical support for members and families.') }}</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-church" aria-hidden="true"></i></div><h2>{{ __('Worship support') }}</h2><p>{{ __('Help prepare the church and support orderly services.') }}</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-clipboard-check" aria-hidden="true"></i></div><h2>{{ __('Service planning') }}</h2><p>{{ __('Coordinate assigned duties with ministry leaders.') }}</p></article>
</section>
<section class="content-box">
    <h2 class="role-section-title">{{ __('Quick actions') }}</h2>
    <div class="role-tools">
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'duties') }}"><span><i class="fas fa-list-check"></i> {{ __('Duty Schedule') }}</span><i class="fas fa-arrow-right"></i></a>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'meetings') }}"><span><i class="fas fa-users"></i> {{ __('Church meetings') }}</span><i class="fas fa-arrow-right"></i></a>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'events') }}"><span><i class="fas fa-calendar-week"></i> {{ __('Church events') }}</span><i class="fas fa-arrow-right"></i></a>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'announcements') }}"><span><i class="fas fa-bullhorn"></i> {{ __('Church Announcements') }}</span><i class="fas fa-arrow-right"></i></a>
    </div>
 </section>
@endsection
