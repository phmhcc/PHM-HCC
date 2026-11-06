@extends('layouts.dashboard')

@section('title', __('Website Content').' | PHM HCC')

@section('content')
<header class="resource-page-heading">
    <div>
        <h1>{{ __('Website Content') }}</h1>
        <p>{{ __('Manage public gallery photos, social links, and giving details.') }}</p>
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
    <p style="margin-bottom:16px;">{{ __('Choose an existing category or enter a new category name. New categories appear publicly after their first photo is added.') }}</p>
    <form method="POST" action="{{ route('dashboard.site-content.gallery.upload') }}" enctype="multipart/form-data">
        @csrf
        <div class="resource-form-grid">
            <label class="resource-field">
                <span>{{ __('Existing category') }}</span>
                <select name="category_id">
                    <option value="">{{ __('Choose a category') }}</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id')==$category->id)>{{ __($category->name) }} ({{ $category->images_count }})</option>
                    @endforeach
                </select>
            </label>
            <label class="resource-field">
                <span>{{ __('Or add a category') }}</span>
                <input type="text" name="new_category" value="{{ old('new_category') }}" maxlength="100">
            </label>
            <label class="resource-field">
                <span>{{ __('Photos') }}</span>
                <div class="resource-file-picker">
                    <input id="gallery-images-file" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple required>
                    <label class="dashboard-action dashboard-action-secondary" for="gallery-images-file">{{ __('Choose file') }}</label>
                    <span data-file-name>{{ __('No file chosen') }}</span>
                </div>
                <small>{{ __('JPEG, PNG, or WebP; up to 10 MB per photo and 30 photos at a time.') }}</small>
            </label>
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
        <table class="dashboard-table">
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
                    <td>{{ __($category->name) }}</td>
                    <td>{{ $category->images_count }}</td>
                    <td>
                        @if($category->images_count === 0)
                        <form method="POST" action="{{ route('dashboard.site-content.gallery.categories.delete', $category) }}" data-confirm="{{ __('Delete this empty category?') }}" onsubmit="return confirm(this.dataset.confirm);">
                            @csrf
                            @method('DELETE')
                            <button class="dashboard-action dashboard-action-danger" type="submit">{{ __('Delete') }}</button>
                        </form>
                        @else
                        <span>{{ __('Add photos to another category before deleting.') }}</span>
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
    {{ $images->links() }}
</section>

<section class="content-box" style="margin-bottom:24px;">
    <h2 class="resource-panel-title"><i class="fas fa-wallet" aria-hidden="true"></i> {{ __('Giving payment details') }}</h2>
    <form method="POST" action="{{ route('dashboard.site-content.giving.update') }}">
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
        </div>
        <button class="dashboard-action" type="submit">{{ __('Save giving details') }}</button>
    </form>
</section>

<section class="content-box">
    <h2 class="resource-panel-title"><i class="fas fa-share-nodes" aria-hidden="true"></i> {{ __('Social links') }}</h2>
    @foreach($socialLinks as $socialLink)
    <form method="POST" action="{{ route('dashboard.site-content.social-links.update', $socialLink) }}" style="padding:16px 0;border-bottom:1px solid #e5e7eb;">
        @csrf
        @method('PUT')
        <div class="resource-form-grid">
            <label class="resource-field"><span>{{ __('Name') }}</span><input type="text" name="name" value="{{ $socialLink->name }}" maxlength="80" required></label>
            <label class="resource-field"><span>{{ __('URL') }}</span><input type="url" name="url" value="{{ $socialLink->url }}" maxlength="2048" required></label>
            <label class="resource-field"><span>{{ __('Icon class') }}</span><input type="text" name="icon" value="{{ $socialLink->icon }}" pattern="fa-[a-z0-9-]+" required><small>{{ __('Font Awesome icon class, for example fa-facebook.') }}</small></label>
            <label class="resource-field"><span>{{ __('Display order') }}</span><input type="number" name="sort_order" value="{{ $socialLink->sort_order }}" min="0" max="65535" required></label>
        </div>
        <button class="dashboard-action" type="submit">{{ __('Save social link') }}</button>
        <button class="dashboard-action dashboard-action-danger" type="submit" form="delete-social-{{ $socialLink->id }}">{{ __('Delete') }}</button>
    </form>
    <form id="delete-social-{{ $socialLink->id }}" method="POST" action="{{ route('dashboard.site-content.social-links.delete', $socialLink) }}" data-confirm="{{ __('Delete this social link?') }}" onsubmit="return confirm(this.dataset.confirm);">
        @csrf
        @method('DELETE')
    </form>
    @endforeach

    <h3 style="margin:22px 0 12px;">{{ __('Add a social link') }}</h3>
    <form method="POST" action="{{ route('dashboard.site-content.social-links.store') }}">
        @csrf
        <div class="resource-form-grid">
            <label class="resource-field"><span>{{ __('Name') }}</span><input type="text" name="name" value="{{ old('name') }}" maxlength="80" required></label>
            <label class="resource-field"><span>{{ __('URL') }}</span><input type="url" name="url" value="{{ old('url') }}" maxlength="2048" required></label>
            <label class="resource-field"><span>{{ __('Icon class') }}</span><input type="text" name="icon" value="{{ old('icon', 'fa-link') }}" pattern="fa-[a-z0-9-]+" required></label>
            <label class="resource-field"><span>{{ __('Display order') }}</span><input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" max="65535" required></label>
        </div>
        <button class="dashboard-action" type="submit">{{ __('Add social link') }}</button>
    </form>
</section>
@endsection
