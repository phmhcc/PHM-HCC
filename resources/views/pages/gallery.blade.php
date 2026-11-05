@extends('layouts.app')

@section('title', __('Gallery | PHM HCC'))

@push('styles')
<style>
    body.gallery-page {
        background: #fffdf6;
        color: #242a30;
    }

    .gallery-hero {
        display: grid;
        min-height: min(690px, calc(100svh - 68px));
        grid-template-columns: 1.05fr .95fr;
        align-items: center;
        gap: clamp(16px, 4vw, 60px);
        overflow: hidden;
        padding: 50px max(5vw, calc((100vw - 1200px) / 2)) 64px;
        background: linear-gradient(115deg, #fffdf7 8%, #fff8df 54%, #fff1ad 100%);
    }

    .gallery-photo-stack {
        position: relative;
        width: min(100%, 560px);
        height: 490px;
        justify-self: center;
        perspective: 1000px;
    }

    .gallery-photo {
        position: absolute;
        width: 68%;
        height: 62%;
        overflow: hidden;
        padding: 10px;
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 15px 38px rgba(37, 46, 56, .18);
        transition: transform .35s ease, box-shadow .35s ease;
        cursor: grab;
        touch-action: none;
        user-select: none;
    }

    .gallery-photo:active {
        cursor: grabbing;
    }

    .gallery-photo:focus-visible,
    .gallery-photo-tile:focus-visible {
        outline: 3px solid #3373ad;
        outline-offset: 3px;
    }

    .gallery-photo img {
        display: block;
        width: 100%;
        height: 100%;
        border-radius: 10px;
        object-fit: cover;
        pointer-events: none;
    }

    .gallery-photo-one {
        z-index: 4;
        top: 24%;
        left: 3%;
        transform: rotate(-5deg);
    }

    .gallery-photo-two {
        z-index: 3;
        top: 13%;
        left: 17%;
        transform: rotate(4deg);
    }

    .gallery-photo-three {
        z-index: 2;
        top: 6%;
        left: 29%;
        transform: rotate(-7deg);
    }

    .gallery-photo-four {
        z-index: 1;
        top: 25%;
        left: 34%;
        transform: rotate(8deg);
    }

    .gallery-photo-five {
        z-index: 0;
        top: 36%;
        left: 20%;
        transform: rotate(-2deg);
    }

    .gallery-photo:hover {
        box-shadow: 0 20px 44px rgba(37, 46, 56, .22);
    }

    .gallery-hero-copy {
        max-width: 500px;
        padding: 20px 0;
    }

    .gallery-hero-copy h1 {
        margin: 0 0 14px;
        color: #252b31;
        font-family: Georgia, 'Times New Roman', serif;
        font-size: clamp(34px, 4.1vw, 54px);
        font-weight: 400;
        line-height: 1.15;
    }

    .gallery-hero-copy p {
        margin-bottom: 27px;
        color: #77766e;
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 16px;
        font-style: italic;
        line-height: 1.7;
    }

    .gallery-view-button {
        display: inline-flex;
        min-height: 46px;
        align-items: center;
        justify-content: center;
        padding: 0 22px;
        border-radius: 7px;
        background: #202c38;
        color: #fff;
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 13px;
        text-decoration: none;
        transition: background .2s ease, transform .2s ease;
    }

    .gallery-view-button:hover {
        transform: translateY(-2px);
        background: #33485b;
    }

    .gallery-collection {
        padding: 88px max(5vw, calc((100vw - 1200px) / 2)) 100px;
        background: #fff;
    }

    .gallery-photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 8px;
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
        overflow: hidden;
        padding: 0;
        border: 0;
        background: transparent;
        cursor: zoom-in;
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
            min-height: auto;
            grid-template-columns: 1fr;
            gap: 2px;
            padding: 30px 22px 54px;
        }

        .gallery-photo-stack {
            width: min(100%, 480px);
            height: min(76vw, 390px);
            min-height: 270px;
        }

        .gallery-hero-copy {
            justify-self: center;
            padding: 8px 0 0;
            text-align: center;
        }

        .gallery-collection {
            padding: 64px 22px 76px;
        }
    }

    @media (max-width: 420px) {
        .gallery-photo {
            padding: 7px;
            border-radius: 12px;
        }

        .gallery-photo img {
            border-radius: 7px;
        }

        .gallery-photo-stack {
            min-height: 230px;
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
@php
    $galleryImages = [
        'FB_IMG_1784717227063.jpg',
        'FB_IMG_1784722994019.jpg',
        'FB_IMG_1784723023935.jpg',
        'FB_IMG_1784723083161.jpg',
        'FB_IMG_1784723120511.jpg',
        'FB_IMG_1784723251611.jpg',
        'FB_IMG_1788195392657.jpg',
        'FB_IMG_1791266130812.jpg',
        'FB_IMG_1793875573960.jpg',
        'FB_IMG_1793875628971.jpg',
        'FB_IMG_1793875637214.jpg',
        'FB_IMG_1793875648741.jpg',
        'FB_IMG_1793875662824.jpg',
        'FB_IMG_1793876003894.jpg',
        'FB_IMG_1793876013734.jpg',
        'FB_IMG_1793876024320.jpg',
        'FB_IMG_1793876039359.jpg',
        'FB_IMG_1793876048817.jpg',
        'FB_IMG_1793876071395.jpg',
        'FB_IMG_1793876082431.jpg',
        'FB_IMG_1793876091870.jpg',
        'FB_IMG_1793876100910.jpg',
        'FB_IMG_1793876109950.jpg',
        'FB_IMG_1793876118990.jpg',
        'FB_IMG_1793876128030.jpg',
        'FB_IMG_1793876137070.jpg',
        'FB_IMG_1793876146110.jpg',
        'FB_IMG_1793876155150.jpg',
        'FB_IMG_1793876164190.jpg',
        'FB_IMG_1793876173230.jpg',
        'FB_IMG_1793876182270.jpg',
        'FB_IMG_1793876191310.jpg',
        'FB_IMG_1793876200350.jpg',
        'FB_IMG_1793876209390.jpg',
        'FB_IMG_1793876218430.jpg',
        'FB_IMG_1793876227470.jpg',
        'FB_IMG_1793876236510.jpg',
        'FB_IMG_1793876245550.jpg',
        'FB_IMG_1793876254590.jpg',
        'FB_IMG_1793876263630.jpg',
        'FB_IMG_1793876272670.jpg',
        'FB_IMG_1793876281710.jpg',
        'FB_IMG_1793876290750.jpg',
        'FB_IMG_1793876299790.jpg',
        'FB_IMG_1793876308830.jpg',
        'FB_IMG_1793876317870.jpg',
        'FB_IMG_1793876326910.jpg',
        'FB_IMG_1793876335950.jpg',
        'FB_IMG_1793876344990.jpg',
        'FB_IMG_1793876354030.jpg',
        'FB_IMG_1793876363070.jpg',
        'FB_IMG_1793876372110.jpg',
        'FB_IMG_1793876381150.jpg',
        'FB_IMG_1793876390190.jpg',
        'FB_IMG_1793876399230.jpg',
        'FB_IMG_1793876408270.jpg',
        'FB_IMG_1793876417310.jpg',
        'FB_IMG_1793876426350.jpg',
        'FB_IMG_1793876435390.jpg',
        'FB_IMG_1793876444430.jpg',
        'FB_IMG_1793876453470.jpg',
        'FB_IMG_1793876462510.jpg',
        'FB_IMG_1793876471550.jpg',
        'FB_IMG_1793876480590.jpg',
        'FB_IMG_1793876489630.jpg',
        'FB_IMG_1793876498670.jpg',
        'FB_IMG_1793876507710.jpg',
        'FB_IMG_1793876516750.jpg',
        'FB_IMG_1793876525790.jpg',
        'FB_IMG_1793876534830.jpg',
        'FB_IMG_1793876543870.jpg',
        'FB_IMG_1793876552910.jpg',
        'FB_IMG_1793876561950.jpg',
        'FB_IMG_1793876570990.jpg',
        'FB_IMG_1793876580030.jpg',
        'FB_IMG_1793876589070.jpg',
        'FB_IMG_1793876598110.jpg',
        'FB_IMG_1793876607150.jpg',
        'FB_IMG_1793876616190.jpg',
        'FB_IMG_1793876625230.jpg',
        'FB_IMG_1793876634270.jpg',
        'FB_IMG_1793876643310.jpg', 
        'FB_IMG_1793876652350.jpg',
        'FB_IMG_1793876661390.jpg',
        'FB_IMG_1793876670430.jpg',
        'FB_IMG_1793876679470.jpg',
        'FB_IMG_1793876688510.jpg',
        'FB_IMG_1793876697550.jpg',
        'FB_IMG_1793876706590.jpg',
        'FB_IMG_1793876715630.jpg',
        'FB_IMG_1793876724670.jpg',
        'FB_IMG_1793876733710.jpg',
        'FB_IMG_1793876742750.jpg',
        'FB_IMG_1793876751790.jpg',
        'FB_IMG_1793876760830.jpg',
        'FB_IMG_1793876769870.jpg',
        'FB_IMG_1793876778910.jpg',
        'FB_IMG_1793876787950.jpg',
        'FB_IMG_1793876796990.jpg',
        'FB_IMG_1793876806030.jpg',
        'FB_IMG_1793876815070.jpg',
        'FB_IMG_1793876824110.jpg',
        'FB_IMG_1793876833150.jpg',
        'FB_IMG_1793876842190.jpg',
        'FB_IMG_1793876851230.jpg',
        'FB_IMG_1793876860270.jpg',
        'FB_IMG_1793876869310.jpg',
        'FB_IMG_1793876878350.jpg',
        'FB_IMG_1793876887390.jpg',
        'FB_IMG_1793876896430.jpg',
        'FB_IMG_1793876905470.jpg',
        'FB_IMG_1793876914510.jpg',
        'FB_IMG_1793876923550.jpg',
        'FB_IMG_1793876932590.jpg',
        'FB_IMG_1793876941630.jpg',
        'FB_IMG_1793876950670.jpg',
        'FB_IMG_1793876959710.jpg',
        'FB_IMG_1793876968750.jpg',
        'FB_IMG_1793876977790.jpg',
        'FB_IMG_1793876986830.jpg',
        'FB_IMG_1793876995870.jpg',
        'FB_IMG_1793877004910.jpg',
        'FB_IMG_1793877013950.jpg',
        'FB_IMG_1793877022990.jpg',
        'FB_IMG_1793877032030.jpg',
        'FB_IMG_1793877041070.jpg',
        'FB_IMG_1793877050110.jpg',
        'FB_IMG_1793877059150.jpg',
        'FB_IMG_1793877068190.jpg',
        'FB_IMG_1七年九三八七七零七七二三零.jpg',
    ];
    $galleryImages = array_values(array_filter(
        $galleryImages,
        static fn (string $image): bool => is_file(public_path('Images/'.$image)),
    ));
@endphp

<section class="gallery-hero" aria-labelledby="gallery-heading">
    <div class="gallery-photo-stack" aria-label="{{ __('Church gallery photos') }}">
        @foreach(array_slice($galleryImages, 0, 5) as $index => $image)
            <figure
                class="gallery-photo gallery-photo-{{ ['one', 'two', 'three', 'four', 'five'][$index] }}"
                data-gallery-index="{{ $index }}"
                role="button"
                tabindex="0"
                aria-label="{{ __('Open church gallery photo :number', ['number' => $index + 1]) }}">
                <img src="{{ asset('Images/'.$image) }}" alt="{{ __('Church gallery photo :number', ['number' => $index + 1]) }}">
            </figure>
        @endforeach
    </div>
    <div class="gallery-hero-copy">
        <h1 id="gallery-heading">{{ __('Capturing Life’s Beautiful Moments') }}</h1>
        <p>{{ __('Moments of Worship, Fellowship, and Ministry') }}</p>
        <a class="gallery-view-button" href="#leadership-gallery">{{ __('View Gallery') }}</a>
    </div>
</section>

<section class="gallery-collection" id="leadership-gallery">
    <div class="gallery-photo-grid">
        @foreach($galleryImages as $index => $image)
            <button
                type="button"
                class="gallery-photo-tile"
                data-gallery-index="{{ $index }}"
                aria-label="{{ __('Open church gallery photo :number', ['number' => $index + 1]) }}">
                <img src="{{ asset('Images/'.$image) }}" alt="{{ __('Church gallery photo :number', ['number' => $index + 1]) }}" loading="lazy">
            </button>
        @endforeach
    </div>
</section>

<dialog class="gallery-lightbox" id="galleryLightbox" aria-label="{{ __('Church gallery photo viewer') }}">
    <button class="gallery-lightbox-close" type="button" aria-label="{{ __('Close photo') }}" data-gallery-close>&times;</button>
    <button type="button" aria-label="{{ __('Previous photo') }}" data-gallery-previous>&lsaquo;</button>
    <img id="galleryLightboxImage" src="" alt="">
    <button type="button" aria-label="{{ __('Next photo') }}" data-gallery-next>&rsaquo;</button>
    <p class="gallery-lightbox-count" id="galleryLightboxCount" aria-live="polite"></p>
</dialog>

<script>
    (() => {
        const dialog = document.getElementById('galleryLightbox');
        const lightboxImage = document.getElementById('galleryLightboxImage');
        const count = document.getElementById('galleryLightboxCount');
        const photos = Array.from(document.querySelectorAll('[data-gallery-index]'));
        const imageSources = @json(array_map(fn (string $image): string => asset('Images/'.$image), $galleryImages));
        let currentIndex = 0;
        let lastFocusedPhoto = null;
        let nextStackZIndex = 5;

        const showPhoto = (index) => {
            currentIndex = (index + imageSources.length) % imageSources.length;
            lightboxImage.src = imageSources[currentIndex];
            lightboxImage.alt = photos.find((photo) => Number(photo.dataset.galleryIndex) === currentIndex)
                ?.querySelector('img')?.alt ?? '';
            count.textContent = `${currentIndex + 1} / ${imageSources.length}`;
        };

        const openPhoto = (photo) => {
            lastFocusedPhoto = photo;
            showPhoto(Number(photo.dataset.galleryIndex));
            dialog.showModal();
        };

        photos.forEach((photo) => {
            photo.addEventListener('click', () => openPhoto(photo));
            photo.addEventListener('keydown', (event) => {
                if (event.currentTarget.matches('figure') && (event.key === 'Enter' || event.key === ' ')) {
                    event.preventDefault();
                    openPhoto(photo);
                }
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

        document.querySelectorAll('.gallery-photo-stack .gallery-photo').forEach((photo) => {
            let startX = 0;
            let startY = 0;
            let originLeft = 0;
            let originTop = 0;
            let didDrag = false;

            photo.addEventListener('pointerdown', (event) => {
                if (event.button !== 0) {
                    return;
                }

                startX = event.clientX;
                startY = event.clientY;
                originLeft = photo.offsetLeft;
                originTop = photo.offsetTop;
                didDrag = false;
                photo.setPointerCapture(event.pointerId);
            });

            photo.addEventListener('pointermove', (event) => {
                if (!photo.hasPointerCapture(event.pointerId)) {
                    return;
                }

                const deltaX = event.clientX - startX;
                const deltaY = event.clientY - startY;
                if (!didDrag && Math.hypot(deltaX, deltaY) < 6) {
                    return;
                }

                didDrag = true;
                const stack = photo.parentElement;
                photo.style.left = `${Math.max(0, Math.min(stack.clientWidth - photo.offsetWidth, originLeft + deltaX))}px`;
                photo.style.top = `${Math.max(0, Math.min(stack.clientHeight - photo.offsetHeight, originTop + deltaY))}px`;
                photo.style.zIndex = String(++nextStackZIndex);
            });

            photo.addEventListener('pointerup', (event) => {
                if (photo.hasPointerCapture(event.pointerId)) {
                    photo.releasePointerCapture(event.pointerId);
                }

                if (didDrag) {
                    photo.dataset.draggedAt = String(Date.now());
                }
            });

            photo.addEventListener('click', (event) => {
                if (Date.now() - Number(photo.dataset.draggedAt ?? 0) < 300) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                }
            }, true);
        });
    })();
</script>

@endsection
