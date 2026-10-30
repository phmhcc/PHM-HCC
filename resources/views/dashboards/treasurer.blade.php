@extends('layouts.dashboard')

@section('title', 'Treasurer Dashboard | PHM HCC')
@section('page-heading', 'Treasury')

@section('content')
<header class="role-hero">
    <div class="role-hero-content">
        <span class="role-eyebrow">Finance portal</span>
        <h1>Treasurer Dashboard</h1>
        <p>Maintain accurate records and support transparent financial stewardship.</p>
    </div>
    <div class="role-hero-icon"><i class="fas fa-wallet" aria-hidden="true"></i></div>
</header>
<h2 class="role-section-title">Treasury responsibilities</h2>
<section class="role-cards" aria-label="Treasurer responsibilities">
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-file-invoice-dollar" aria-hidden="true"></i></div><h2>Financial records</h2><p>Keep complete records of church income and expenditure.</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-scale-balanced" aria-hidden="true"></i></div><h2>Accountability</h2><p>Prepare clear financial updates for authorized leadership.</p></article>
    <article class="role-card"><div class="role-card-icon"><i class="fas fa-chart-pie" aria-hidden="true"></i></div><h2>Budget oversight</h2><p>Track approved budgets and support responsible planning.</p></article>
</section>
<section class="content-box">
    <h2 class="role-section-title">Quick actions</h2>
    <div class="role-tools">
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'finance') }}"><span><i class="fas fa-wallet"></i> Manage transactions</span><i class="fas fa-arrow-right"></i></a>
        <a class="role-tool-link" href="{{ route('dashboard.resource.index', 'reports') }}"><span><i class="fas fa-file-lines"></i> Financial reporting</span><i class="fas fa-arrow-right"></i></a>
    </div>
 </section>
@endsection
