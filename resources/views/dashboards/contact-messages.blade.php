@extends('layouts.dashboard')

@section('title', __('Contact messages').' | PHM HCC')

@section('content')
<header class="resource-page-heading">
    <div>
        <h1>{{ __('Contact messages') }}</h1>
    </div>
</header>

<section class="content-box">
    <div class="resource-records-heading">
        <h2>{{ __('Inbox') }}</h2>
        <span>{{ $messages->total() }} {{ __('messages') }}</span>
    </div>
    @forelse($messages as $message)
    @php($messageTime = $message->created_at->copy()->setTimezone('Africa/Dar_es_Salaam'))
    <article class="contact-message">
        <header>
            <div>
                <h3>{{ $message->name }}</h3>
                <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
            </div>
            <time datetime="{{ $messageTime->toIso8601String() }}">
                {{ $messageTime->locale(app()->getLocale())->translatedFormat('d M Y, h:i A') }}
            </time>
        </header>
        <p>{{ $message->message }}</p>
    </article>
    @empty
    <div class="dashboard-empty-state">
        <span><i class="far fa-envelope" aria-hidden="true"></i></span>
        <h2>{{ __('No messages yet.') }}</h2>
    </div>
    @endforelse
    {{ $messages->links() }}
</section>
@endsection

@push('styles')
<style>
    .contact-message {
        min-width: 0;
        padding: 18px 0;
        border-bottom: 1px solid #edf0f5;
    }

    .contact-message:last-of-type {
        border-bottom: 0;
    }

    .contact-message header {
        display: flex;
        min-width: 0;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 8px 18px;
        margin-bottom: 10px;
    }

    .contact-message header > div {
        min-width: 0;
        max-width: 100%;
    }

    .contact-message h3 {
        color: #123b64;
        font-size: 15px;
    }

    .contact-message a,
    .contact-message time {
        color: #718096;
        font-size: 12px;
        overflow-wrap: anywhere;
    }

    .contact-message p {
        max-width: 100%;
        margin: 0;
        color: #34445a;
        line-height: 1.7;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    @media (max-width: 600px) {
        .contact-message header {
            align-items: flex-start;
            flex-direction: column;
        }

        .contact-message time {
            max-width: 100%;
        }
    }
</style>
@endpush
