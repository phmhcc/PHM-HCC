@extends('layouts.app')

@section('title', __('Giving | PHM HCC'))

@push('styles')
<style>
    .giving-page {
        color: #242a30;
    }

    .giving-intro {
        display: grid;
        grid-template-columns: minmax(0, .9fr) minmax(0, 1fr);
        align-items: center;
        gap: clamp(36px, 7vw, 90px);
        padding: 58px max(6vw, calc((100vw - 1060px) / 2));
        background: #fff3e4;
    }

    .giving-photo-stack {
        position: relative;
        height: 340px;
    }

    .giving-photo-link {
        position: absolute;
        inset: 0;
        display: block;
        height: 100%;
        border-radius: 16px;
    }

    .giving-photo-link:focus-visible {
        outline: 3px solid #3373ad;
        outline-offset: 4px;
    }

    .giving-photo-link img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: inherit;
        box-shadow: 0 12px 30px rgba(37, 46, 56, .16);
    }

    .giving-copy h1,
    .giving-section h2 {
        color: #20252a;
        font-size: clamp(26px, 3vw, 34px);
        text-align: center;
    }

    .giving-copy h1 {
        margin: 0 0 26px;
        text-align: left;
    }

    .giving-copy h1 em,
    .giving-section h2 em {
        color: #e87916;
    }

    .giving-copy p {
        margin: 0 0 20px;
        color: #55514e;
        line-height: 1.9;
    }

    .giving-section {
        max-width: 1080px;
        margin: 0 auto;
        padding: 14px 28px 58px;
    }

    .giving-section h2 {
        margin: 10px 0 38px;
    }

    .giving-payment-card {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        padding: 38px clamp(24px, 7vw, 80px);
        border-left: 4px solid #f47b16;
        background: #fff3e4;
    }

    .giving-payment-card h3 {
        margin: 0 0 12px;
        color: #252b31;
        font-size: 17px;
    }

    .giving-payment-card p {
        margin: 7px 0;
        color: #55514e;
        line-height: 1.7;
    }

    .giving-number {
        color: #202c38 !important;
        font-size: 24px;
        font-weight: 700;
        letter-spacing: .04em;
    }

    .giving-payment-help {
        grid-column: 1 / -1;
        margin-top: 8px !important;
    }

    .giving-contact {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px 28px;
        margin-top: 24px;
        text-align: center;
    }

    .giving-contact a {
        color: #003366;
        font-weight: 600;
    }

    @media (max-width: 700px) {
        .giving-intro {
            grid-template-columns: 1fr;
            gap: 26px;
            padding: 36px 22px 44px;
        }

        .giving-photo-stack {
            height: 290px;
        }

        .giving-copy h1 {
            text-align: center;
        }

        .giving-section {
            padding: 8px 18px 44px;
        }

        .giving-payment-card {
            grid-template-columns: 1fr;
            gap: 24px;
            padding: 28px 22px;
        }

        .giving-payment-help {
            grid-column: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="giving-page">
    <section class="giving-intro" aria-labelledby="giving-heading">
        <div class="giving-photo-stack" aria-label="{{ __('Giving and community outreach') }}">
            <a class="giving-photo-link" href="{{ $givingImageUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Open community outreach photo in a new tab') }}">
                <img src="{{ $givingImageUrl }}" alt="{{ __('Church members serving children in the community') }}">
            </a>
        </div>

        <div class="giving-copy">
            <h1 id="giving-heading">{{ __('We are') }} <em>{{ __('generous') }}</em></h1>
            <p>{{ __('Giving is part of our culture and who we are. Your offering supports the church’s mission to share the good news of Jesus Christ and disciple our community.') }}</p>
            <p>{{ __('You can give through Vodacom Lipa Namba or in person during a church service. Thank you for supporting PHM HCC and its ministries.') }}</p>
        </div>
    </section>

    <section class="giving-section" aria-labelledby="mobile-giving-heading">
        <h2 id="mobile-giving-heading">{{ __('Mobile') }} <em>{{ __('giving') }}</em></h2>

        <div class="giving-payment-card">
            <div>
                <h3>{{ __('Vodacom M-Pesa') }}</h3>
                <p>{{ __('LIPA NAMBA') }}</p>
                <p class="giving-number">{{ $paymentNumber }}</p>
                <p>{{ $paymentName }}</p>
                @foreach($additionalPaymentMethods as $method)
                    <h3 style="margin-top:24px;">{{ $method['name'] ?? '' }}</h3>
                    <p>{{ $method['details'] ?? '' }}</p>
                @endforeach
            </div>
            <div>
                <h3>{{ __('Give in person') }}</h3>
                <p>{{ __('You are welcome to give during a church service at our location.') }}</p>
                <p>{{ __('Mbezi-Magari Saba, Dar es Salaam, Tanzania') }}</p>
            </div>
            <p class="giving-payment-help">{{ __('If you need help with your gift, contact the church using the official number below.') }}</p>
        </div>

        <div class="giving-contact">
            <a href="tel:+255754970421">+255 754 970 421</a>
            <a href="https://wa.me/255754970421" target="_blank" rel="noopener noreferrer">{{ __('Message our Pastor on WhatsApp') }}</a>
        </div>
    </section>
</div>
@endsection
