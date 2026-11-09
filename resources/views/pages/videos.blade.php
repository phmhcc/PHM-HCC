@extends('layouts.app')

@section('title', __('Videos').' | PHM HCC')

@push('styles')
<style>
    .videos-page {
        width: min(100% - 32px, 1100px);
        margin: 0 auto;
        padding: clamp(32px, 7vw, 60px) 0;
    }

    .videos-page h1 {
        margin-bottom: 24px;
        color: #003366;
        text-align: center;
    }

    .videos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr));
        gap: 20px;
    }

    .videos-card {
        overflow: hidden;
        border: 1px solid #e8edf4;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
    }

    .videos-thumbnail {
        position: relative;
        display: block;
        overflow: hidden;
        aspect-ratio: 16 / 9;
        background: #111;
    }

    .videos-thumbnail img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .videos-play {
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

    .videos-details {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
    }

    .videos-details h2 {
        margin-bottom: 5px;
        color: #17324d;
        font-size: 17px;
    }

    .videos-details time,
    .videos-provider {
        color: #697789;
        font-size: 13px;
    }

    .videos-empty {
        padding: 24px;
        border-radius: 12px;
        background: #fff;
        color: #697789;
        text-align: center;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
    }

    @media (max-width: 600px) {
        .videos-details {
            padding: 13px;
        }

        .videos-details h2 {
            font-size: 15px;
        }
    }
</style>
@endpush

@section('content')
<section class="videos-page">
    <h1>{{ __('Videos') }}</h1>
    @if($videos->isNotEmpty())
    <div class="videos-grid">
        @foreach($videos as $video)
        <article class="videos-card">
            <a class="videos-thumbnail" href="{{ $video['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Watch :title', ['title' => $video['title']]) }}">
                <img src="{{ $video['thumbnail'] }}" alt="{{ $video['title'] }}" loading="lazy">
                <span class="videos-play" aria-hidden="true"><i class="fas fa-play"></i></span>
            </a>
            <div class="videos-details">
                <div>
                    <h2>{{ $video['title'] }}</h2>
                    @if($video['date'])
                    <time datetime="{{ $video['date'] }}">{{ \Illuminate\Support\Carbon::parse($video['date'])->locale(app()->getLocale())->translatedFormat('F j, Y') }}</time>
                    @endif
                </div>
                <span class="videos-provider">{{ $video['provider'] }}</span>
            </div>
        </article>
        @endforeach
    </div>
    @else
    <p class="videos-empty">{{ __('No videos are available right now.') }}</p>
    @endif
</section>
@endsection
