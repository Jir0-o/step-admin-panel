@extends('layouts.master')

@section('title', $store->name . ' - Stock Data || Step Shoe POS')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="{{ route('stores.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                        <i class="ri-arrow-left-line me-1"></i> Back to Stores
                    </a>
                    <div>
                        <h4 class="mb-1">{{ $store->name }} - Stock Data</h4>
                        <p class="text-muted mb-0">View and manage final stock table data</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('manager.stock-data.export', ['store' => $store->id]) }}" 
                       class="btn btn-success me-2">
                        <i class="ri-download-line me-1"></i> Export CSV
                    </a>
                    <button id="refreshBtn" class="btn btn-primary">
                        <i class="ri-refresh-line me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                <i class="ri-box-3-line fs-2 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" id="totalItems">0</h5>
                            <p class="text-muted mb-0">Total Items</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                <i class="ri-stack-line fs-2 text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" id="totalQuantity">0</h5>
                            <p class="text-muted mb-0">Total Quantity</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                <i class="ri-money-dollar-circle-line fs-2 text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" id="totalValue">0</h5>
                            <p class="text-muted mb-0">Total Value</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                <i class="ri-alert-line fs-2 text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" id="lowStock">0</h5>
                            <p class="text-muted mb-0">Low Stock Items</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">Stock Items</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="searchProduct" placeholder="Search product...">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" id="filterCategory">
                                        <option value="">All Categories</option>
                                        <!-- Categories will be populated dynamically -->
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" id="filterStatus">
                                        <option value="">All Status</option>
                                        <option value="in_stock">In Stock</option>
                                        <option value="low_stock">Low Stock</option>
                                        <option value="out_of_stock">Out of Stock</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="stockTable" class="table table-hover table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Barcode</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Value</th>
                                    <th>Reorder Level</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0">Loading stock data...</p>
                </div>
            </div>
        </div>
    </div>
</div>



