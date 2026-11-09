@extends('layouts.dashboard')

@section('title', __('Contact messages').' | PHM HCC')

@section('content')
<header class="resource-page-heading">
    <div>
        <h1>{{ __('Contact messages') }}</h1>
        <p>{{ __('Messages submitted through the public contact form.') }}</p>
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
        padding: 18px 0;
        border-bottom: 1px solid #edf0f5;
    }

    .contact-message:last-of-type {
        border-bottom: 0;
    }

    .contact-message header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 8px 18px;
        margin-bottom: 10px;
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
        color: #34445a;
        line-height: 1.7;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }
</style>
@endpush
