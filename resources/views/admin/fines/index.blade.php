@extends('layout')

@section('content')
<div class="admin-shell">
    @include('admin._sidebar')
    <main class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Manage Global Fines</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.fines.create') }}" class="btn btn-primary">Add New Fine</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Dashboard</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="regulator" class="form-control" placeholder="Regulator"
                           value="{{ request('regulator') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="sector" class="form-control" placeholder="Sector"
                           value="{{ request('sector') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Fines Table -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Organisation</th>
                    <th>Regulator</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Sector</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fines as $fine)
                    <tr>
                        <td><strong>{{ Str::limit($fine->organisation, 25) }}</strong></td>
                        <td>{{ $fine->regulator ?? '-' }}</td>
                        <td>{{ $fine->formatted_amount }}</td>
                        <td>{{ $fine->fine_date?->format('M d, Y') ?? '-' }}</td>
                        <td>{{ Str::limit($fine->sector, 20) }}</td>
                        <td>
                            <a href="{{ route('admin.fines.show', $fine->id) }}" class="btn btn-sm btn-info">View</a>
                            <a href="{{ route('admin.fines.edit', $fine->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.fines.destroy', $fine->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirm delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No fines found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $fines->links() }}
    </div>
    </main>
</div>
@endsection
