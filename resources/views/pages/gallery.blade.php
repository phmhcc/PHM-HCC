@extends('layouts.app')

@section('title', __('Gallery | PHM HCC'))

@push('styles')
<style>
    body.gallery-page {
        background: #fffdf6;
        color: #242a30;
    }

    .gallery-hero {
        padding: 42px 24px 24px;
        background: #fff;
        text-align: center;
    }

    .gallery-hero::before {
        display: block;
        width: 60px;
        height: 2px;
        margin: 0 auto 18px;
        background: #edaa00;
        content: "";
    }

    .gallery-hero-copy {
        max-width: 700px;
        margin: 0 auto;
    }

    .gallery-hero-copy h1 {
        margin: 0 0 10px;
        color: #252b31;
        font-family: Georgia, 'Times New Roman', serif;
        font-size: clamp(30px, 4vw, 42px);
        font-weight: 400;
    }

    .gallery-hero-copy p {
        margin: 0;
        color: #77766e;
        font-size: 16px;
        line-height: 1.7;
    }

    .gallery-category-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        max-width: 1280px;
        margin: 0 auto;
        padding: 20px 28px 30px;
    }

    .gallery-ministry-card {
        min-width: 0;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 9px;
        background: #fff;
        box-shadow: 0 3px 8px rgba(15, 23, 42, .1);
    }

    .gallery-category-mosaic {
        display: grid;
        height: 164px;
        grid-template-columns: 1.7fr 1fr;
        gap: 2px;
        overflow: hidden;
        background: #f1f3f5;
    }

    .gallery-category-mosaic img {
        display: block;
        width: 100%;
        height: 100%;
        min-height: 0;
        object-fit: cover;
    }

    .gallery-category-mosaic img:first-child {
        grid-row: 1 / span 2;
    }

    .gallery-category-mosaic img:only-child {
        grid-row: 1;
        grid-column: 1 / -1;
    }

    .gallery-category-details {
        display: flex;
        min-height: 60px;
        align-items: center;
        gap: 11px;
        padding: 10px 12px;
    }

    .gallery-category-icon {
        display: grid;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        place-items: center;
        border-radius: 50%;
        background: #173c70;
        color: #fff;
    }

    .gallery-category-icon-gold {
        background: #dca313;
    }

    .gallery-category-copy {
        min-width: 0;
        flex: 1;
    }

    .gallery-category-copy h2 {
        margin: 0 0 3px;
        color: #202c38;
        font-size: 15px;
        font-weight: 600;
    }

    .gallery-category-copy p {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
    }

    .gallery-category-link {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        gap: 7px;
        padding: 8px 0 8px 8px;
        border: 0;
        background: transparent;
        color: #172d52;
        font: inherit;
        font-size: 12px;
        cursor: pointer;
    }

    .gallery-category-link:hover {
        color: #b47c00;
    }

    .gallery-all-button {
        display: flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 9px;
        margin: 0 auto 38px;
        padding: 0 24px;
        border: 1px solid #173c70;
        border-radius: 6px;
        background: #fff;
        color: #173c70;
        font: inherit;
        cursor: pointer;
    }

    .gallery-all-button:hover {
        background: #173c70;
        color: #fff;
    }

    .gallery-category-link:focus-visible,
    .gallery-all-button:focus-visible,
    .gallery-photo-tile:focus-visible {
        outline: 3px solid #3373ad;
        outline-offset: 3px;
    }

    .gallery-collection {
        padding: 34px max(5vw, calc((100vw - 1200px) / 2)) 80px;
        background: #fff;
    }

    .gallery-collection[hidden] {
        display: none;
    }

    .gallery-photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 8px;
    }

    .gallery-category-nav {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin: 0 0 30px;
    }

    .gallery-category-button {
        min-height: 42px;
        padding: 0 17px;
        border: 1px solid #d5d9dc;
        border-radius: 999px;
        background: #fff;
        color: #202c38;
        font: inherit;
        cursor: pointer;
    }

    .gallery-category-button[aria-pressed="true"],
    .gallery-category-button:hover {
        border-color: #202c38;
        background: #202c38;
        color: #fff;
    }

    .gallery-category-button:focus-visible,
    .gallery-download:focus-visible {
        outline: 3px solid #3373ad;
        outline-offset: 3px;
    }

    .gallery-image-card[hidden] {
        display: none;
    }

    .gallery-image-card {
        min-width: 0;
        background: #f7f7f5;
    }

    .gallery-photo-tile img {
        display: block;
        width: 100%;
        height: 280px;
        object-fit: cover;
        transition: opacity .25s ease;
        pointer-events: none;
    }

    .gallery-photo-tile:hover img {
        opacity: .88;
    }

    .gallery-photo-tile {
        display: block;
        width: 100%;
        overflow: hidden;
        padding: 0;
        border: 0;
        background: transparent;
        cursor: zoom-in;
    }

    .gallery-download {
        display: block;
        padding: 12px 14px;
        color: #202c38;
        font-size: 14px;
        text-align: center;
        text-decoration: none;
    }

    .gallery-download:hover {
        text-decoration: underline;
    }

    .gallery-empty {
        grid-column: 1 / -1;
        padding: 36px 20px;
        color: #77766e;
        text-align: center;
    }

    .gallery-lightbox-download {
        position: absolute;
        right: 0;
        bottom: -34px;
        left: 0;
        width: fit-content !important;
        height: auto !important;
        margin: auto;
        padding: 7px 15px !important;
        border-radius: 6px !important;
        font-size: 14px !important;
        text-decoration: none;
    }

    .gallery-lightbox {
        position: fixed;
        width: min(94vw, 1100px);
        max-width: none;
        max-height: 92vh;
        padding: 54px 70px 28px;
        overflow: visible;
        border: 0;
        border-radius: 12px;
        background: #17191c;
        color: #fff;
    }

    .gallery-lightbox[open] {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 20px;
    }

    .gallery-lightbox::backdrop {
        background: rgba(8, 10, 12, .88);
    }

    .gallery-lightbox img {
        display: block;
        width: 100%;
        max-height: calc(92vh - 112px);
        object-fit: contain;
    }

    .gallery-lightbox button {
        display: grid;
        width: 44px;
        height: 44px;
        place-items: center;
        border: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, .14);
        color: #fff;
        font: inherit;
        font-size: 24px;
        cursor: pointer;
    }

    .gallery-lightbox button:hover,
    .gallery-lightbox button:focus-visible {
        background: rgba(255, 255, 255, .28);
    }

    .gallery-lightbox-close {
        position: absolute;
        top: 10px;
        right: 12px;
    }

    .gallery-lightbox-count {
        position: absolute;
        right: 0;
        bottom: 10px;
        left: 0;
        color: #ddd;
        font-size: 13px;
        text-align: center;
    }

    @media (max-width: 760px) {
        .gallery-hero {
            padding: 34px 22px 18px;
        }

        .gallery-category-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            padding: 16px 18px 26px;
        }

        .gallery-collection {
            padding: 32px 22px 64px;
        }
    }

    @media (max-width: 420px) {
        .gallery-category-grid {
            grid-template-columns: 1fr;
            gap: 13px;
            padding-right: 14px;
            padding-left: 14px;
        }

        .gallery-category-mosaic {
            height: 176px;
        }

        .gallery-photo-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 5px;
        }

        .gallery-photo-tile img {
            height: 190px;
        }

        .gallery-lightbox {
            width: 100vw;
            max-height: 100vh;
            padding: 54px 12px 40px;
            border-radius: 0;
        }

        .gallery-lightbox[open] {
            gap: 8px;
        }

        .gallery-lightbox button {
            width: 38px;
            height: 38px;
        }

        .gallery-lightbox img {
            max-height: calc(100vh - 120px);
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .gallery-page *,
        .gallery-page *::before,
        .gallery-page *::after {
            scroll-behavior: auto !important;
            transition-duration: .01ms !important;
        }
    }
