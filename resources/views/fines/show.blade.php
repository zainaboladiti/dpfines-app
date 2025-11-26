@extends('layout')

@section('content')

<a href="/" class="btn btn-secondary mb-3">← Back</a>

<div class="card">
    <div class="card-header">
        <h2>{{ $fine->organisation }}</h2>
    </div>

    <div class="card-body">
        <p><strong>Regulator:</strong> {{ $fine->regulator }}</p>
        <p><strong>Sector:</strong> {{ $fine->sector }}</p>
        <p><strong>Region:</strong> {{ $fine->region }}</p>
        <p><strong>Fine Amount:</strong> {{ number_format($fine->fine_amount, 2) }} {{ $fine->currency }}</p>
        <p><strong>Date:</strong> {{ $fine->fine_date }}</p>
        <p><strong>Law:</strong> {{ $fine->law }}</p>
        <p><strong>Articles Breached:</strong> {{ $fine->articles_breached }}</p>
        <p><strong>Violation Type:</strong> {{ $fine->violation_type }}</p>
        <p><strong>Summary:</strong> {{ $fine->summary }}</p>
        <p><strong>Badges:</strong> {{ $fine->badges }}</p>
        <p><strong>Link to Case:</strong> <a href="{{ $fine->link_to_case }}" target="_blank">View Source</a></p>
    </div>
</div>

@endsection
