@extends('layouts.dashboard')

@section('title', __('Website Content').' | PHM HCC')

@push('styles')
<style>
    #new-gallery-category-field[hidden] {
        display: none !important;
    }

    .gallery-dropzone {
        display: grid;
        justify-items: center;
        gap: 12px;
        padding: 20px;
        border: 2px dashed #b8c9dc;
        border-radius: 10px;
        background: #f8fafc;
        text-align: center;
        transition: border-color .15s ease, background-color .15s ease;
    }

    .gallery-dropzone.is-dragging {
        border-color: #003366;
        background: #eaf2fa;
    }

    .gallery-dropzone p {
        margin: 0;
        color: #34445a;
    }

    .gallery-categories-table {
        min-width: 560px;
        table-layout: fixed;
    }

    .gallery-categories-table th:nth-child(1) { width: 38%; }
    .gallery-categories-table th:nth-child(2) { width: 14%; }
    .gallery-categories-table th:nth-child(3) { width: 48%; }

    .gallery-categories-table th:last-child,
    .gallery-categories-table td:last-child {
        text-align: right;
    }

    .gallery-category-name {
        color: #183b60;
        font-weight: 600;
    }

    .gallery-category-count {
        display: inline-flex;
        min-width: 34px;
        justify-content: center;
        padding: 5px 9px;
        border-radius: 999px;
        background: #edf4fb;
        color: #164875;
        font-weight: 600;
    }

    .gallery-category-delete-hint {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 10px;
        border-radius: 7px;
        background: #f7f9fc;
        color: #66758a;
        line-height: 1.5;
        text-align: left;
    }

    .gallery-category-delete-hint i {
        color: #8b98a9;
    }

    .gallery-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid #e8edf3;
    }

    .gallery-pagination-summary {
        color: #66758a;
        font-size: 12px;
    }

    .gallery-pagination-links {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
    }

    .gallery-pagination-links a,
    .gallery-pagination-links span {
        display: inline-flex;
        min-width: 34px;
        min-height: 34px;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border: 1px solid #dce4ee;
        border-radius: 6px;
        background: #fff;
        color: #164875;
        font-size: 12px;
        text-decoration: none;
    }

    .gallery-pagination-links [aria-current="page"] {
        border-color: #003366;
        background: #003366;
        color: #fff;
    }

    .additional-payment-method-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto;
        align-items: end;
        gap: 16px;
        margin-bottom: 12px;
    }

    .additional-payment-method-row > button {
        justify-self: start;
    }

    @media (max-width: 700px) {
        .gallery-pagination {
            align-items: flex-start;
            flex-direction: column;
        }

        .gallery-pagination-links {
            justify-content: flex-start;
        }

        .additional-payment-method-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<header class="resource-page-heading">
    <div>
        <h1>{{ __('Website Content') }}</h1>
    </div>
</header>

@if(session('status'))
<div class="content-box" role="status" style="margin-bottom:20px;">{{ session('status') }}</div>
@endif

@if($errors->any())
<div class="content-box" role="alert" style="margin-bottom:20px;">
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<section class="content-box" style="margin-bottom:24px;">
    <h2 class="resource-panel-title"><i class="fas fa-images" aria-hidden="true"></i> {{ __('Add gallery photos') }}</h2>
    <p style="margin-bottom:16px;">{{ __('Choose an existing category, or select Other to add a new category. New categories appear publicly after their first photo is added.') }}</p>
    <form method="POST" action="{{ route('dashboard.site-content.gallery.upload') }}" enctype="multipart/form-data">
        @csrf
        <div class="resource-form-grid">
            <label class="resource-field">
                <span>{{ __('Existing category') }}</span>
                <select id="gallery-category" name="category_id">
                    <option value="">{{ __('Choose a category') }}</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id')==$category->id)>{{ __($category->name) }} ({{ $category->images_count }})</option>
                    @endforeach
                    <option value="other" @selected(old('category_id') === 'other')>{{ __('Other') }}</option>
                </select>
            </label>
            <label class="resource-field" id="new-gallery-category-field" @if(old('category_id') !== 'other') hidden @endif>
                <span>{{ __('Add category') }}</span>
                <input id="new-gallery-category" type="text" name="new_category" value="{{ old('new_category') }}" maxlength="100" @if(old('category_id') === 'other') required @endif>
            </label>
            <div class="resource-field">
                <span>{{ __('Photos') }}</span>
                <div class="gallery-dropzone" id="gallery-dropzone">
                    <p>{{ __('Drag and drop photos here') }}</p>
                    <div class="resource-file-picker">
                        <input id="gallery-images-file" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple required>
                        <label class="dashboard-action dashboard-action-secondary" for="gallery-images-file">{{ __('Choose file') }}</label>
                        <span data-file-name>{{ __('No file chosen') }}</span>
                    </div>
                </div>
                <small>{{ __('JPEG, PNG, or WebP; up to 10 MB per photo and 30 photos at a time.') }}</small>
            </div>
        </div>
        <button class="dashboard-action" type="submit">{{ __('Upload photos') }}</button>
    </form>
</section>

<section class="content-box" style="margin-bottom:24px;">
    <div class="resource-records-heading">
        <h2>{{ __('Gallery categories') }}</h2>
        <span>{{ $categories->count() }} {{ __('categories') }}</span>
    </div>
    <div class="table-responsive">
        <table class="dashboard-table gallery-categories-table">
            <thead>
                <tr>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Photos') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td class="gallery-category-name">{{ __($category->name) }}</td>
                    <td><span class="gallery-category-count">{{ $category->images_count }}</span></td>
                    <td>
                        @if($category->images_count === 0)
                        <form method="POST" action="{{ route('dashboard.site-content.gallery.categories.delete', $category) }}" data-confirm="{{ __('Delete this empty category?') }}" onsubmit="return confirm(this.dataset.confirm);">
                            @csrf
                            @method('DELETE')
                            <button class="dashboard-action dashboard-action-danger" type="submit">{{ __('Delete') }}</button>
                        </form>
                        @else
                        <span class="gallery-category-delete-hint"><i class="fas fa-circle-info" aria-hidden="true"></i>{{ __('Add photos to another category before deleting.') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">{{ __('No gallery categories yet.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="content-box" style="margin-bottom:24px;">
    <div class="resource-records-heading">
        <h2>{{ __('Current gallery photos') }}</h2>
        <span>{{ $images->total() }} {{ __('photos') }}</span>
    </div>
    <form method="GET" action="{{ route('dashboard.site-content.index') }}" style="display:flex;flex-wrap:wrap;align-items:end;gap:10px;margin:0 0 18px;">
        <label class="resource-field" for="gallery-category-filter" style="min-width:min(100%,280px);">
            <span>{{ __('Filter by category') }}</span>
            <select id="gallery-category-filter" name="gallery_category_id">
                <option value="">{{ __('All categories') }}</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $selectedCategoryId === (string) $category->id)>{{ __($category->name) }} ({{ $category->images_count }})</option>
                @endforeach
            </select>
        </label>
        <button class="dashboard-action" type="submit">{{ __('Filter') }}</button>
        @if($selectedCategoryId !== '')
        <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.site-content.index') }}">{{ __('Clear filter') }}</a>
        @endif
    </form>
    <div class="table-responsive">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>{{ __('Photo') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('File') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($images as $image)
                <tr>
                    <td>
                        <img
                            src="{{ route('gallery.image', $image) }}"
                            alt="Gallery image"
                            width="60"
                            height="45"
                            style="
                            display: block;
                            width: 60px;
                            height: 45px;
                            object-fit: cover;
                            border-radius: 4px;
                       ">
                    </td>
                    <td>{{ __($image->category?->name ?? '') }}</td>
                    <td>{{ $image->original_name }}</td>
                    <td>
                        <form method="POST" action="{{ route('dashboard.site-content.gallery.images.delete', $image) }}" data-confirm="{{ __('Delete this gallery photo?') }}" onsubmit="return confirm(this.dataset.confirm);">
                            @csrf
                            @method('DELETE')
                            <button class="dashboard-action dashboard-action-danger" type="submit">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">{{ __('No gallery photos yet.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <nav class="gallery-pagination" aria-label="{{ __('Pagination Navigation') }}">
        <span class="gallery-pagination-summary">{{ __('Showing :first to :last of :total results', ['first' => $images->firstItem() ?? 0, 'last' => $images->lastItem() ?? 0, 'total' => $images->total()]) }}</span>
        @if($images->hasPages())
        <div class="gallery-pagination-links">
            @if($images->onFirstPage())
            <span aria-disabled="true">{{ __('Previous') }}</span>
            @else
            <a href="{{ $images->previousPageUrl() }}" rel="prev">{{ __('Previous') }}</a>
            @endif

            @foreach($images->getUrlRange(1, $images->lastPage()) as $page => $url)
                @if($page === $images->currentPage())
                <span aria-current="page">{{ $page }}</span>
                @else
                <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($images->hasMorePages())
            <a href="{{ $images->nextPageUrl() }}" rel="next">{{ __('Next') }}</a>
            @else
            <span aria-disabled="true">{{ __('Next') }}</span>
            @endif
        </div>
        @endif
    </nav>
</section>

<section class="content-box" style="margin-bottom:24px;">
    <h2 class="resource-panel-title"><i class="fas fa-wallet" aria-hidden="true"></i> {{ __('Giving payment details') }}</h2>
    @php($additionalPaymentMethods = old('additional_methods', json_decode($settings->get('giving.additional_methods', '[]'), true) ?: []))
    <form method="POST" action="{{ route('dashboard.site-content.giving.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="resource-form-grid">
            <label class="resource-field">
                <span>{{ __('Vodacom Lipa Namba') }}</span>
                <input type="text" name="payment_number" value="{{ old('payment_number', $settings->get('giving.payment_number', '5061162')) }}" maxlength="40" required>
            </label>
            <label class="resource-field">
                <span>{{ __('Payment name / instructions') }}</span>
                <input type="text" name="payment_name" value="{{ old('payment_name', $settings->get('giving.payment_name', 'VODACOM LIPA NAMBA SADAKA HCC')) }}" maxlength="120" required>
            </label>
            <div class="resource-field">
                <span>{{ __('Giving page image') }}</span>
                @php($givingImagePath = $settings->get('giving.image_path'))
                <img src="{{ $givingImagePath ? \Illuminate\Support\Facades\Storage::disk('public')->url($givingImagePath) : asset('Images/FB_IMG_1793878208435.jpg') }}" alt="{{ __('Current giving image') }}" style="display:block;width:min(100%,320px);height:180px;object-fit:cover;border-radius:10px;margin-bottom:10px;">
                <div class="resource-file-picker">
                    <input id="giving-image-file" type="file" name="image" accept="image/jpeg,image/png,image/webp">
                    <label class="dashboard-action dashboard-action-secondary" for="giving-image-file">{{ __('Choose file') }}</label>
                    <span data-file-name>{{ __('No file chosen') }}</span>
                </div>
                <small>{{ __('JPEG, PNG, or WebP; up to 10 MB. Leave empty to keep the current image.') }}</small>
            </div>
        </div>

        <h3 style="margin:20px 0 6px;">{{ __('Additional payment methods') }}</h3>
        <p style="margin-bottom:12px;">{{ __('Add other ways people can give, such as bank transfer or another mobile money service.') }}</p>
        <div id="additional-payment-methods" data-next-index="{{ count($additionalPaymentMethods) }}">
            @forelse($additionalPaymentMethods as $index => $method)
            <div class="additional-payment-method-row" data-payment-method-row>
                <label class="resource-field">
                    <span>{{ __('Payment method name') }}</span>
                    <input type="text" name="additional_methods[{{ $index }}][name]" value="{{ $method['name'] ?? '' }}" maxlength="100">
                </label>
                <label class="resource-field">
                    <span>{{ __('Payment details / instructions') }}</span>
                    <input type="text" name="additional_methods[{{ $index }}][details]" value="{{ $method['details'] ?? '' }}" maxlength="300">
                </label>
                <button class="dashboard-action dashboard-action-danger" type="button" data-remove-payment-method>{{ __('Remove') }}</button>
            </div>
            @empty
            <div class="additional-payment-method-row" data-payment-method-row>
                <label class="resource-field">
                    <span>{{ __('Payment method name') }}</span>
                    <input type="text" name="additional_methods[0][name]" maxlength="100">
                </label>
                <label class="resource-field">
                    <span>{{ __('Payment details / instructions') }}</span>
                    <input type="text" name="additional_methods[0][details]" maxlength="300">
                </label>
                <button class="dashboard-action dashboard-action-danger" type="button" data-remove-payment-method>{{ __('Remove') }}</button>
            </div>
            @endforelse
        </div>
        <template id="additional-payment-method-template">
            <div class="additional-payment-method-row" data-payment-method-row>
                <label class="resource-field">
                    <span>{{ __('Payment method name') }}</span>
                    <input type="text" name="additional_methods[__INDEX__][name]" maxlength="100">
                </label>
                <label class="resource-field">
                    <span>{{ __('Payment details / instructions') }}</span>
                    <input type="text" name="additional_methods[__INDEX__][details]" maxlength="300">
                </label>
                <button class="dashboard-action dashboard-action-danger" type="button" data-remove-payment-method>{{ __('Remove') }}</button>
            </div>
        </template>
        <button class="dashboard-action dashboard-action-secondary" type="button" id="add-payment-method">{{ __('Add payment method') }}</button>
        <button class="dashboard-action" type="submit">{{ __('Save giving details') }}</button>
    </form>
</section>

<section class="content-box">
    <h2 class="resource-panel-title"><i class="fas fa-video" aria-hidden="true"></i> {{ __('Home page video') }}</h2>
    <p style="margin-bottom:16px;">{{ __('Add videos with their service title and date. YouTube thumbnails are loaded automatically; you can upload a thumbnail for Vimeo or MP4 videos.') }}</p>
    <form method="POST" action="{{ route('dashboard.site-content.video.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @error('videos')<small class="form-error" style="display:block;margin-bottom:12px;">{{ $message }}</small>@enderror
        <div id="home-video-list" data-next-index="{{ $homeVideoNextIndex }}">
            @foreach($homeVideoRows as $index => $video)
            <div class="home-video-admin-row" data-home-video-row style="padding:16px 0;border-bottom:1px solid #e5e7eb;">
                <input type="hidden" name="videos[{{ $index }}][id]" value="{{ $video['id'] ?? '' }}">
                <div class="resource-form-grid">
                    <label class="resource-field">
                        <span>{{ __('Video URL') }}</span>
                        <input type="url" name="videos[{{ $index }}][url]" value="{{ old("videos.$index.url", $video['url'] ?? '') }}" maxlength="2048">
                        @error("videos.$index.id")<small class="form-error">{{ $message }}</small>@enderror
                        @error("videos.$index.url")<small class="form-error">{{ $message }}</small>@enderror
                    </label>
                    <label class="resource-field">
                        <span>{{ __('Video title') }}</span>
                        <select name="videos[{{ $index }}][title]">
                            <option value="">{{ __('Select a service') }}</option>
                            @foreach($videoTitleOptions as $videoTitle)
                            <option value="{{ $videoTitle }}" @selected(old("videos.$index.title", $video['title'] ?? '') === $videoTitle)>{{ __($videoTitle) }}</option>
                            @endforeach
                        </select>
                        @error("videos.$index.title")<small class="form-error">{{ $message }}</small>@enderror
                    </label>
                    <label class="resource-field">
                        <span>{{ __('Video date') }}</span>
                        <input type="date" name="videos[{{ $index }}][date]" value="{{ old("videos.$index.date", $video['date'] ?? '') }}">
                        @error("videos.$index.date")<small class="form-error">{{ $message }}</small>@enderror
                    </label>
                    <div class="resource-field">
                        <span>{{ __('Video thumbnail') }}</span>
                        @php($thumbnailPath = $homeVideoThumbnailById->get($video['id'] ?? ''))
                        @if(filled($thumbnailPath))
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($thumbnailPath) }}" alt="{{ __('Current video thumbnail') }}" style="width:min(100%,320px);aspect-ratio:16/9;object-fit:cover;border-radius:8px;">
                        @endif
                        <div class="resource-file-picker">
                            <input id="home-video-thumbnail-{{ $loop->index }}" type="file" name="videos[{{ $index }}][thumbnail]" accept="image/jpeg,image/png,image/webp">
                            <label class="dashboard-action dashboard-action-secondary" for="home-video-thumbnail-{{ $loop->index }}">{{ __('Choose file') }}</label>
                            <span data-file-name>{{ __('No file chosen') }}</span>
                        </div>
                        <small>{{ __('JPEG, PNG, or WebP; up to 10 MB. Leave empty to keep the current thumbnail.') }}</small>
                        @error("videos.$index.thumbnail")<small class="form-error">{{ $message }}</small>@enderror
                    </div>
                </div>
                <button class="dashboard-action dashboard-action-danger" type="button" data-remove-home-video>{{ __('Remove video') }}</button>
            </div>
            @endforeach
        </div>
        <template id="home-video-template">
            <div class="home-video-admin-row" data-home-video-row style="padding:16px 0;border-bottom:1px solid #e5e7eb;">
                <input type="hidden" name="videos[__INDEX__][id]" value="">
                <div class="resource-form-grid">
                    <label class="resource-field">
                        <span>{{ __('Video URL') }}</span>
                        <input type="url" name="videos[__INDEX__][url]" maxlength="2048">
                    </label>
                    <label class="resource-field">
                        <span>{{ __('Video title') }}</span>
                        <select name="videos[__INDEX__][title]">
                            <option value="">{{ __('Select a service') }}</option>
                            @foreach($videoTitleOptions as $videoTitle)
                            <option value="{{ $videoTitle }}">{{ __($videoTitle) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="resource-field">
                        <span>{{ __('Video date') }}</span>
                        <input type="date" name="videos[__INDEX__][date]">
                    </label>
                    <div class="resource-field">
                        <span>{{ __('Video thumbnail') }}</span>
                        <div class="resource-file-picker">
                            <input id="home-video-thumbnail-__INDEX__" type="file" name="videos[__INDEX__][thumbnail]" accept="image/jpeg,image/png,image/webp">
                            <label class="dashboard-action dashboard-action-secondary" for="home-video-thumbnail-__INDEX__">{{ __('Choose file') }}</label>
                            <span data-file-name>{{ __('No file chosen') }}</span>
                        </div>
                        <small>{{ __('JPEG, PNG, or WebP; up to 10 MB. Leave empty to keep the current thumbnail.') }}</small>
                    </div>
                </div>
                <button class="dashboard-action dashboard-action-danger" type="button" data-remove-home-video>{{ __('Remove video') }}</button>
            </div>
        </template>
        <button class="dashboard-action dashboard-action-secondary" type="button" id="add-home-video">{{ __('Add another video') }}</button>
        <button class="dashboard-action" type="submit">{{ __('Save home page video') }}</button>
        <small style="display:block;margin-top:10px;">{{ __('Remove videos from this list and save to remove them from the home page.') }}</small>
    </form>
</section>

<section class="content-box">
    <h2 class="resource-panel-title"><i class="fas fa-share-nodes" aria-hidden="true"></i> {{ __('Social links') }}</h2>
    @foreach($socialLinks as $socialLink)
    <form method="POST" action="{{ route('dashboard.site-content.social-links.update', $socialLink) }}" style="padding:16px 0;border-bottom:1px solid #e5e7eb;">
        @csrf
        @method('PUT')
        <div class="resource-form-grid">
            <label class="resource-field"><span>{{ $socialLink->name }}</span><input type="url" name="url" value="{{ $socialLink->url }}" maxlength="2048"></label>
        </div>
        <button class="dashboard-action" type="submit">{{ __('Save social link') }}</button>
    </form>
    @endforeach
</section>

@push('scripts')
<script>
    (() => {
        const categorySelect = document.getElementById('gallery-category');
        const newCategoryField = document.getElementById('new-gallery-category-field');
        const newCategoryInput = document.getElementById('new-gallery-category');
        const dropzone = document.getElementById('gallery-dropzone');
        const fileInput = document.getElementById('gallery-images-file');
        const paymentMethods = document.getElementById('additional-payment-methods');
        const paymentMethodTemplate = document.getElementById('additional-payment-method-template');
        const addPaymentMethodButton = document.getElementById('add-payment-method');
        const homeVideos = document.getElementById('home-video-list');
        const homeVideoTemplate = document.getElementById('home-video-template');
        const addHomeVideoButton = document.getElementById('add-home-video');

        if (categorySelect && newCategoryField && newCategoryInput) {
            const updateCategoryField = () => {
                const isOther = categorySelect.value === 'other';
                newCategoryField.hidden = !isOther;
                newCategoryInput.required = isOther;
            };

            categorySelect.addEventListener('change', updateCategoryField);
            updateCategoryField();
        }

        if (dropzone && fileInput) {
            ['dragenter', 'dragover'].forEach((eventName) => {
                dropzone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    dropzone.classList.add('is-dragging');
                });
            });

            ['dragleave', 'drop'].forEach((eventName) => {
                dropzone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    const nextTarget = event.relatedTarget;
                    if (eventName === 'drop' || !(nextTarget instanceof Node && dropzone.contains(nextTarget))) {
                        dropzone.classList.remove('is-dragging');
                    }
                });
            });

            dropzone.addEventListener('drop', (event) => {
                if (event.dataTransfer?.files.length) {
                    fileInput.files = event.dataTransfer.files;
                    fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        }

        if (paymentMethods && paymentMethodTemplate && addPaymentMethodButton) {
            let nextIndex = Math.max(
                Number(paymentMethods.dataset.nextIndex || 0),
                paymentMethods.querySelectorAll('[data-payment-method-row]').length,
            );

            const updateAddPaymentMethodButton = () => {
                addPaymentMethodButton.disabled = paymentMethods.querySelectorAll('[data-payment-method-row]').length >= 10;
            };

            addPaymentMethodButton.addEventListener('click', () => {
                if (paymentMethods.querySelectorAll('[data-payment-method-row]').length >= 10) {
                    return;
                }

                const row = paymentMethodTemplate.content.cloneNode(true);
                row.querySelectorAll('[name]').forEach((input) => {
                    input.name = input.name.replaceAll('__INDEX__', String(nextIndex));
                });
                paymentMethods.append(row);
                nextIndex += 1;
                updateAddPaymentMethodButton();
            });

            paymentMethods.addEventListener('click', (event) => {
                if (event.target instanceof Element && event.target.closest('[data-remove-payment-method]')) {
                    event.target.closest('[data-payment-method-row]')?.remove();
                    updateAddPaymentMethodButton();
                }
            });

            updateAddPaymentMethodButton();
        }

        if (homeVideos && homeVideoTemplate && addHomeVideoButton) {
            let nextVideoIndex = Number(homeVideos.dataset.nextIndex || 0);

            const updateAddHomeVideoButton = () => {
                addHomeVideoButton.disabled = homeVideos.querySelectorAll('[data-home-video-row]').length >= 20;
            };

            addHomeVideoButton.addEventListener('click', () => {
                if (homeVideos.querySelectorAll('[data-home-video-row]').length >= 20) {
                    return;
                }

                const row = homeVideoTemplate.content.cloneNode(true);
                row.querySelectorAll('[name]').forEach((input) => {
                    input.name = input.name.replaceAll('__INDEX__', String(nextVideoIndex));
                });
                homeVideos.append(row);
                nextVideoIndex += 1;
                updateAddHomeVideoButton();
            });

            homeVideos.addEventListener('click', (event) => {
                if (event.target instanceof Element && event.target.closest('[data-remove-home-video]')) {
                    event.target.closest('[data-home-video-row]')?.remove();
                    updateAddHomeVideoButton();
                }
            });

            updateAddHomeVideoButton();
        }
    })();
</script>
@endpush
@endsection
