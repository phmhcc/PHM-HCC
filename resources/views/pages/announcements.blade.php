@extends('layouts.app')

@section('title', 'Announcements | PHM HCC')

@push('styles')
<style>
    .announcements-banner { padding: 75px 20px; text-align: center; color: #fff; background: linear-gradient(rgba(0, 0, 0, .58), rgba(0, 0, 0, .58)), url("{{ asset('Images/church-background.jpg') }}") center / cover no-repeat; }
    .announcements-banner h1 { font-size: 42px; }
    .announcements-section { max-width: 900px; margin: 0 auto; padding: 60px 24px; }
    .announcement-card { display: flex; gap: 20px; align-items: flex-start; margin-bottom: 20px; padding: 25px; border-left: 5px solid #d4b200; border-radius: 8px; background: #fff; box-shadow: 0 5px 18px rgba(0, 0, 0, .08); }
    .announcement-card i { margin-top: 4px; color: #003366; font-size: 22px; }
    .announcement-card h2 { margin-bottom: 8px; color: #003366; font-size: 20px; }
    .announcement-card p { color: #555; line-height: 1.7; }
    @media (max-width: 600px) { .announcements-banner h1 { font-size: 34px; } .announcement-card { padding: 20px; } }
</style>
@endpush

@section('content')
<section class="announcements-banner">
    <h1>Church Announcements</h1>
    <p>Updates and information for our church family</p>
</section>

<section class="announcements-section" aria-label="Announcements">
    <article class="announcement-card">
        <i class="fas fa-bullhorn" aria-hidden="true"></i>
        <div>
            <h2>Stay connected</h2>
            <p>Church announcements will be shared here. Please contact the church through our <a href="{{ route('contact') }}">contact page</a> for the latest information.</p>
        </div>
    </article>
    <article class="announcement-card">
        <i class="fas fa-calendar-days" aria-hidden="true"></i>
        <div>
            <h2>Regular services</h2>
            <p>For our regular worship, Bible study, and prayer schedule, visit the <a href="{{ route('home') }}">home page</a>.</p>
        </div>
    </article>
</section>
@endsection