<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<style>
    #stockTable_wrapper .row {
        margin-bottom: 1rem;
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .status-in-stock {
        background-color: #d1f7c4;
        color: #0d6b26;
    }
    .status-low-stock {
        background-color: #fff3cd;
        color: #856404;
    }
    .status-out-of-stock {
        background-color: #f8d7da;
        color: #721c24;
    }
    .avatar-sm {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(function() {
    let stockTable;
    const storeId = {{ $store->id }};
    const apiUrl = '{{ route("manager.stock-data.data", ["store" => $store->id]) }}';

    function formatNumber(n, decimals = 2) {
        n = parseFloat(n) || 0;
        return n.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
    }

    function money(n) {
        return '৳ ' + formatNumber(n, 2);
    }

    function initializeDataTable() {
        stockTable = $('#stockTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: apiUrl,
                type: 'GET',
                data: function(d) {
                    // forward datatables parameters - controller handles mapping
                    d.search = d.search?.value || '';
                    d.length = d.length;
                    d.start = d.start;
                    // custom filters
                    d.filter_category = $('#filterCategory').val();
                    d.filter_status = $('#filterStatus').val();
                    d.search_product = $('#searchProduct').val();
                }
            },
            columns: [
                { data: 'stock_id', name: 'stock_id', className: 'text-center' },                 // ID
                { data: 'article', name: 'article', className: 'text-start' },                    // Product (article)
                { data: 'product_id', name: 'product_id', className: 'text-center' },             // SKU
                { data: 'barcode', name: 'barcode', className: 'text-center' },                   // Barcode
                { data: 'colors_id', name: 'colors_id', className: 'text-center' },               // Category (colors_id)
                { data: 'final_quantity', name: 'final_quantity', className: 'text-end',          // Quantity
                    render: function(data, type, row) {
                        return formatNumber(row.final_quantity, 2);
                    }
                },
                { data: 'purchase_price', name: 'purchase_price', className: 'text-end',         // Unit Price
                    render: function(data, type, row) { return money(row.purchase_price); }
                },
                { data: null, name: 'total_value', className: 'text-end',                        // Total Value (computed)
                    render: function(data, type, row) {
                        const qty = parseFloat(row.final_quantity) || 0;
                        const price = parseFloat(row.purchase_price) || 0;
                        return money(qty * price);
                    }
                },
                { data: 'in_order_queue', name: 'in_order_queue', className: 'text-center',      // Reorder / in queue
                    render: function(data) { return (parseFloat(data) || 0).toLocaleString(); }
                },
                { data: 'sync_status', name: 'sync_status', className: 'text-center',            // Status
                    render: function(data) {
                        let cls = 'status-in-stock';
                        let txt = data || 'Unknown';
                        if (data === 'pending') { cls = 'status-low-stock'; txt = 'Pending'; }
                        if (data === 'error')   { cls = 'status-out-of-stock'; txt = 'Error'; }
                        if (data === 'synced')  { cls = 'status-in-stock'; txt = 'Synced'; }
                        return `<span class="status-badge ${cls}">${txt}</span>`;
                    }
                },
                { data: null, orderable: false, searchable: false, className: 'text-center',    // Actions
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary btn-view-item" data-id="${row.stock_id}">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info btn-edit-item" data-id="${row.stock_id}">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-delete-item" data-id="${row.stock_id}">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, 'All']],
            dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            drawCallback: function() { updateSummaryStats(); },
            initComplete: function() { $('#loadingModal').modal('hide'); }
        });
    }

    function updateSummaryStats() {
        const api = stockTable.ajax.json();
        const pageRows = stockTable.rows({page: 'current'}).data();

        const totalItems = api?.recordsTotal || 0;
        let totalQuantity = 0;
        let totalValue = 0;
        let lowStockCount = 0;

        for (let i = 0; i < pageRows.length; i++) {
            const r = pageRows[i];
            const qty = parseFloat(r.final_quantity) || 0;
            const purchasePrice = parseFloat(r.purchase_price) || 0;
            totalQuantity += qty;
            totalValue += qty * purchasePrice;
            // treat in_order_queue as reorder threshold demonstration if present
            const reorder = parseFloat(r.in_order_queue) || 0;
            if (reorder > 0 && qty <= reorder) lowStockCount++;
        }

        $('#totalItems').text(totalItems.toLocaleString());
        $('#totalQuantity').text(totalQuantity.toLocaleString(undefined, { maximumFractionDigits: 2 }));
        $('#totalValue').text('৳ ' + totalValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#lowStock').text(lowStockCount.toLocaleString());
    }

    function loadCategories() {
        // Build category list from current page (best-effort)
        const seen = {};
        stockTable.rows().data().each(function(r) {
            const cat = r.colors_id || null;
            if (cat && !seen[cat]) {
                $('#filterCategory').append(`<option value="${cat}">Category ${cat}</option>`);
                seen[cat] = true;
            }
        });
    }

    // Event bindings
    $('#searchProduct').on('keyup', function() { // debounce
        clearTimeout(this._t);
        const q = this;
        this._t = setTimeout(function(){ stockTable.draw(); }, 300);
    });
    $('#filterCategory, #filterStatus').on('change', function(){ stockTable.draw(); });

    $('#refreshBtn').on('click', function(){
        $('#loadingModal').modal('show');
        stockTable.ajax.reload(() => { $('#loadingModal').modal('hide'); });
    });



    $(document).on('click', '.btn-edit-item', function() {
        const id = $(this).data('id');
        alert('Edit item ' + id);
    });

    $(document).on('click', '.btn-delete-item', function() {
        const id = $(this).data('id');
        if (!confirm('Delete item ' + id + '?')) return;
        alert('Implement delete for ' + id);
    });

    // Initialize
    $('#loadingModal').modal('show');
    initializeDataTable();

    // auto-refresh every 5 minutes
    setInterval(function(){ if (stockTable) stockTable.ajax.reload(null, false); }, 300000);
});
</script>

@endsection
