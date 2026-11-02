<!-- @extends('layouts.app')

@section('title', 'Events | PHM HCC')

@push('styles')
<style>
    .events-banner { padding: 75px 20px; text-align: center; color: #fff; background: linear-gradient(rgba(0, 0, 0, .58), rgba(0, 0, 0, .58)), url("{{ asset('Images/church-background.jpg') }}") center / cover no-repeat; }
    .events-banner h1 { font-size: 42px; }
    .events-section { max-width: 900px; margin: 0 auto; padding: 60px 24px; }
    .event-card { display: flex; gap: 20px; align-items: flex-start; margin-bottom: 20px; padding: 25px; border-radius: 8px; background: #fff; box-shadow: 0 5px 18px rgba(0, 0, 0, .08); }
    .event-card i { margin-top: 4px; color: #c9a900; font-size: 24px; }
    .event-card h2 { margin-bottom: 8px; color: #003366; font-size: 20px; }
    .event-card p { color: #555; line-height: 1.7; }
    .event-date { margin-bottom: 8px; color: #778397; font-size: 13px; }
    .events-empty { padding: 28px; border-radius: 8px; background: #fff; color: #555; text-align: center; box-shadow: 0 5px 18px rgba(0, 0, 0, .08); }
    .events-contact { margin-top: 28px; text-align: center; }
    @media (max-width: 600px) { .events-banner h1 { font-size: 34px; } .event-card { padding: 20px; } }
</style>
@endpush

@section('content')
<section class="events-banner">
    <h1>Church Events</h1>
    <p>Gathering together to worship, grow, and serve</p>
</section>

<section class="events-section" aria-label="Church events">
    @forelse($events as $event)
        <article class="event-card">
            <i class="fas fa-calendar-days" aria-hidden="true"></i>
            <div>
                <h2>{{ $event->title }}</h2>
                <p class="event-date">
                    {{ $event->start_date->format('F j, Y \a\t g:i A') }}
                    @if($event->venue) · {{ $event->venue }} @endif
                    · {{ str($event->status)->title() }}
                </p>
                @if($event->description)
                    <p>{{ $event->description }}</p>
                @endif
            </div>
        </article>
    @empty
        <div class="events-empty">
            <i class="fas fa-calendar-days" aria-hidden="true"></i>
            <p>There are no events at this time. Please check back soon.</p>
        </div>
    @endforelse
    @if($events->hasPages())
        <div class="announcement-pagination">{{ $events->links() }}</div>
    @endif
    <article class="event-card">
        <i class="fas fa-church" aria-hidden="true"></i>
        <div>
            <h2>Weekly gatherings</h2>
            <p>Join us for Sunday worship, Tuesday Bible study, and Friday prayer. Service times are listed on the <a href="{{ route('home') }}">home page</a>.</p>
        </div>
    </article>
    <p class="events-contact">Have a question? <a href="{{ route('contact') }}">Contact us</a>.</p>
</section>
@endsection -->
