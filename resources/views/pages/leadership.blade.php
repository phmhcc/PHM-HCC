@extends('layouts.app')
@section('title', __('Leadership | PHM HCC'))

@push('styles')
<style>
        .page-title {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 80px 20px;
            background-image: linear-gradient(rgba(186, 129, 129, 0.6), rgba(0, 0, 0, 0.60)),
                url('{{ asset("Images/church-background.jpg") }}');
            background-position: center center;
            background-size: cover;
            background-repeat: no-repeat;
        }

        .page-title h1 {
            font-size: 42px;
        }

        .leaders {
            padding: 70px 10%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
        }

        .leader-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .15);
            transition: .3s;
            text-align: center;
        }

        .leader-card:hover {
            transform: translateY(-8px);
        }


        .leader-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .leader-card h3 {
            color: #003366;
            margin-top: 20px;
        }

        .position {
            color: #FFD700;
            font-weight: bold;
            margin: 10px 0;
        }

        .leader-card p {
            padding: 0 20px 25px;
            line-height: 1.6;
        }
    </style>
@endpush

@section('content')
<section class="page-title">
        <h1>{{ __('Church Leadership') }}</h1>
        <p>{{ __('Meet the servants God has called to lead PHM HCC.') }}</p>
    </section>

    <section class="leaders">

        <div class="leader-card">
            <img src="{{ asset('Images/pastor.jpg') }}" alt="{{ __('Senior Pastor') }} Happymacx">

            <h3>Happymacx & Spora Mkomekwa</h3>

            <p class="position">
                {{ __('Senior Pastor') }}
            </p>

            <p>
                {{ __('Pastor Happymacx & Spora Mkomekwa has faithfully served the church for many years, preaching the Gospel, mentoring believers, and leading the congregation with wisdom and compassion.') }}
            </p>
        </div>

        <div class="leader-card">

            <img src="{{ asset('Images/Tumsifu.jpg') }}" alt="{{ __('Church Secretary') }} Tumsifu Didas">

            <h3> Tumsifu Didas</h3>

            <p class="position">
                {{ __('Church Secretary') }}
            </p>

            <p>
                {{ __('Responsible for spiritual guidance, discipleship, and assisting in church administration.') }}
            </p>

        </div>

         <div class="leader-card">

            <img src="{{ asset('Images/Kunjumu.jpg') }}" alt="{{ __('Church Secretary') }} Faustine Kunjumu">

            <h3> Faustine Kunjumu</h3>

            <p class="position">
                {{ __('Church Elder') }}
            </p>

            <p>
                {{ __('Responsible for spiritual guidance, discipleship, and assisting in church administration.') }}
            </p>

        </div>

        <div class="leader-card">

            <img src="{{ asset('Images/Mkole.jpg') }}" alt="{{ __('Church Elder') }} Mkole">

            <h3> Samson Mkole</h3>

            <p class="position">
                {{ __('Church Elder') }}
            </p>

            <p>
                {{ __('Responsible for spiritual guidance, discipleship, and assisting in church administration.') }}
            </p>

        </div>


        <div class="leader-card">

            <img src="{{ asset('Images/Eliya.jpg') }}" alt="{{ __('Church Elder') }} Mhagama">

            <h3> Eliya Mhagama</h3>

            <p class="position">
                {{ __('Church Elder') }}
            </p>

            <p>
                {{ __('Responsible for spiritual guidance, discipleship, and assisting in church administration.') }}
            </p>

        </div>

        <div class="leader-card">

            <img src="{{ asset('Images/Kikoti.jpg') }}" alt="{{ __('Church Elder') }} Kikoti">

            <h3> Kasian Kikoti</h3>

            <p class="position">
                {{ __('Church Elder') }}
            </p>

            <p>
                {{ __('Responsible for spiritual guidance, discipleship, and assisting in church administration.') }}
            </p>

        </div>

        <div class="leader-card">

            <img src="{{ asset('Images/Kasian.jpg') }}" alt="{{ __('Church Elder') }} Kasian">

            <h3> Kasian Mkomekwa</h3>

            <p class="position">
                {{ __('Church Elder') }}
            </p>

            <p>
                {{ __('Responsible for spiritual guidance, discipleship, and assisting in church administration.') }}
            </p>

        </div>

        <div class="leader-card">

            <img src="{{ asset('Images/Debora.jpg') }}" alt="{{ __('Church Elder') }} Majani">

            <h3> Debora Majani</h3>

            <p class="position">
                {{ __('Church Elder') }}
            </p>

            <p>
                {{ __('Responsible for spiritual guidance, discipleship, and assisting in church administration.') }}
            </p>

        </div>

        <div class="leader-card">

            <img src="{{ asset('Images/Elizabeth.jpg') }}" alt="{{ __('Head Deaconess') }} Elizabeth">

            <h3> Elizabeth Batista</h3>

            <p class="position">
                {{ __('Head Deaconess') }}
            </p>

            <p>
                {{ __('Coordinates church services, welcomes visitors, and oversees welfare ministries.') }}
            </p>
        </div>

        <div class="leader-card">

            <img src="{{ asset('Images/Comfort.jpg') }}" alt="{{ __('Deacon') }} Comfort">

            <h3>Comfort Edward</h3>

            <p class="position">
                {{ __('Deacon') }}
            </p>

            <p>
                {{ __('Coordinates church services, welcomes visitors, and oversees welfare ministries.') }}
            </p>
        </div>

        <div class="leader-card">

            <img src="{{ asset('Images/Rebecca.jpg') }}" alt="{{ __('Deaconess') }} Rebecca">

            <h3>Rebecca Sulley</h3>

            <p class="position">
                {{ __('Deaconess') }}
            </p>

            <p>
                {{ __('Coordinates church services, welcomes visitors, and oversees welfare ministries.') }}
            </p>
        </div>

        <div class="leader-card">

            <img src="{{ asset('Images/Tabby.jpg') }}" alt="{{ __('Deaconess') }} Tabiana">

            <h3>Tabiana Vitus</h3>

            <p class="position">
                {{ __('Deaconess') }}
            </p>

            <p>
                {{ __('Coordinates church services, welcomes visitors, and oversees welfare ministries.') }}
            </p>
        </div>

        <div class="leader-card">

            <img src="{{ asset('Images/Ibrahim.jpg') }}" alt="{{ __('Deacon') }} Ibrahim">

            <h3>Ibrahim Kimaro</h3>

            <p class="position">
                {{ __('Deacon') }}
            </p>

            <p>
                {{ __('Coordinates church services, welcomes visitors, and oversees welfare ministries.') }}
            </p>
        </div>
        <div class="leader-card">

            <img src="{{ asset('Images/Emmanuel.jpg') }}" alt="{{ __('Deacon') }} Emmanuel">

            <h3>Emmanuel Andrew</h3>

            <p class="position">
                {{ __('Deacon') }}
            </p>

            <p>
                {{ __('Coordinates church services, welcomes visitors, and oversees welfare ministries.') }}
            </p>
        </div>

        <div class="leader-card">

            <img src="{{ asset('Images/Rachel.jpg') }}" alt="{{ __('Deaconess') }} Rachel">

            <h3>Rachel Mkole</h3>

            <p class="position">
                {{ __('Deaconess') }}
            </p>

            <p>
                {{ __('Coordinates church services, welcomes visitors, and oversees welfare ministries.') }}
            </p>
        </div>
<!-- 
        <div class="leader-card">

            <img src="{{ asset('Images/David.jpg') }}" alt="Deacon David">

            <h3>David Alibalio</h3>

            <p class="position">
                Deacon
            </p>

            <p>
                Coordinates church services,
                welcomes visitors,
                and oversees welfare ministries.
            </p>
        </div> -->

    </section>
@endsection
