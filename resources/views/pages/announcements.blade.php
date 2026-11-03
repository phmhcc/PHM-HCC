@extends('layouts.app')

@section('title', 'Announcements | PHM HCC')

@push('styles')
<style>
    .announcements-banner {
        padding: clamp(52px, 8vw, 75px) 20px;
        text-align: center;
        color: #fff;
        background: linear-gradient(rgba(0, 0, 0, .58), rgba(0, 0, 0, .58)), url("{{ asset('Images/church-background.jpg') }}") center / cover no-repeat;
    }
    .announcements-banner h1 {
        font-size: clamp(30px, 5vw, 42px);
        line-height: 1.2;
        overflow-wrap: anywhere;
    }
    .announcements-banner p {
        margin-top: 10px;
        line-height: 1.6;
    }
    .announcements-section {
        width: min(100% - 32px, 900px);
        margin: 0 auto;
        padding: clamp(32px, 7vw, 60px) 0;
    }
    .announcement-card {
        display: flex;
        gap: 20px;
        align-items: flex-start;
        margin-bottom: 20px;
        padding: clamp(18px, 4vw, 25px);
        border-left: 5px solid #d4b200;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(0, 0, 0, .08);
    }
    .announcement-card > i {
        flex: 0 0 auto;
        margin-top: 4px;
        color: #003366;
        font-size: 22px;
    }
    .announcement-card > div,
    .announcement-info-card > div {
        min-width: 0;
    }
    .announcement-card h2 {
        margin-bottom: 8px;
        color: #003366;
        font-size: clamp(18px, 3vw, 20px);
        line-height: 1.4;
        overflow-wrap: anywhere;
    }
    .announcement-card p,
    .announcement-info-card p {
        color: #555;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }
    .announcement-date {
        margin-bottom: 8px;
        color: #778397 !important;
        font-size: 13px;
    }
    .announcement-empty {
        padding: clamp(20px, 5vw, 28px);
        border-radius: 8px;
        background: #fff;
        color: #555;
        text-align: center;
        box-shadow: 0 5px 18px rgba(0, 0, 0, .08);
    }
    .announcement-info-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-top: 28px;
        padding: clamp(18px, 4vw, 25px);
        border-radius: 8px;
        background: #f3f6fa;
    }
    .announcement-info-card h2 {
        margin-bottom: 6px;
        color: #003366;
        font-size: clamp(18px, 3vw, 20px);
        line-height: 1.4;
    }
    .announcement-contact-link {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 11px 18px;
        border-radius: 6px;
        background: #003366;
        color: #fff;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
    }
    .announcement-contact-link:hover { background: #145986; }
    .announcement-pagination {
        max-width: 100%;
        margin-top: 24px;
        overflow-x: auto;
    }
    .announcement-pagination nav { min-width: max-content; }
    body.announcements-page { display: flex; min-height: 100vh; flex-direction: column; }
    body.announcements-page main { flex: 1; min-width: 0; }
    body.announcements-page footer { flex-shrink: 0; }
    @media (max-width: 600px) {
        .announcement-card { gap: 14px; }
        .announcement-info-card { align-items: stretch; flex-direction: column; }
        .announcement-contact-link { width: 100%; }
    }
    @media (max-width: 360px) {
        .announcements-section { width: calc(100% - 24px); }
        .announcement-card { gap: 10px; padding: 16px 13px; }
        .announcement-card > i { font-size: 18px; }
    }
</style>
@endpush

@section('content')
<section class="announcements-banner">
    <h1>{{ __('Church Announcements') }}</h1>
    <p>{{ __('Updates and information for our church family') }}</p>
</section>

<section class="announcements-section" aria-label="Announcements">
    @forelse($announcements as $announcement)
        <article class="announcement-card">
            <i class="fas fa-bullhorn" aria-hidden="true"></i>
            <div>
                <h2>{{ $announcement->title }}</h2>
                @if($announcement->published_at)
                    <p class="announcement-date">
                        {{ __('Published') }} {{ $announcement->published_at->format('F j, Y') }}
                    </p>
                @endif
                <p>{{ $announcement->message }}</p>
            </div>
        </article>
    @empty
        <div class="announcement-empty">
            <i class="fas fa-bullhorn" aria-hidden="true"></i>
            <p>{{ __('There are no announcements at this time. Please check back soon.') }}</p>
        </div>
    @endforelse

    @if($announcements->hasPages())
        <div class="announcement-pagination">{{ $announcements->links() }}</div>
    @endif

    <article class="announcement-info-card">
        <div>
            <h2>{{ __('Need more information?') }}</h2>
            <p>{{ __('Contact the church team if you have a question or need help.') }}</p>
        </div>
        <a class="announcement-contact-link" href="{{ route('contact') }}">{{ __('Contact us') }}</a>
    </article>
</section>
@endsection
