@extends('layouts.dashboard')

@section('title', 'Department Leader Dashboard | PHM HCC')
@section('page-heading', 'Department Leadership')

@section('content')
<header class="role-hero">
    <div class="role-hero-content">
        <span class="role-eyebrow">Team leadership portal</span>
        <h1>Department Leader Dashboard</h1>
        <p>Plan ministry activities, coordinate your team, and keep church leadership informed.</p>
    </div>
    <div class="role-hero-icon"><i class="fas fa-people-group" aria-hidden="true"></i></div>
</header>
<h2 class="role-section-title">Leadership responsibilities</h2>
<section class="role-cards" aria-label="Department leader responsibilities">
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-people-group" aria-hidden="true"></i></div><h2>Team coordination</h2><p>Organize your ministry team and share clear responsibilities.</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-calendar-days" aria-hidden="true"></i></div><h2>Ministry schedule</h2><p>Plan activities and coordinate dates with church leadership.</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-chart-line" aria-hidden="true"></i></div><h2>Ministry updates</h2><p>Share progress, needs, and outcomes with church leaders.</p></article>
</section>
<section class="content-box">
    <h2 class="role-section-title">Quick actions</h2>
    <div class="role-tools">
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'departments') }}"><span><i class="fas fa-sitemap"></i> Departments</span><i class="fas fa-arrow-right"></i></a>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'duties') }}"><span><i class="fas fa-list-check"></i> Schedule duties</span><i class="fas fa-arrow-right"></i></a>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'reports') }}"><span><i class="fas fa-file-lines"></i> Submit report</span><i class="fas fa-arrow-right"></i></a>
    </div>
 </section>
@endsection
