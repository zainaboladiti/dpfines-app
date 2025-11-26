@extends('layout')

@section('content')
<h1 class="mb-4">Global Data Privacy Fines</h1>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Organisation</th>
            <th>Regulator</th>
            <th>Sector</th>
            <th>Fine Amount</th>
            <th>Date</th>
            <th>Law</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($fines as $fine)
        <tr>
            <td>{{ $fine->organisation }}</td>
            <td>{{ $fine->regulator }}</td>
            <td>{{ $fine->sector }}</td>
            <td>{{ number_format($fine->fine_amount, 2) }} {{ $fine->currency }}</td>
            <td>{{ $fine->fine_date }}</td>
            <td>{{ $fine->law }}</td>
            <td>
                <a href="{{ route('fines.show', $fine->id) }}" class="btn btn-primary btn-sm">Details</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $fines->links() }}

@endsection
