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

    <h1>{{ __('Church Gallery') }}</h1>

    <p>
        {{ __('Moments of Worship, Fellowship, and Ministry') }}
    </p>

</section>

<section class="gallery">

    <div class="gallery-grid">

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="{{ __('Sunday Worship') }}">

            <div class="caption">
                <h3>{{ __('Sunday Worship') }}</h3>
                <p>{{ __('Worship service with the congregation.') }}</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="{{ __('Choir') }}">

            <div class="caption">
                <h3>{{ __('Choir Ministry') }}</h3>
                <p>{{ __('Praise and worship through music.') }}</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="{{ __('Youth') }}">

            <div class="caption">
                <h3>{{ __('Youth Fellowship') }}</h3>
                <p>{{ __('Young people growing in Christ.') }}</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="{{ __('Prayer') }}">

            <div class="caption">
                <h3>{{ __('Prayer Meeting') }}</h3>
                <p>{{ __('Seeking God\'s presence together.') }}</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="{{ __('Children') }}">

            <div class="caption">
                <h3>{{ __('Children\'s Ministry') }}</h3>
                <p>{{ __('Teaching children God\'s Word.') }}</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="{{ __('Outreach') }}">

            <div class="caption">
                <h3>{{ __('Community Outreach') }}</h3>
                <p>{{ __('Serving and supporting our community.') }}</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="{{ __('Conference') }}">

            <div class="caption">
                <h3>{{ __('Annual Conference') }}</h3>
                <p>{{ __('Special conference and revival meetings.') }}</p>
            </div>

        </div>

        <div class="gallery-item">

            <img src="{{ asset('Images/images.jpg') }}" alt="{{ __('Baptism') }}">

            <div class="caption">
                <h3>{{ __('Baptism Service') }}</h3>
                <p>{{ __('Celebrating new life in Christ.') }}</p>
            </div>
        </div>
    </div>
</section>
@endsection