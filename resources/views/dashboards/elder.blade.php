@extends('layouts.dashboard')

@section('title', 'Elder Dashboard | PHM HCC')
@section('page-heading', 'Elder Ministry')

@section('content')
<header class="role-hero">
    <div class="role-hero-content">
        <span class="role-eyebrow">Ministry portal</span>
        <h1>Elder Dashboard</h1>
        <p>Encourage spiritual growth and provide faithful support across the church.</p>
    </div>
    <div class="role-hero-icon"><i class="fas fa-book-bible" aria-hidden="true"></i></div>
</header>
<h2 class="role-section-title">Areas of ministry</h2>
<section class="role-cards" aria-label="Elder responsibilities">
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-hands-praying" aria-hidden="true"></i></div><h2>Prayer and care</h2><p>Support prayer, pastoral care, and the spiritual wellbeing of members.</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-book-bible" aria-hidden="true"></i></div><h2>Spiritual guidance</h2><p>Encourage biblical teaching and spiritual growth in the congregation.</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-people-group" aria-hidden="true"></i></div><h2>Leadership support</h2><p>Work alongside church leaders to strengthen ministry teams.</p></article>
</section>
<section class="content-box">
    <h2 class="role-section-title">Quick actions</h2>
    <div class="role-tools">
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'departments') }}"><span><i class="fas fa-sitemap"></i> Ministry departments</span><i class="fas fa-arrow-right"></i></a>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'reports') }}"><span><i class="fas fa-file-lines"></i> Ministry reports</span><i class="fas fa-arrow-right"></i></a>
    </div>
 </section>
@endsection
