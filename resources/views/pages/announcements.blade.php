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
    .announcement-date { margin-bottom: 8px; color: #778397; font-size: 13px; }
    .announcement-empty { padding: 28px; border-radius: 8px; background: #fff; color: #555; text-align: center; box-shadow: 0 5px 18px rgba(0, 0, 0, .08); }
    .announcement-info-card { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-top: 28px; padding: 25px; border-radius: 8px; background: #f3f6fa; }
    .announcement-info-card h2 { margin-bottom: 6px; color: #003366; font-size: 20px; }
    .announcement-info-card p { color: #555; line-height: 1.7; }
    .announcement-contact-link { flex: 0 0 auto; padding: 11px 18px; border-radius: 6px; background: #003366; color: #fff; font-weight: 600; }
    .announcement-contact-link:hover { background: #145986; }
    .announcement-pagination { margin-top: 24px; }
    body.announcements-page { display: flex; min-height: 100vh; flex-direction: column; }
    body.announcements-page main { flex: 1; }
    body.announcements-page footer { flex-shrink: 0; }
    @media (max-width: 600px) {
        .announcements-banner h1 { font-size: 34px; }
        .announcement-card { padding: 20px; }
        .announcement-info-card { align-items: flex-start; flex-direction: column; }
    }
</style>
@endpush

@section('content')
<section class="announcements-banner">
    <h1>Church Announcements</h1>
    <p>Updates and information for our church family</p>
</section>

<section class="announcements-section" aria-label="Announcements">
    @forelse($announcements as $announcement)
        <article class="announcement-card">
            <i class="fas fa-bullhorn" aria-hidden="true"></i>
            <div>
                <h2>{{ $announcement->title }}</h2>
                @if($announcement->published_at)
                    <p class="announcement-date">
                        Published {{ $announcement->published_at->format('F j, Y') }}
                    </p>
                @endif
                <p>{{ $announcement->message }}</p>
            </div>
        </article>
    @empty
        <div class="announcement-empty">
            <i class="fas fa-bullhorn" aria-hidden="true"></i>
            <p>There are no announcements at this time. Please check back soon.</p>
        </div>
    @endforelse

    @if($announcements->hasPages())
        <div class="announcement-pagination">{{ $announcements->links() }}</div>
    @endif

    <article class="announcement-info-card">
        <div>
            <h2>Need more information?</h2>
            <p>Contact the church team if you have a question or need help.</p>
        </div>
        <a class="announcement-contact-link" href="{{ route('contact') }}">Contact us</a>
    </article>
</section>
@endsection
