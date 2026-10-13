@extends('layouts.app')
@section('title', 'Ministries | PHM HCC')
@push('styles')
<style>
    .page-banner {
        background: #003366;
        color: white;
        text-align: center;
        padding: 80px 20px;
        background:
            linear-gradient(rgba(186, 129, 129, 0.6), rgba(0, 0, 0, 0.60)),
            url("./Images/church-background.jpg") center center / cover no-repeat;

    }

    .page-banner h1 {
        font-size: 42px;
    }

    .page-banner p {
        margin-top: 10px;
        font-size: 18px;
    }

    .ministry-section {
        padding: 70px 8%;
    }

    .ministry-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    .ministry-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
        padding: 35px;
        text-align: center;
        transition: .3s;

    }

    .ministry-card:hover {
        transform: translateY(-10px);
    }

    .ministry-card i {
        font-size: 55px;
        color: #FFD700;
        margin-bottom: 20px;
    }

    .ministry-card h3 {
        color: #003366;
        margin-bottom: 15px;
    }

    .ministry-card p {
        line-height: 1.7;
        color: #555;
    }

    .schedule {
        margin-top: 20px;
        color: #003366;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<section class="page-banner">

    <h1>Church Ministries</h1>

    <p>
        Serving God Together Through Different Ministries
    </p>

</section>

<section class="ministry-section">

    <div class="ministry-grid">

        <div class="ministry-card">

            <i class="fas fa-child"></i>

            <h3>Children's Ministry</h3>

            <p>
                Teaching children the Word of God through Bible lessons,
                songs, prayer, games, and Christian values.
            </p>

            <p class="schedule">
                Every Sunday - 8:30 AM
            </p>

        </div>

        <div class="ministry-card">

            <i class="fas fa-users"></i>

            <h3>Youth Ministry</h3>

            <p>
                Helping young people grow spiritually through Bible study,
                fellowship, worship, leadership training, and outreach.
            </p>

            <p class="schedule">
                Wednesday - 04:30 PM
            </p>

        </div>

        <div class="ministry-card">

            <i class="fas fa-female"></i>

            <h3>Women's Ministry</h3>

            <p>
                Empowering women through prayer meetings,
                Bible studies, conferences, and community service.
            </p>

            <p class="schedule">
                Every Thursday - 04:30 PM
            </p>

        </div>

        <div class="ministry-card">

            <i class="fas fa-male"></i>

            <h3>Men's Ministry</h3>

            <p>
                Encouraging men to become godly leaders in their families,
                church, workplace, and community.
            </p>

            <p class="schedule">
                Every Thursday - 06:00 PM
            </p>

        </div>

        <div class="ministry-card">

            <i class="fas fa-music"></i>

            <h3>Choir Ministry</h3>

            <p>
                Leading the congregation in praise and worship through
                music, singing, and special presentations.
            </p>

            <p class="schedule">
                Practice: Friday - 08:00 PM
            </p>

        </div>

        <div class="ministry-card">

            <i class="fas fa-cross"></i>

            <h3>Evangelism Ministry</h3>

            <p>
                Sharing the Gospel of Jesus Christ through door-to-door
                evangelism, crusades, and outreach programs.
            </p>

            <p class="schedule">
                Last Sunday of Every Month
            </p>

        </div>

        <div class="ministry-card">

            <i class="fas fa-hands-praying"></i>

            <h3>Prayer Ministry</h3>

            <p>
                Standing in prayer for the church, families,
                the nation, and the world.
            </p>

            <p class="schedule">
                Friday - 06:30 PM - 08:00 PM
            </p>

        </div>

        <div class="ministry-card">

            <i class="fas fa-hand-holding-heart"></i>

            <h3>Community Outreach</h3>

            <p>
                Serving our community through charity work,
                visiting the sick, helping families,
                and supporting those in need.
            </p>

            <p class="schedule">
                Monthly Outreach Program
            </p>
        </div>
    </div>
</section>
@endsection