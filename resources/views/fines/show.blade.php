@extends('layout')

@section('content')

<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container">
    <a href="/" class="btn btn-secondary mb-3">← Back to Home</a>

    <div class="fine-detail-card">
        <div class="fine-detail-header">
            <h1>{{ $fine->organisation }}</h1>
            <p class="fine-detail-regulator">{{ $fine->regulator }}</p>
        </div>

        <div class="fine-detail-grid">
            <div class="detail-item">
                <label>Fine Amount</label>
                <div class="detail-amount">{{ $fine->formatted_amount }}</div>
            </div>

            <div class="detail-item">
                <label>Date</label>
                <div class="detail-text">{{ \Carbon\Carbon::parse($fine->fine_date)->format('M d, Y') }}</div>
            </div>

            <div class="detail-item">
                <label>Regulator</label>
                <div class="detail-text">{{ $fine->regulator }}</div>
            </div>

            <div class="detail-item">
                <label>Sector</label>
                <div class="detail-text">{{ $fine->sector }}</div>
            </div>

            <div class="detail-item">
                <label>Region</label>
                <div class="detail-text">{{ $fine->region }}</div>
            </div>

            <div class="detail-item">
                <label>Currency</label>
                <div class="detail-text">{{ $fine->currency }}</div>
            </div>

            <div class="detail-item">
                <label>Violation Type</label>
                <span class="badge badge-red">{{ $fine->violation_type }}</span>
            </div>

            <div class="detail-item">
                <label>Law/Regulation</label>
                <div class="detail-text">{{ $fine->law }}</div>
            </div>

            <div class="detail-item">
                <label>Articles Breached</label>
                <div class="detail-text">{{ $fine->articles_breached }}</div>
            </div>
        </div>

        <div class="fine-detail-section">
            <h2>Summary</h2>
            <p class="fine-detail-summary">{{ $fine->summary }}</p>
        </div>

        @if($fine->badges)
        <div class="fine-detail-section">
            <h2>Tags</h2>
            <div class="badges-list">
                @foreach(explode(',', $fine->badges) as $badge)
                    <span class="badge">{{ trim($badge) }}</span>
                @endforeach
            </div>
        </div>
        @endif

        <div class="fine-detail-actions">
            <a href="{{ $fine->link_to_case }}" target="_blank" class="btn btn-primary">
                <i class=""></i> View Case
            </a>
            <a href="/database" class="btn btn-secondary">
                <i class=""></i> View Full Database
            </a>

        </div>
    </div>
</div>

@endsection
