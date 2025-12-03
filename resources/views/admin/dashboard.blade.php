@extends('layout')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Admin Dashboard</h1>
        </div>
        <div class="col-md-4 text-end">
            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Fines</h5>
                    <h2 class="text-primary">{{ $stats['total_fines'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Pending Reviews</h5>
                    <h2 class="text-warning">{{ $stats['pending_reviews'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Approved</h5>
                    <h2 class="text-success">{{ $stats['approved_fines'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Rejected</h5>
                    <h2 class="text-danger">{{ $stats['rejected_fines'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h3>Quick Actions</h3>
            <a href="{{ route('admin.fines.index') }}" class="btn btn-primary me-2">Manage Global Fines</a>
            <a href="{{ route('admin.scraped-fines.create') }}" class="btn btn-info me-2">Submit Scraped Fine</a>
            <a href="{{ route('admin.scraped-fines.index') }}" class="btn btn-warning">Review Scraped Fines</a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-6">
            <h3>Recent Scraped Fines</h3>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Organisation</th>
                            <th>Amount</th>
                            <th>Submitted By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent_scraped as $fine)
                            <tr>
                                <td>{{ Str::limit($fine->organisation, 20) }}</td>
                                <td>{{ $fine->formatted_amount }}</td>
                                <td>{{ $fine->submittedBy->name }}</td>
                                <td>
                                    <a href="{{ route('admin.scraped-fines.show', $fine->id) }}" class="btn btn-sm btn-secondary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No recent scraped fines</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <h3>Pending Reviews</h3>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Organisation</th>
                            <th>Amount</th>
                            <th>Submitted By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pending_reviews as $fine)
                            <tr>
                                <td>{{ Str::limit($fine->organisation, 20) }}</td>
                                <td>{{ $fine->formatted_amount }}</td>
                                <td>{{ $fine->submittedBy->name }}</td>
                                <td>
                                    <a href="{{ route('admin.scraped-fines.review', $fine->id) }}" class="btn btn-sm btn-warning">Review</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No pending reviews</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
