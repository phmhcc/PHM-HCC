@extends('layouts.dashboard')

@section('title', 'Pastor Dashboard | PHM HCC')
@section('page-heading', 'Pastoral Leadership')

@section('content')
<header class="role-hero">
    <div class="role-hero-content">
        <span class="role-eyebrow">Leadership portal</span>
        <h1>Pastor Dashboard</h1>
        <p>Lead with care, strengthen the church family, and keep ministries moving together.</p>
    </section>
    <div class="role-hero-icon"><i class="fas fa-cross" aria-hidden="true"></i></div>
</header>
<h2 class="role-section-title">Leadership priorities</h2>
<section class="role-cards" aria-label="Pastor responsibilities">
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-people-group" aria-hidden="true"></i></div><h2>Congregational care</h2><p>Coordinate pastoral care and support for members and families.</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-calendar-check" aria-hidden="true"></i></div><h2>Church activities</h2><p>Keep worship services and ministry activities organized.</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-bullhorn" aria-hidden="true"></i></div><h2>Church communication</h2><p>Share timely guidance and updates with the church family.</p></article>
</section>
<section class="content-box">
    <h2 class="role-section-title">Quick actions</h2>
    <div class="role-tools">
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'events') }}"><span><i class="fas fa-calendar-days"></i> Manage activities</span><i class="fas fa-arrow-right"></i></a>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'announcements') }}"><span><i class="fas fa-bullhorn"></i> Publish announcement</span><i class="fas fa-arrow-right"></i></a>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'reports') }}"><span><i class="fas fa-file-lines"></i> Review reports</span><i class="fas fa-arrow-right"></i></a>
    </div>
</div>
@endsection