</style>
@endpush

@section('content')
<section class="gallery-hero" aria-labelledby="gallery-heading">
    <div class="gallery-hero-copy">
        <h1 id="gallery-heading">{{ __('Church Gallery') }}</h1>
        <p>{{ __('Moments of Worship, Fellowship, and Ministry') }}</p>
    </div>
</section>

<section class="gallery-category-grid" aria-label="{{ __('Gallery categories') }}">
    @foreach($categories as $category)
    <article class="gallery-ministry-card" data-category="{{ $category->slug }}" data-photo-count="{{ $category->images_count }}">
        <div class="gallery-category-mosaic" aria-hidden="true">
            @foreach($category->images->take(3) as $photo)
            <img src="{{ route('gallery.image', $photo) }}" alt="" loading="lazy">
            @endforeach
        </div>
        <div class="gallery-category-details">
            <span class="gallery-category-icon {{ $loop->iteration % 2 === 0 ? 'gallery-category-icon-gold' : '' }}" aria-hidden="true">
                <i class="fas {{ $category->icon }}"></i>
            </span>
            <div class="gallery-category-copy">
                <h2>{{ __($category->name) }}</h2>
                <p>{{ trans_choice(':count Photo|:count Photos', $category->images_count, ['count' => $category->images_count]) }}</p>
            </div>
            <button class="gallery-category-link" type="button" data-view-category="{{ $category->slug }}">
                {{ __('View Photos') }} <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </button>
        </div>
    </article>
    @endforeach
</section>

<button class="gallery-all-button" type="button" data-view-category="all">
    <i class="fas fa-border-all" aria-hidden="true"></i>
    {{ __('View All Photos') }}
</button>

