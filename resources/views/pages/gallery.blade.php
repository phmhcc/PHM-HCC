@extends('layouts.app')
@section('title', 'Gallery | PHM HCC')
@push('styles')
<style>
    .page-header {
        background: #003366;
        color: #fff;
        text-align: center;
        padding: 80px 20px;
        background:
            linear-gradient(rgba(186, 129, 129, 0.6), rgba(0, 0, 0, 0.60)),
            url("./Images/church-background.jpg") center center / cover no-repeat;

    }

    .page-header h1 {
        font-size: 42px;
    }

    .page-header p {
        margin-top: 10px;
        font-size: 18px;
    }

    .gallery {
        padding: 70px 8%;
        background: #f8f9fa;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }

    .gallery-item {
        overflow: hidden;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
        background: #fff;
        transition: .3s;
    }

    .gallery-item:hover {
        transform: translateY(-8px);
    }

    .gallery-item img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        transition: .4s;
    }

    .gallery-item:hover img {
        transform: scale(1.08);
    }

    .caption {
        padding: 18px;
        text-align: center;
    }

    .caption h3 {
        color: #003366;
        margin-bottom: 8px;
    }

    .caption p {
        color: #666;
    }
</style>
@endpush

@section('content')
<section class="page-header">

    <h1>Church Gallery</h1>

    <p>
        Moments of Worship, Fellowship, and Ministry
    </p>

</section>

<section class="gallery">

    <div class="gallery-grid">

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="Sunday Worship">

            <div class="caption">
                <h3>Sunday Worship</h3>
                <p>Worship service with the congregation.</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="Choir">

            <div class="caption">
                <h3>Choir Ministry</h3>
                <p>Praise and worship through music.</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="Youth">

            <div class="caption">
                <h3>Youth Fellowship</h3>
                <p>Young people growing in Christ.</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="Prayer">

            <div class="caption">
                <h3>Prayer Meeting</h3>
                <p>Seeking God's presence together.</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="Children">

            <div class="caption">
                <h3>Children's Ministry</h3>
                <p>Teaching children God's Word.</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="Outreach">

            <div class="caption">
                <h3>Community Outreach</h3>
                <p>Serving and supporting our community.</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="Conference">

            <div class="caption">
                <h3>Annual Conference</h3>
                <p>Special conference and revival meetings.</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="Baptism">

            <div class="caption">
                <h3>Baptism Service</h3>
                <p>Celebrating new life in Christ.</p>
            </div>
        </div>
    </div>
</section>
@endsection