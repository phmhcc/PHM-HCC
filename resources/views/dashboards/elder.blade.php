@extends('layouts.dashboard')

@section('title', __('Elder Dashboard').' | PHM HCC')
@section('page-heading', __('Elder Ministry'))

@section('content')
<header class="role-hero">
    <div class="role-hero-content">
        <span class="role-eyebrow">{{ __('Ministry portal') }}</span>
        <h1>{{ __('Elder Dashboard') }}</h1>
        <p>{{ __('Encourage spiritual growth and provide faithful support across the church.') }}</p>
    </div>
    <div class="role-hero-icon"><i class="fas fa-book-bible" aria-hidden="true"></i></div>
</header>
<h2 class="role-section-title">{{ __('Areas of ministry') }}</h2>
<section class="role-cards" aria-label="{{ __('Elder responsibilities') }}">
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-hands-praying" aria-hidden="true"></i></div><h2>{{ __('Prayer and care') }}</h2><p>{{ __('Support prayer, pastoral care, and the spiritual wellbeing of members.') }}</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-book-bible" aria-hidden="true"></i></div><h2>{{ __('Spiritual guidance') }}</h2><p>{{ __('Encourage biblical teaching and spiritual growth in the congregation.') }}</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-people-group" aria-hidden="true"></i></div><h2>{{ __('Leadership support') }}</h2><p>{{ __('Work alongside church leaders to strengthen ministry teams.') }}</p></article>
</section>
<section class="content-box">
    <h2 class="role-section-title">{{ __('Quick actions') }}</h2>
    <div class="role-tools">
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'departments') }}"><span><i class="fas fa-sitemap"></i> {{ __('Ministry departments') }}</span><i class="fas fa-arrow-right"></i></a>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'reports') }}"><span><i class="fas fa-file-lines"></i> {{ __('Ministry reports') }}</span><i class="fas fa-arrow-right"></i></a>

    </div>
 </section>
@endsection