<section class="gallery-collection" id="gallery-collection" hidden>
    <nav class="gallery-category-nav" aria-label="{{ __('Gallery categories') }}">
        <button class="gallery-category-button" type="button" data-gallery-category="all" data-photo-count="{{ count($galleryImages) }}" aria-pressed="true">
            {{ __('All photos') }} ({{ count($galleryImages) }})
        </button>
        @foreach($categories as $category)
        <button class="gallery-category-button" type="button" data-gallery-category="{{ $category->slug }}" data-photo-count="{{ $category->images_count }}" aria-pressed="false">
            {{ __($category->name) }} ({{ $category->images_count }})
        </button>
        @endforeach
    </nav>

    <div class="gallery-photo-grid">
        @foreach($galleryImages as $index => $image)
        <div class="gallery-image-card" data-gallery-card data-category="{{ $image->category?->slug ?? '' }}">
            <button
                type="button"
                class="gallery-photo-tile"
                data-gallery-index="{{ $index }}"
                aria-label="{{ __('Open church gallery photo :number', ['number' => $index + 1]) }}">
                <img src="{{ route('gallery.image', $image) }}" alt="{{ __('Church gallery photo :number', ['number' => $index + 1]) }}" loading="lazy">
            </button>
            <a class="gallery-download" href="{{ route('gallery.image', ['galleryImage' => $image, 'download' => 1]) }}">
                {{ __('Download image') }}
            </a>
        </div>
        @endforeach
    </div>
    <p class="gallery-empty" data-gallery-empty hidden>{{ __('No photos are available in this category yet.') }}</p>
</section>

<dialog class="gallery-lightbox" id="galleryLightbox" aria-label="{{ __('Church gallery photo viewer') }}">
    <button class="gallery-lightbox-close" type="button" aria-label="{{ __('Close photo') }}" data-gallery-close>&times;</button>
    <button type="button" aria-label="{{ __('Previous photo') }}" data-gallery-previous>&lsaquo;</button>
    <img id="galleryLightboxImage" src="" alt="">
    <button type="button" aria-label="{{ __('Next photo') }}" data-gallery-next>&rsaquo;</button>
    <a class="gallery-lightbox-download" id="galleryLightboxDownload" href="" download>{{ __('Download image') }}</a>
    <p class="gallery-lightbox-count" id="galleryLightboxCount" aria-live="polite"></p>
</dialog>

<script>
    (() => {
        const dialog = document.getElementById('galleryLightbox');
        const lightboxImage = document.getElementById('galleryLightboxImage');
        const lightboxDownload = document.getElementById('galleryLightboxDownload');
        const count = document.getElementById('galleryLightboxCount');
        const photos = Array.from(document.querySelectorAll('.gallery-photo-tile'));
        const categoryButtons = Array.from(document.querySelectorAll('[data-gallery-category]'));
        const galleryCollection = document.getElementById('gallery-collection');
        const imageSources = photos.map((photo) => photo.querySelector('img').src);
        let currentIndex = 0;
        let lastFocusedPhoto = null;

        const showPhoto = (index) => {
            currentIndex = (index + imageSources.length) % imageSources.length;
            lightboxImage.src = imageSources[currentIndex];
            lightboxImage.alt = photos[currentIndex].querySelector('img').alt;
            lightboxDownload.href = imageSources[currentIndex];
            lightboxDownload.download = imageSources[currentIndex].split('/').pop().split('?')[0];
            count.textContent = `${currentIndex + 1} / ${imageSources.length}`;
        };

        const openPhoto = (photo) => {
            lastFocusedPhoto = photo;
            showPhoto(Number(photo.dataset.galleryIndex));
            dialog.showModal();
        };

        photos.forEach((photo) => {
            photo.addEventListener('click', () => openPhoto(photo));
        });

        categoryButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const category = button.dataset.galleryCategory;
                let visibleCount = 0;

                categoryButtons.forEach((categoryButton) => {
                    categoryButton.setAttribute('aria-pressed', String(categoryButton === button));
                });

                document.querySelectorAll('[data-gallery-card]').forEach((card) => {
                    const isVisible = category === 'all' || card.dataset.category === category;
                    card.hidden = !isVisible;
                    visibleCount += Number(isVisible);
                });

                document.querySelector('[data-gallery-empty]').hidden = visibleCount > 0;
            });
        });

        document.querySelectorAll('[data-view-category]').forEach((button) => {
            button.addEventListener('click', () => {
                const category = button.dataset.viewCategory;
                const filter = categoryButtons.find((categoryButton) => categoryButton.dataset.galleryCategory === category);

                galleryCollection.hidden = false;
                filter.click();
                galleryCollection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });

        dialog.querySelector('[data-gallery-close]').addEventListener('click', () => dialog.close());
        dialog.querySelector('[data-gallery-previous]').addEventListener('click', () => showPhoto(currentIndex - 1));
        dialog.querySelector('[data-gallery-next]').addEventListener('click', () => showPhoto(currentIndex + 1));

        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
            }
        });

        dialog.addEventListener('close', () => lastFocusedPhoto?.focus());
        dialog.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowLeft') {
                showPhoto(currentIndex - 1);
            } else if (event.key === 'ArrowRight') {
                showPhoto(currentIndex + 1);
            }
        });

    })();
</script>

@endsection