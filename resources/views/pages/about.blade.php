
@extends('layouts.app')

@section('title', __('About').' | PHM HCC')

@push('styles')
<style>
    .welcome {
        padding: 70px 8%;
        text-align: center;
        background:
            linear-gradient(rgba(4, 4, 4, 0.6), rgba(85, 64, 64, 0.6)),
            url("{{ asset('Images/church-background.jpg') }}")
            center center / cover no-repeat;
    }

    .mission {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 50px;
        padding: 70px 8%;
        background: whitesmoke;
    }

    .mission div {
        max-width: 400px;
    }

    .mission h2 {
        font-size: 28px;
        margin-bottom: 15px;
        color:gold;
    }

    .mission p {
        font-size: 18px;
        line-height: 1.6;
    }
</style>
@endpush

@section('content')

<section class="welcome">
    <h2 style="color:white">{{ __('About Our Church') }}</h2>

    <p style="color:white">
        {{ __('Pentecostal Holiness Mission Hope Christian Church (PHM HCC) is a Bible-believing church committed to preaching the Gospel of Jesus Christ, nurturing believers, and serving the community through love, prayer, and outreach. We welcome everyone to worship with us and grow in faith.') }}
    </p>
</section>

<section class="mission">
    <div>
        <h2>{{ __('Our Vision') }}</h2>
        <p>
            {{ __('To build a Christ-centered church that transforms lives through faith, love, and the power of the Holy Spirit.') }}
        </p>
    </div>

    <div>
        <h2>{{ __('Our Mission') }}</h2>
        <p>
            {{ __('To preach the Gospel, disciple believers, equip leaders, and reach communities with the message of hope and salvation.') }}
        </p>
    </div>

    <div>
        <h2>{{ __('Core Values') }}</h2>

        <ul style="font-size:18px;line-height:2;list-style:square;
                   text-align:left;max-width:800px;margin:auto;">
            <li>{{ __('Faith in Jesus Christ') }}</li>
            <li>{{ __('Prayer and Worship') }}</li>
            <li>{{ __('Love and Compassion') }}</li>
            <li>{{ __('Integrity and Holiness') }}</li>
            <li>{{ __('Community Service') }}</li>
            <li>{{ __('Evangelism and Discipleship') }}</li>
        </ul>
    </div>
</section>

@endsection
