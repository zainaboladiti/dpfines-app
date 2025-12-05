@extends('layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
    .metric-card{background:#fff;border-radius:10px;padding:1rem;box-shadow:0 6px 18px rgba(16,24,40,0.06)}
    .card-hero{background:linear-gradient(90deg,#4f46e5,#06b6d4);color:#fff;padding:1.25rem;border-radius:10px}
    .avatar-sm{width:38px;height:38px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;background:#fff;color:#111}
    .small-muted{color:#6b7280;font-size:.95rem}
</style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-0">Admin Dashboard</h2>
            <div class="small-muted">Overview of fines & review activity</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand">
                <img src="{{ asset('images/dpfines_logo.png') }}" alt="logo" style="width:36px;height:36px;border-radius:6px">
                <h3>DPFines Admin</h3>
            </div>

            <div class="small-muted">Signed in as</div>
            <div style="margin-bottom:.75rem;font-weight:600;color:#fff">{{ Auth::user()?->name }}</div>

            <nav>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> Dashboard</a>
                <a href="{{ route('admin.fines.index') }}" class="nav-link {{ request()->routeIs('admin.fines.*') ? 'active' : '' }}"><i class="fa-solid fa-gavel"></i> Global Fines</a>
                <a href="{{ route('admin.scraped-fines.index') }}" class="nav-link {{ request()->routeIs('admin.scraped-fines.*') ? 'active' : '' }}"><i class="fa-solid fa-file-import"></i> Scraped Fines</a>
                <a href="{{ route('admin.fines.create') }}" class="nav-link"><i class="fa-solid fa-plus"></i> New Fine</a>
                <a href="{{ route('admin.scraped-fines.index') }}?status=pending" class="nav-link"><i class="fa-solid fa-hourglass-half"></i> Pending Reviews</a>
                <a href="{{ route('admin.verification.send') }}" class="nav-link"><i class="fa-solid fa-envelope"></i> Resend Verification</a>
            </nav>

            <div style="margin-top:1rem;border-top:1px solid rgba(255,255,255,0.04);padding-top:1rem">
                <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); this.closest('form')?.submit();" class="nav-link"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
                <form action="{{ route('admin.logout') }}" method="POST" style="display:none">@csrf</form>
            </div>
        </aside>

        <main>
    <div class="metrics-grid mb-4">
        <a class="metric-link" href="{{ route('admin.fines.index') }}">
            <div class="metric-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="small-muted">Total Published Fines</div>
                    <div class="h3 mt-1">{{ number_format($stats['total_fines']) }}</div>
                </div>
                <div class="avatar-sm"><i class="fa-solid fa-gavel"></i></div>
            </div>
            </div>
        </a>

        <a class="metric-link" href="{{ route('admin.scraped-fines.index') }}">
            <div class="metric-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="small-muted">Scraped Items</div>
                    <div class="h3 mt-1">{{ number_format($stats['total_scraped']) }}</div>
                </div>
                <div class="avatar-sm"><i class="fa-solid fa-file-import"></i></div>
            </div>
            </div>
        </a>

        <a class="metric-link" href="{{ route('admin.scraped-fines.index') }}?status=pending">
            <div class="metric-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="small-muted">Pending Reviews</div>
                    <div class="h3 mt-1 text-warning">{{ number_format($stats['pending_reviews']) }}</div>
                </div>
                <div class="avatar-sm"><i class="fa-solid fa-hourglass-half"></i></div>
            </div>
            </div>
        </a>

        <a class="metric-link" href="{{ route('admin.scraped-fines.index') }}?status=approved">
            <div class="metric-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="small-muted">Approved This Month</div>
                    <div class="h3 mt-1 text-success">{{ number_format($stats['approved_fines']) }}</div>
                </div>
                <div class="avatar-sm"><i class="fa-solid fa-check-circle"></i></div>
            </div>
            </div>
        </a>

        {{-- Sparkline for monthly approvals (small) --}}
        <div style="grid-column:1/-1;margin-top:-6px">
            <svg id="sparkline" width="100%" height="48" viewBox="0 0 200 48" preserveAspectRatio="none"></svg>
        </div>
    </div>

    <div class="dashboard-grid">
        <div>
            <div class="card card-hero mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Welcome back, Admin</h4>
                        <div class="small-muted">Quick summary and recent activity</div>
                    </div>
                    <div class="text-end">
                        <div class="small-muted">Today</div>
                        <div class="h5 mt-1">{{ now()->format('d M Y') }}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Recent Scraped Fines</h5>
                    @if($recent_scraped->isEmpty())
                        <div class="small-muted">No recent scraped fines</div>
                    @else
                        <div class="list-group">
                            @foreach($recent_scraped as $fine)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ Str::limit($fine->organisation, 40) }}</div>
                                        <div class="small-muted">{{ $fine->regulator }} • {{ $fine->fine_date?->format('Y-m-d') }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="small-muted">{{ $fine->formatted_amount }}</div>
                                        <div class="mt-1">
                                            <a href="{{ route('admin.scraped-fines.show', $fine->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                            <a href="{{ route('admin.scraped-fines.review', $fine->id) }}" class="btn btn-sm btn-warning">Review</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Top Regulators</h5>
                    @if(isset($top_regulators) && $top_regulators->isNotEmpty())
                        <ul class="list-unstyled mb-0">
                            @foreach($top_regulators as $reg)
                                <li class="d-flex justify-content-between align-items-center py-1">
                                    <div>{{ $reg->regulator }}</div>
                                    <div class="small-muted">{{ $reg->total }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="small-muted">No data yet</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Pending Reviews</h5>
                    @if($pending_reviews->isEmpty())
                        <div class="small-muted">No pending reviews</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Organisation</th>
                                        <th>Amount</th>
                                        <th>Submitted</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pending_reviews as $fine)
                                        <tr>
                                            <td>{{ Str::limit($fine->organisation, 30) }}</td>
                                            <td>{{ $fine->formatted_amount }}</td>
                                            <td class="small-muted">{{ $fine->submittedBy?->name }}</td>
                                            <td class="text-end"><a href="{{ route('admin.scraped-fines.review', $fine->id) }}" class="btn btn-sm btn-warning">Review</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <aside>
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-2">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.fines.create') }}" class="btn btn-primary">Add Global Fine</a>
                        <a href="{{ route('admin.fines.index') }}" class="btn btn-outline-primary">Manage Fines</a>
                        <a href="{{ route('admin.scraped-fines.index') }}" class="btn btn-outline-warning">Review Scraped</a>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-2">System</h6>
                    <div class="small-muted">PHP: {{ phpversion() }}</div>
                    <div class="small-muted">Env: {{ app()->environment() }}</div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2">Support</h6>
                    <p class="small-muted">Need an audit or export? Contact the dev team for custom reports.</p>
                    <a href="mailto:admin@dpfines.local" class="btn btn-sm btn-outline-secondary">Contact</a>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Render a simple sparkline using monthly data passed from controller
    (function(){
        try {
            const monthly = @json($monthlyApproved ?? []);
            const labels = @json($months ?? []);
            const svg = document.getElementById('sparkline');
            if (!svg || !monthly.length) return;

            const w = svg.viewBox.baseVal.width || 200;
            const h = svg.viewBox.baseVal.height || 48;
            const max = Math.max(...monthly, 1);
            const points = monthly.map((v,i) => {
                const x = (i/(monthly.length-1)) * w;
                const y = h - (v / max) * (h - 8) - 4;
                return `${x},${y}`;
            }).join(' ');

            svg.innerHTML = `
                <defs>
                    <linearGradient id="g1" x1="0" x2="1">
                        <stop offset="0%" stop-color="#7c3aed" stop-opacity="0.18" />
                        <stop offset="100%" stop-color="#06b6d4" stop-opacity="0.06" />
                    </linearGradient>
                </defs>
                <polyline points="${points}" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <polyline points="${points}" fill="url(#g1)" stroke="none" stroke-width="0" opacity="0.45"/>
            `;
        } catch(e) { console.error(e); }
    })();
</script>
@endpush
