<footer class="site-footer">
    <!-- <h2>PHM HCC</h2> -->
    <p>{{ __('Pentecostal Holiness Mission Hope Christian Church (PHM HCC)') }}</p>

    <div class="social">
        @foreach(\App\Models\SocialLink::query()->whereNotNull('url')->where('url', '!=', '')->orderBy('sort_order')->orderBy('name')->get() as $socialLink)
        <a href="{{ $socialLink->url }}"
            target="_blank" rel="noopener noreferrer"
            aria-label="{{ $socialLink->name }}">
            <i class="fab {{ $socialLink->icon }}" aria-hidden="true"></i>
        </a>
        @endforeach
    </div>

    <p>&copy; {{ date('Y') }} PHM HCC. {{ __('All Rights Reserved.') }}</p>
</footer>
