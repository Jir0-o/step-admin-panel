@extends('layouts.master')

@section('title', $store->name . ' - Sales Report || Step Shoe POS')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                        <div class="mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('stores.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                                    <i class="ri-arrow-left-line me-1"></i> Back
                                </a>
                                <div>
                                    <h4 class="mb-1">{{ $store->name }} - Sales Report</h4>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2">
                                            <i class="ri-check-line me-1"></i> Connected
                                        </span>
                                        <small class="text-muted">Live sales data from POS • Updated: <span id="lastUpdateTime">Just now</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button id="exportBtn" class="btn btn-success">
                                <i class="ri-download-line me-1"></i> Export CSV
                            </button>
                            <button id="refreshBtn" class="btn btn-primary">
                                <i class="ri-refresh-line me-1"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-summary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="summary-icon bg-primary">
                                <i class="ri-file-list-line"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" id="totalInvoices">0</h5>
                            <p class="text-muted mb-0">Total Invoices</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-summary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="summary-icon bg-success">
                                <i class="ri-money-dollar-circle-line"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" id="totalAmount">৳ 0</h5>
                            <p class="text-muted mb-0">Total Sales</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-summary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="summary-icon bg-info">
                                <i class="ri-hand-coin-line"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" id="totalPaid">৳ 0</h5>
                            <p class="text-muted mb-0">Total Paid</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-summary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="summary-icon bg-warning">
                                <i class="ri-profit-line"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" id="totalProfit">৳ 0</h5>
                            <p class="text-muted mb-0">Total Profit</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" class="form-control" id="fromDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" class="form-control" id="toDate">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ri-search-line"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput" 
                                       placeholder="Search invoice, customer, payment method...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="applyFilter">
                                <i class="ri-filter-line me-1"></i> Apply Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Sales Invoices</h5>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm" style="width: 120px;">
                                <span class="input-group-text">Show</span>
                                <select class="form-select form-select-sm" id="perPageSelect">
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100" selected>100</option>
                                    <option value="200">200</option>
                                    <option value="500">500</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Table Container -->
                    <div id="tableContainer">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover table-bordered mb-0" id="salesTable">
                                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Invoice ID</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Payment Method</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">VAT</th>
                                        <th class="text-end">Discount</th>
                                        <th class="text-end">Paid</th>
                                        <th class="text-end">Due</th>
                                        <th class="text-end">Profit</th>
                                        <th>Created By</th>
                                        <th>Items</th>
                                    </tr>
                                </thead>
                                <tbody id="salesTableBody">
                                    <!-- Loading placeholder -->
                                    <tr id="loadingRow">
                                        <td colspan="13" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 mb-0 text-muted">Loading sales data...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="text-muted me-3" id="pageInfo">Page 1 of 1</span>
                                    <span class="text-muted" id="totalInfo">Total: 0 invoices</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <nav aria-label="Page navigation" class="float-end">
                                    <ul class="pagination pagination-sm mb-0" id="pagination">
                                        <!-- Pagination will be generated here -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Summary Footer -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-light alert-summary">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-file-list-line fs-4 text-primary me-3"></i>
                                            <div>
                                                <strong id="footerTotalInvoices">0</strong>
                                                <p class="mb-0 text-muted small">Invoices</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-money-dollar-circle-line fs-4 text-success me-3"></i>
                                            <div>
                                                <strong id="footerTotalAmount">৳ 0.00</strong>
                                                <p class="mb-0 text-muted small">Total Amount</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-hand-coin-line fs-4 text-info me-3"></i>
                                            <div>
                                                <strong id="footerTotalPaid">৳ 0.00</strong>
                                                <p class="mb-0 text-muted small">Paid Amount</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-profit-line fs-4 text-warning me-3"></i>
                                            <div>
                                                <strong id="footerTotalProfit">৳ 0.00</strong>
                                                <p class="mb-0 text-muted small">Net Profit</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-summary {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    .card-summary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    .alert-summary {
        border: 1px solid #e9ecef;
        border-left: 4px solid #0d6efd;
        background: #f8f9fa;
    }
    #salesTable thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 10;
        border-bottom: 2px solid #dee2e6;
    }
    .table-responsive {
        scrollbar-width: thin;
        scrollbar-color: #6c757d #f8f9fa;
    }
    .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #6c757d;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #495057;
    }
    .status-paid { background-color: rgba(25, 135, 84, 0.05) !important; }
    .status-partial { background-color: rgba(255, 193, 7, 0.1) !important; }
    .status-unpaid { background-color: rgba(220, 53, 69, 0.05) !important; }
