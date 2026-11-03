@extends('layouts.app')
@section('title', __('Contact').' | PHM HCC')
@push('styles')
<style>
    .page-header {
        background: #003366;
        color: white;
        text-align: center;
        padding: 80px 20px;
        background:
            linear-gradient(rgba(186, 129, 129, 0.6), rgba(0, 0, 0, 0.60)),
            url("./Images/church-background.jpg") center center / cover no-repeat;

    }

    .page-header h1 {
        font-size: 42px;
    }

    .contact-section {
        padding: 70px 8%;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        /* background:
            linear-gradient(rgba(186, 129, 129, 0.6), rgba(0, 0, 0, 0.60)),
            url("./Images/church-background.jpg") center center / cover no-repeat; */

    }

    .contact-info {
        background: #003366;
        color: white;
        padding: 35px;
        border-radius: 15px;
    }

    .contact-info h2 {
        color: #FFD700;
        margin-bottom: 20px;
    }

    .contact-info p {
        margin: 15px 0;
        line-height: 1.8;
    }

    .contact-info i {
        color: #FFD700;
        margin-right: 10px;
    }

    .contact-info a {
        color: white;
        text-decoration: none;
    }

    .contact-form {
        background: white;
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
    }

    .contact-form h2 {
        color: #003366;
        margin-bottom: 20px;

    }

    .contact-form input,
    .contact-form textarea {

        width: 100%;
        padding: 14px;
        margin-bottom: 18px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;

    }

    .contact-form button {

        width: 100%;
        padding: 14px;
        border: none;
        background: #FFD700;
        color: #003366;
        font-weight: bold;
        border-radius: 8px;
        cursor: pointer;

    }

    .contact-form button:hover {

        background: #003366;
        color: white;

    }

    .social a {

        font-size: 25px;
        margin-right: 15px;
        color: #FFD700;

    }

    @media(max-width:900px) {

        .contact-section {

            grid-template-columns: 1fr;

        }

    }
</style>
@endpush

@section('content')
<section class="page-header">
    <h1>{{ __('Contact us') }}</h1>
    <p>{{ __('We would love to hear from you.') }}</p>
</section>

<section class="contact-section">
    <div class="contact-info">
        <h2>{{ __('Church Information') }}</h2>
        <p>
            <i class="fas fa-map-marker-alt"></i>
            Mbezi-Magari Saba,
            Dar es Salaam, Tanzania
        </p>

        <p>
            <i class="fas fa-phone"></i>
            <a href="tel:+255754970421">
                +255 754 970 421
            </a>
        </p>

        <p>
            <i class="fas fa-envelope"></i>
            <a href="mailto:info@phmhcc.org">
                info@phmhcc.org
            </a>
        </p>

        <p>
            <i class="fas fa-clock"></i>
            {{ __('Sunday Worship') }}:
            <br>
            {{ __('06:30 AM - 09:30 AM (English Service)') }}
            <br>
            {{ __('09:00 AM - 01:00 PM (Swahili Service)') }}
        </p>

        <p>
            <i class="fas fa-clock"></i>
            {{ __('Tuesday Bible Study') }}:
            <br>
            06:30 PM - 08:00 PM
        </p>

        <p>
            <i class="fas fa-clock"></i>
            {{ __('Friday Prayer') }}:
            <br>
            06:30 PM - 08:00 PM
        </p>

        <div class="social">
            <a href="https://www.facebook.com/share/14kCi4Q2iXW/" target="_blank" rel="noopener noreferrer">
                
                <i class="fab fa-facebook"></i>
            </a>

            <a href="https://youtube.com/@pastor-happymacxmkomekwa" target="_blank" rel="noopener noreferrer">

                <i class="fab fa-youtube"></i>

            </a>

            <a href="https://www.instagram.com/pastor_mkomekwa" target="_blank" rel="noopener noreferrer">

                <i class="fab fa-instagram"></i>

            </a>

            <a href="https://wa.me/255754970421" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-whatsapp"></i>

            </a>
        </div>
    </div>

    <div class="contact-form">
        <h2>{{ __('Send Us a Message') }}</h2>

            {{-- Success message --}}
            @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('contact.send') }}" method="POST">
                @csrf

                <input
                    type="text"
                    name="name"
                    placeholder="{{ __('Full Name') }}"
                    value="{{ old('name') }}"
                    required>

                <input
                    type="email"
                    name="email"
                    placeholder="{{ __('Email address') }}"
                    value="{{ old('email') }}"
                    required>

                <textarea
                    name="message"
                    rows="7"
                    placeholder="{{ __('Write your message here...') }}"
                    required>{{ old('message') }}</textarea>

                <button type="submit">{{ __('Send Message') }}</button>
            </form>

        </form>
    </div>
</section>

@endsection