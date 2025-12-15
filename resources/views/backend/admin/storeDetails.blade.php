{{-- resources/views/backend/admin/storeDetails.blade.php --}}
@extends('layouts.master')

@section('title', 'Store Details || Step Shoe Pos')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ $store->name }} - Store Details</h4>
            <p class="text-muted mb-0">Last updated: {{ now()->format('d M Y, h:i A') }}</p>
        </div>
        <div>
            <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Error Alert -->
    @if(isset($error))
        <div class="alert alert-danger">
            <i class="ri-error-warning-line me-2"></i>
            {{ $error }}
            @if(isset($status))
                <span class="badge bg-dark ms-2">Status: {{ $status }}</span>
            @endif
        </div>
    @endif

    @if(isset($data['ok']) && $data['ok'])
        @php
            $results = $data['results'] ?? [];
            $cartInfo = $results['cart_informtion'] ?? [];
            $expenseDetails = $results['expense_details'] ?? [];
            $products = $results['products'] ?? [];
            $suppliers = $results['suppliers'] ?? [];
            $bannerInfo = $results['banner_information'] ?? [];
        @endphp
        
        <!-- Sales Summary Cards -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="mb-3">Sales Summary</h5>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">Total Sales</h6>
                                        <h4 class="mb-0">৳ {{ number_format($cartInfo['total_amount'] ?? 0, 2) }}</h4>
                                    </div>
                                    <div class="icon-box bg-white text-primary">
                                        <i class="ri-money-dollar-circle-line"></i>
                                    </div>
                                </div>
                                <p class="mb-0 small mt-2">{{ $cartInfo['total_count'] ?? 0 }} transactions</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">This Year Sales</h6>
                                        <h4 class="mb-0">৳ {{ number_format($cartInfo['total_amount_year'] ?? 0, 2) }}</h4>
                                    </div>
                                    <div class="icon-box bg-white text-info">
                                        <i class="ri-calendar-line"></i>
                                    </div>
                                </div>
                                <p class="mb-0 small mt-2">Current year {{ date('Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">Today's Sales</h6>
                                        <h4 class="mb-0">৳ {{ number_format($cartInfo['total_amount_today'] ?? 0, 2) }}</h4>
                                    </div>
                                    <div class="icon-box bg-white text-success">
                                        <i class="ri-sun-line"></i>
                                    </div>
                                </div>
                                <p class="mb-0 small mt-2">{{ date('d M Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">Total Profit</h6>
                                        <h4 class="mb-0">৳ {{ number_format($cartInfo['total_profit'] ?? 0, 2) }}</h4>
                                    </div>
                                    <div class="icon-box bg-white text-warning">
                                        <i class="ri-profit-line"></i>
                                    </div>
                                </div>
                                <p class="mb-0 small mt-2">Net profit from sales</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="ri-shopping-bag-line fs-4"></i>
                        </div>
                        <h3 class="text-primary">{{ $products['total_count'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">Products</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="ri-truck-line fs-4"></i>
                        </div>
                        <h3 class="text-info">{{ $suppliers['total_count'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">Suppliers</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="ri-money-dollar-circle-line fs-4"></i>
                        </div>
                        <h3 class="text-danger">৳ {{ number_format($expenseDetails['total_amount'] ?? 0, 2) }}</h3>
                        <p class="mb-0 text-muted">Expenses</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="ri-image-line fs-4"></i>
                        </div>
                        <h3 class="text-warning">{{ $bannerInfo['total_count'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">Banners</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other Tables -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Other Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Table</th>
                                        <th class="text-center">Count</th>
                                        <th class="text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['customer_payments', 'customer_payment_infos', 'expenses', 'purchase_info', 'purchase_details', 'supplier_payments', 'cart_items', 'final_stock_table'] as $table)
                                        @if(isset($results[$table]))
                                            <tr>
                                                <td>{{ ucfirst(str_replace('_', ' ', $table)) }}</td>
                                                <td class="text-center">{{ $results[$table]['total_count'] ?? 0 }}</td>
                                                <td class="text-center">
                                                    @if(isset($results[$table]['total_amount']) && $results[$table]['total_amount'] > 0)
                                                        ৳ {{ number_format($results[$table]['total_amount'], 2) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Raw Data for Debugging -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">API Response</h6>
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleRawData()">
                            <i class="ri-code-line"></i> Toggle Raw Data
                        </button>
                    </div>
                    <div class="card-body" id="rawData" style="display: none;">
                        <pre class="mb-0 bg-light p-3 rounded" style="font-size: 11px; max-height: 400px; overflow: auto;">{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Data Available -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ri-database-line display-1 text-muted mb-4"></i>
                <h4 class="mb-3">No Data Available</h4>
                <p class="text-muted mb-4">Could not fetch store data from the API.</p>
                <a href="javascript:window.location.reload()" class="btn btn-primary">
                    <i class="ri-refresh-line me-1"></i> Retry
                </a>
            </div>
        </div>
    @endif
</div>

<script>
function toggleRawData() {
    const rawData = document.getElementById('rawData');
    rawData.style.display = rawData.style.display === 'none' ? 'block' : 'none';
}

// Auto-refresh every 5 minutes if data is loaded
@if(isset($data['ok']) && $data['ok'])
setTimeout(function() {
    window.location.reload();
}, 5 * 60 * 1000);
@endif
</script>

<style>
.icon-box {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: transform 0.3s;
}
.card:hover {
    transform: translateY(-5px);
}
</style>
@endsection