</style>

<script>
$(function() {
    const storeId = {{ $store->id }};
    const apiUrl = '{{ route("manager.sales-data.data", ["store" => $store->id]) }}';
    const exportUrl = '{{ route("manager.sales-data.export", ["store" => $store->id]) }}';

    // State
    let currentPage = 1;
    let perPage = 100;
    let totalPages = 1;
    let totalItems = 0;
    let lastUpdate = new Date();
    let searchTimeout = null;

    // UI Helpers
    function formatNumber(num, decimals = 2) {
        num = parseFloat(num) || 0;
        return num.toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }
    
    function formatCurrency(num) {
        return '৳ ' + formatNumber(num, 2);
    }
    
    function escapeHtml(text) {
        if (text == null) return '';
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }
    
    function updateTime() {
        const now = new Date();
        const diff = Math.floor((now - lastUpdate) / 1000);
        let text = 'Just now';
        if (diff > 60) {
            const mins = Math.floor(diff / 60);
            text = `${mins} minute${mins > 1 ? 's' : ''} ago`;
        }
        $('#lastUpdateTime').text(text);
    }

    // Load Data
    async function loadSalesData(page = 1, showLoading = true) {
        if (showLoading) {
            $('#loadingRow').show();
            $('#salesTableBody tr:not(#loadingRow)').hide();
        }
        
        currentPage = page;
        const startTime = Date.now();

        try {
            // Build query params
            const params = new URLSearchParams({
                start: (page - 1) * perPage,
                length: perPage,
                draw: Date.now()
            });

            // Date filters
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();
            
            if (fromDate) params.set('from_date', fromDate);
            if (toDate) params.set('to_date', toDate);

            // Search
            const searchVal = $('#searchInput').val().trim();
            if (searchVal) {
                params.set('search[value]', searchVal);
                params.set('search_sales', searchVal);
            }

            const response = await fetch(`${apiUrl}?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            const loadTime = Date.now() - startTime;

            if (data.error) {
                throw new Error(data.error || 'API returned error');
            }

            // Update state
            lastUpdate = new Date();
            updateTime();

            // Process data
            const apiData = data.data || [];
            const allDataSummary = data.all_data_summary || {};
            const currentPageSummary = data.current_page_summary || {};
            totalItems = data.recordsFiltered || data.recordsTotal || 0;
            totalPages = data.pagination?.last_page || Math.ceil(totalItems / perPage) || 1;

            // Update UI
            renderSalesTable(apiData);
            updatePagination();
            updateSummaryCards(allDataSummary); // TOP cards use ALL data summary
            updateCurrentPageSummary(currentPageSummary); // FOOTER uses current page summary
            updatePageInfo();

            // Log performance
            console.log(`Loaded ${apiData.length || 0} sales in ${loadTime}ms`);

        } catch (error) {
            console.error('Load error:', error);
            showError(`Failed to load data: ${error.message}`);
        } finally {
            $('#loadingRow').hide();
        }
    }

    // Render Table
    function renderSalesTable(items) {
        const tbody = $('#salesTableBody');
        tbody.empty();

        if (!items.length) {
            tbody.html(`
                <tr>
                    <td colspan="13" class="text-center py-5">
                        <i class="ri-inbox-line fs-1 text-muted"></i>
                        <p class="mt-2 mb-0">No sales data found</p>
                        <small class="text-muted">Try adjusting your filters</small>
                    </td>
                </tr>
            `);
            return;
        }

        let rowNumber = (currentPage - 1) * perPage + 1;

        items.forEach((item, index) => {
            const dueAmount = parseFloat(item.due_amount) || 0;
            const paidAmount = parseFloat(item.paid_amount) || 0;
            const totalAmount = parseFloat(item.total_amount) || 0;
            
            // Determine status
            let statusClass = 'status-paid';
            if (dueAmount > 0 && paidAmount > 0) {
                statusClass = 'status-partial';
            } else if (dueAmount > 0 && paidAmount === 0) {
                statusClass = 'status-unpaid';
            }

            const row = `
                <tr class="${statusClass}">
                    <td class="text-center">${rowNumber++}</td>
                    <td>
                        <strong>${escapeHtml(item.cart_id || '')}</strong>
                        ${item.trx_number ? `<br><small class="text-muted">${escapeHtml(item.trx_number)}</small>` : ''}
                    </td>
                    <td>${escapeHtml(item.cart_date || '')}</td>
                    <td>${escapeHtml(item.customer_mobile || '')}</td>
                    <td>${escapeHtml(item.payment_method || '')}</td>
                    <td class="text-end fw-bold">${formatCurrency(item.total_amount || 0)}</td>
                    <td class="text-end">${formatCurrency(item.vat_amount || 0)}</td>
                    <td class="text-end">${formatCurrency(item.discount || 0)}</td>
                    <td class="text-end fw-bold text-success">${formatCurrency(item.paid_amount || 0)}</td>
                    <td class="text-end fw-bold ${dueAmount > 0 ? 'text-danger' : 'text-muted'}">${formatCurrency(dueAmount)}</td>
                    <td class="text-end fw-bold text-info">${formatCurrency(item.net_profit || 0)}</td>
                    <td>
                        ${escapeHtml(item.created_by || '')}
                        ${item.waiter_name ? `<br><small class="text-muted">Waiter: ${escapeHtml(item.waiter_name)}</small>` : ''}
                        ${item.table_no ? `<br><small class="text-muted">Table: ${escapeHtml(item.table_no)}</small>` : ''}
                    </td>
                    <td>
                        ${item.items_html || '<small class="text-muted">No items</small>'}
                        ${item.items_count ? `<br><small class="text-muted">${item.items_count} items</small>` : ''}
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Update Pagination
    function updatePagination() {
        const pagination = $('#pagination');
        pagination.empty();

        if (totalPages <= 1) return;

        // Previous
        const prevDisabled = currentPage <= 1 ? 'disabled' : '';
        pagination.append(`
            <li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" data-page="${Math.max(1, currentPage - 1)}">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            </li>
        `);

        // Pages
        const maxVisible = 5;
        let start = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let end = Math.min(totalPages, start + maxVisible - 1);
        
        if (end - start + 1 < maxVisible) {
            start = Math.max(1, end - maxVisible + 1);
        }

        for (let i = start; i <= end; i++) {
            const active = i === currentPage ? 'active' : '';
            pagination.append(`
                <li class="page-item ${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

        // Next
        const nextDisabled = currentPage >= totalPages ? 'disabled' : '';
        pagination.append(`
            <li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" data-page="${Math.min(totalPages, currentPage + 1)}">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            </li>
        `);
    }

    // Update Summary Cards (TOP - ALL DATA)
    function updateSummaryCards(summary) {
        $('#totalInvoices').text(formatNumber(summary.total_items || 0, 0));
        $('#totalAmount').text(formatCurrency(summary.total_amount || 0));
        $('#totalPaid').text(formatCurrency(summary.total_paid || 0));
        $('#totalProfit').text(formatCurrency(summary.total_net_profit || 0));
    }

    // Update Current Page Summary (FOOTER - CURRENT PAGE ONLY)
    function updateCurrentPageSummary(summary) {
        $('#footerTotalInvoices').text(formatNumber(summary.total_items || 0, 0));
        $('#footerTotalAmount').text(formatCurrency(summary.total_amount || 0));
        $('#footerTotalPaid').text(formatCurrency(summary.total_paid || 0));
        $('#footerTotalProfit').text(formatCurrency(summary.total_net_profit || 0));
    }

    // Update Page Info
    function updatePageInfo() {
        const start = (currentPage - 1) * perPage + 1;
        const end = Math.min(currentPage * perPage, totalItems);
        
        $('#pageInfo').html(`
            Showing <strong>${start.toLocaleString()} - ${end.toLocaleString()}</strong> of <strong>${totalItems.toLocaleString()}</strong> invoices
        `);
        
        $('#totalInfo').text(`${totalItems.toLocaleString()} total invoices`);
    }

    // Show Error
    function showError(message) {
        $('#salesTableBody').html(`
            <tr>
                <td colspan="13" class="text-center py-5 text-danger">
                    <i class="ri-error-warning-line fs-1"></i>
                    <p class="mt-2 mb-0">${escapeHtml(message)}</p>
                    <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadSalesData(currentPage)">
                        <i class="ri-refresh-line me-1"></i> Retry
                    </button>
                </td>
            </tr>
        `);
    }

    // Export Data
    async function exportData() {
        try {
            // Build export URL with current filters
            const params = new URLSearchParams();
            
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();
            const searchVal = $('#searchInput').val().trim();
            
            if (fromDate) params.set('from_date', fromDate);
            if (toDate) params.set('to_date', toDate);
            if (searchVal) params.set('search', searchVal);
            
            const exportUrlWithParams = exportUrl + (params.toString() ? '?' + params.toString() : '');
            
            const response = await fetch(exportUrlWithParams, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error(`Export failed: ${response.status}`);
            }

            const blob = await response.blob();
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `sales_report_${storeId}_${new Date().toISOString().slice(0,10)}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            // Show success message
            const toast = `
                <div class="toast align-items-center text-bg-success border-0 show position-fixed bottom-0 end-0 m-3" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="ri-check-line me-2"></i> Export started successfully
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            $('body').append(toast);
            setTimeout(() => $('.toast').remove(), 3000);

        } catch (error) {
            console.error('Export error:', error);
            alert(`Export failed: ${error.message}`);
        }
    }

    // Event Listeners
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadSalesData(1);
        }, 500);
    });

    $('#clearSearch').on('click', function() {
        $('#searchInput').val('');
        loadSalesData(1);
    });

    $('#applyFilter').on('click', function() {
        loadSalesData(1);
    });

    $('#perPageSelect').on('change', function() {
        perPage = parseInt($(this).val());
        loadSalesData(1);
    });

    $('#refreshBtn').on('click', function() {
        $(this).prop('disabled', true);
        loadSalesData(1).finally(() => {
            $(this).prop('disabled', false);
        });
    });

    $('#exportBtn').on('click', exportData);

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page >= 1 && page <= totalPages) {
            loadSalesData(page);
        }
    });

    // Set default dates (last 30 days)
    const today = new Date().toISOString().split('T')[0];
    const lastMonth = new Date();
    lastMonth.setDate(lastMonth.getDate() - 30);
    const lastMonthStr = lastMonth.toISOString().split('T')[0];
    
    $('#toDate').val(today);
    $('#fromDate').val(lastMonthStr);

    // Initialize
    loadSalesData(1);
    
    // Auto-refresh every 5 minutes
    setInterval(() => {
        if (document.visibilityState === 'visible') {
            loadSalesData(currentPage, false);
        }
    }, 5 * 60 * 1000);

    // Update time every minute
    setInterval(updateTime, 60000);
});
</script>
@endsection