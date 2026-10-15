@extends('layouts.app')

@section('title', 'Home | PHM HCC')

@push('styles')
<style>
    .hero {
        padding: 100px 8%;
        text-align: center;
        background:
            linear-gradient(rgba(4, 4, 4, 0.6), rgba(85, 64, 64, 0.6)),
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
            <p>Winning Souls • Building Faith • Serving God</p>
            <a href="{{ route('about') }}" class="btn">Learn More</a>
        </div>
    </section>

    <!-- Welcome -->

    <section class="welcome">
        <h2>Welcome to PHM HCC</h2>
        <p>
            We warmly welcome you to Pentecostal Holiness Mission Hope Christian Church.
            Join us as we worship God, grow spiritually, and impact our community through
            love, prayer, and the Word of God.
        </p>
    </section>

    <!-- Service Times -->

    <section class="services">
        <h2>Service Schedule</h2>
        <div class="service-box">

            <div class="card">
                <i class="fa-solid fa-church"></i>
                <h3>Sunday Worship</h3>
                <p>06:30 AM - 09:30 AM -English Service</p>
                <p>09:00 AM - 01:00 PM - Swahili Service</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-book-bible"></i>
                <h3>Tuesday Service (Bible Study)</h3>
                <p>06:30 PM - 08:00 PM</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-hands-praying"></i>
                <h3>Friday Prayer</h3>
                <p>06:30 PM - 08:00 PM</p>
            </div>
        </div>
    </section>

    <!-- Pastor -->

    <section class="pastor">
        <div class="pastor-image">
            <img src="{{ asset('Images/pastor.jpg') }}" alt="Pastor">
        </div>
        <div class="pastor-text">
            <h2>Message from the Pastor</h2>
            <p>
                We believe that Jesus Christ transforms lives.
                Our prayer is that every visitor experiences God's love,
                hope, and salvation through our ministry.
            </p>
            <a href="{{ route('contact') }}" class="btn">Contact Us</a>
        </div>
    </section>

    <!-- Bible Verse -->

    <section class="verse">
        <h2>Weekly Bible Verse</h2>
        <blockquote>
            "For where two or three gather in my name,
            there am I with them."

            <br><br>
            <strong>Matthew 18:20</strong>
        </blockquote>
    </section>

    <!-- Announcements -->

    <section class="announcement">

        <h2>Latest Announcements</h2>

        <div class="marquee-container">
            <div class="marquee-content">

                <div class="news-card">
                    <h3>Youth Conference</h3>
                    <p>Wednesday at 04:30 PM.</p>
                </div>

                <div class="news-card">
                    <h3>Choir Practice</h3>
                    <p>Every Friday at 08:00 PM.</p>
                </div>

                <div class="news-card">
                    <h3>Praise and Worship Practice</h3>
                    <p>Every Thursday and Saturday at 08:00 PM - 10:00 PM.</p>
                </div>

                <div class="news-card">
                    <h3>Community Outreach</h3>
                    <p>Next Sunday after church service.</p>
                </div>

                <!-- Duplicate cards for smooth scrolling -->

                <div class="news-card">
                    <h3>Youth Conference</h3>
                    <p>Wednesday at 04:30 PM.</p>
                </div>

                <div class="news-card">
                    <h3>Choir Practice</h3>
                    <p>Every Friday at 08:00 PM.</p>
                </div>
 
                 <div class="news-card">
                    <h3>Praise and Worship Practice</h3>
                    <p>Every Thursday and Saturday at 08:00 PM - 10:00 PM.</p>
                </div>

                <div class="news-card">
                    <h3>Community Outreach</h3>
                    <p>Next Sunday after church service.</p>
                </div>

                <!-- Duplicate cards for smooth scrolling -->

                <div class="news-card">
                    <h3>Youth Conference</h3>
                    <p>Wednesday at 04:30 PM.</p>
                </div>

                <div class="news-card">
                    <h3>Choir Practice</h3>
                    <p>Every Friday at 08:00 PM.</p>
                </div>
 
                 <div class="news-card">
                    <h3>Praise and Worship Practice</h3>
                    <p>Every Thursday and Saturday at 08:00 PM - 10:00 PM.</p>
                </div>

                <div class="news-card">
                    <h3>Community Outreach</h3>
                    <p>Next Sunday after church service.</p>
                </div>

                <!-- Duplicate cards for smooth scrolling -->

                <div class="news-card">
                    <h3>Youth Conference</h3>
                    <p>Wednesday at 04:30 PM.</p>
                </div>

                <div class="news-card">
                    <h3>Choir Practice</h3>
                    <p>Every Friday at 08:00 PM.</p>
                </div>

                 <div class="news-card">
                    <h3>Praise and Worship Practice</h3>
                    <p>Every Thursday and Saturday at 08:00 PM - 10:00 PM.</p>
                </div>

                <div class="news-card">
                    <h3>Community Outreach</h3>
                    <p>Next Sunday after church service.</p>
                </div>
            </div>
        </div>
    </section>

@endsection
