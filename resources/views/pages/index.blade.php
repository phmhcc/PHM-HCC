@extends('layouts.app')

@section('title', __('Home').' | PHM HCC')

@push('styles')
<style>
    .hero {
        padding: 100px 8%;
        text-align: center;
        background: linear-gradient(rgba(186, 129, 129, 0.6), rgba(0, 0, 0, 0.60)),
        url("{{ asset('Images/church-background.jpg') }}") center center / cover no-repeat;

    }

    .hero h1 {
        font-size: 48px;
        color: white;
        margin-bottom: 20px;
    }

    .hero p {
        font-size: 20px;
        color: white;
        margin-bottom: 30px;
    }

    .home-video {
        width: min(100% - 32px, 1100px);
        margin: 56px auto;
    }

    .home-video h2 {
        margin-bottom: 18px;
        color: #003366;
        text-align: center;
    }

    .home-video-items {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr));
        gap: 20px;
    }

    .home-video-item {
        display: flex;
        min-width: 0;
        flex-direction: column;
    }

    .home-video-card {
        flex: 1;
        overflow: hidden;
        border: 1px solid #e8edf4;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
    }

    .home-video-thumbnail {
        position: relative;
        display: block;
        overflow: hidden;
        aspect-ratio: 16 / 9;
        background: #111;
    }

    .home-video-thumbnail img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .home-video-play {
        position: absolute;
        top: 50%;
        left: 50%;
        display: grid;
        width: 60px;
        height: 60px;
        place-items: center;
        transform: translate(-50%, -50%);
        border: 2px solid #fff;
        border-radius: 50%;
        background: rgba(0, 0, 0, .5);
        color: #fff;
        font-size: 22px;
    }

    .home-video-details {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
    }

    .home-video-details h3 {
        margin-bottom: 5px;
        color: #17324d;
        font-size: 17px;
    }

    .home-video-details time {
        color: #697789;
        font-size: 13px;
    }

    .home-video-provider {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #697789;
        font-size: 12px;
    }

    .home-video-provider i {
        color: #168de2;
    }

    .home-video-action {
        display: table;
        margin: 20px auto 0;
        padding: 10px 24px;
        border-radius: 7px;
        background: #003366;
        color: #fff;
        font-weight: 600;
        text-decoration: none;
    }

    .home-video-action:hover {
        background: #c9a900;
        color: #003366;
    }

    @media (max-width: 600px) {
        .home-video {
            margin: 36px auto;
        }

        .home-video-details {
            padding: 13px;
        }

        .home-video-details h3 {
            font-size: 15px;
        }
    }
</style>
@endpush

@section('content')

<section class="hero">
    <div class="hero-content">
        <h1>PENTECOSTAL HOLINESS MISSION</h1>
        <h2>HOPE CHRISTIAN CHURCH</h2>
        <p>{{ __('Winning Souls • Building Faith • Serving God') }}</p>
        <a href="{{ route('about') }}" class="btn">{{ __('Learn More') }}</a>
    </div>
</section>

<!-- Welcome -->

<section class="welcome">
    <h2>{{ __('Welcome to PHM HCC') }}</h2>
    <p>
        {{ __('We warmly welcome you to Pentecostal Holiness Mission Hope Christian Church. Join us as we worship God, grow spiritually, and impact our community through love, prayer, and the Word of God.') }}
    </p>

    @if($videos->isNotEmpty())
    <section class="home-video" aria-labelledby="home-video-heading">
        <h2 id="home-video-heading">{{ __('Watch our videos') }}</h2>
        <div class="home-video-items">
            @foreach($videos as $video)
            <div class="home-video-item">
                <article class="home-video-card">
                    <a class="home-video-thumbnail" href="{{ $video['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Watch :title', ['title' => $video['title']]) }}">
                        <img src="{{ $video['thumbnail'] }}" alt="{{ $video['title'] }}" loading="lazy">
                        <span class="home-video-play" aria-hidden="true"><i class="fas fa-play"></i></span>
                    </a>
                    <div class="home-video-details">
                        <div>
                            <h3>{{ $video['title'] }}</h3>
                            @if($video['date'])
                            <time datetime="{{ $video['date'] }}">{{ \Illuminate\Support\Carbon::parse($video['date'])->locale(app()->getLocale())->translatedFormat('F j, Y') }}</time>
                            @endif
                        </div>
                        <span class="home-video-provider">
                            <i class="fab {{ $video['provider'] === 'Vimeo' ? 'fa-vimeo-v' : ($video['provider'] === 'YouTube' ? 'fa-youtube' : 'fa-video') }}" aria-hidden="true"></i>
                            {{ $video['provider'] }}
                        </span>
                    </div>
                </article>
            </div>
            @endforeach
        </div>
        @if($youtubeChannelUrl)
        <a class="home-video-action" href="{{ $youtubeChannelUrl }}" target="_blank" rel="noopener noreferrer">{{ __('View all videos') }}</a>
        @endif
    </section>
    @endif
</section>

<!-- Service Times -->

<section class="services">
    <h2>{{ __('Service Schedule') }}</h2>
    <div class="service-box">

        <div class="card">
            <i class="fa-solid fa-church"></i>
            <h3>{{ __('Sunday Worship') }}</h3>
            <p>{{ __('06:30 AM - 09:30 AM - English Service') }}</p>
            <p>{{ __('09:00 AM - 01:00 PM - Swahili Service') }}</p>
        </div>

        <div class="card">
            <i class="fa-solid fa-book-bible"></i>
            <h3>{{ __('Tuesday Service (Bible Study)') }}</h3>
            <p>06:30 PM - 08:00 PM</p>
        </div>

        <div class="card">
            <i class="fa-solid fa-hands-praying"></i>
            <h3>{{ __('Friday Prayer') }}</h3>
            <p>06:30 PM - 08:00 PM</p>
        </div>
    </div>
</section>

<!-- Pastor -->

<section class="pastor">
    <div class="pastor-image">
        <img src="{{ asset('Images/pastor.jpg') }}" alt="{{ __('Pastor') }}">
    </div>
    <div class="pastor-text">
        <h2>{{ __('Message from the Pastor') }}</h2>
        <p>
            {{ __('We believe that Jesus Christ transforms lives. Our prayer is that every visitor experiences God\'s love, hope, and salvation through our ministry.') }}
        </p>
        <a href="{{ route('contact') }}" class="btn">{{ __('Contact us') }}</a>
    </div>
</section>

<!-- Bible Verse -->
<!-- 
    <section class="verse">
        <h2>{{ __('Weekly Bible Verse') }}</h2>
        <blockquote>
            {{ __('For where two or three gather in my name, there am I with them.') }}

            <br><br>
            <strong>{{ __('Matthew 18:20') }}</strong>
        </blockquote>
    </section> -->

<!-- Announcements -->

<!-- <section class="announcement">

        <h2>Latest Announcements</h2>

        <div class="marquee-container">
            <div class="marquee-content">
                @forelse($announcements as $announcement)
                    <div class="news-card">
                        <h3>{{ $announcement->title }}</h3>
                        <p>{{ $announcement->message }}</p>
                    </div>
                @empty
                    <div class="news-card">
                        <h3>No announcements yet</h3>
                        <p>Please check back soon for church updates.</p>
                    </div>
                @endforelse
                @foreach($announcements as $announcement)
                    <div class="news-card" aria-hidden="true">
                        <h3>{{ $announcement->title }}</h3>
                        <p>{{ $announcement->message }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section> -->

@endsection