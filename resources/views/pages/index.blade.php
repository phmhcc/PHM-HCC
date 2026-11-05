@extends('layouts.app')

@section('title', __('Home').' | PHM HCC')

@push('styles')
<style>
    .hero {
        padding: 100px 8%;
        text-align: center;
        background:
         linear-gradient( rgba(186, 129, 129, 0.6), rgba(0, 0, 0, 0.60)),
            url("{{ asset('Images/church-background.jpg') }}")
            center center / cover no-repeat;
         
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
