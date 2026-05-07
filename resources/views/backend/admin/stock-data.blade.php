@extends('layouts.master')

@section('title', $store->name . ' - Stock Report || Step Shoe POS')

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
                                    <h4 class="mb-1">{{ $store->name }} - Stock Report</h4>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2">
                                            <i class="ri-check-line me-1"></i> Connected
                                        </span>
                                        <small class="text-muted">Live data from POS • Updated: <span id="lastUpdateTime">Just now</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button id="exportBtn" class="btn btn-success">
                                <i class="ri-download-line me-1"></i> Export CSV
                            </button>
                            <button id="toggleColumnsBtn" class="btn btn-outline-secondary">
                                <i class="ri-eye-line me-1"></i> Columns
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
                                <i class="ri-box-3-line"></i>
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
            <div class="card card-summary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="summary-icon bg-success">
                                <i class="ri-stack-line"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" id="totalStock">0</h5>
                            <p class="text-muted mb-0">Total Stock</p>
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
                                <i class="ri-money-dollar-circle-line"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" id="totalValue">৳ 0</h5>
                            <p class="text-muted mb-0">Total Value</p>
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
                                <i class="ri-shopping-cart-line"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" id="totalSold">0</h5>
                            <p class="text-muted mb-0">Total Sold</p>
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
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ri-search-line"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput" 
                                       placeholder="Search product, article, barcode, category...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="categoryFilter">
                                <option value="">All Categories</option>
                                <!-- Categories will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                <!-- Types will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="stockStatusFilter">
                                <option value="">Stock Status</option>
                                <option value="in_stock">In Stock</option>
                                <option value="low_stock">Low Stock (< 10)</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
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
                        <h5 class="card-title mb-0">Stock Items</h5>
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
                            <table class="table table-hover table-bordered mb-0" id="stockTable">
                                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Product Name</th>
                                        <th>Barcode</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Material</th>
                                        <th>Brand</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th class="text-end">Purchase</th>
                                        <th class="text-end">Sold</th>
                                        <th class="text-end">Stock</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Total Value</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="stockTableBody">
                                    <!-- Loading placeholder -->
                                    <tr id="loadingRow">
                                        <td colspan="15" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 mb-0 text-muted">Loading stock data...</p>
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
                                    <span class="text-muted" id="totalInfo">Total: 0 items</span>
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
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-database-2-line fs-4 text-primary me-3"></i>
                                            <div>
                                                <strong id="footerTotalItems">0</strong>
                                                <p class="mb-0 text-muted small">Total Items</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-stack-line fs-4 text-success me-3"></i>
                                            <div>
                                                <strong id="footerTotalStock">0</strong>
                                                <p class="mb-0 text-muted small">Total Stock</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-money-dollar-circle-line fs-4 text-info me-3"></i>
                                            <div>
                                                <strong id="footerTotalValue">৳ 0.00</strong>
                                                <p class="mb-0 text-muted small">Total Value</p>
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

    <!-- Column Toggle Modal -->
    <div class="modal fade" id="columnsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Show/Hide Columns</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="toggleProduct" checked>
                        <label class="form-check-label" for="toggleProduct">Product Name</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="toggleArticle" checked>
                        <label class="form-check-label" for="toggleArticle">Barcode</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="toggleCategory" checked>
                        <label class="form-check-label" for="toggleCategory">Category</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="toggleType" checked>
                        <label class="form-check-label" for="toggleType">Type</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="toggleMaterial" checked>
                        <label class="form-check-label" for="toggleMaterial">Material</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="toggleBrand" checked>
                        <label class="form-check-label" for="toggleBrand">Brand</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="toggleColor" checked>
                        <label class="form-check-label" for="toggleColor">Color</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="toggleSize" checked>
                        <label class="form-check-label" for="toggleSize">Size</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="applyColumns">Apply</button>
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
    #stockTable thead th {
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
    .stock-in {
        background-color: rgba(25, 135, 84, 0.05) !important;
    }
    .stock-low {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    .stock-out {
        background-color: rgba(220, 53, 69, 0.05) !important;
    }
    .stock-status {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 6px;
    }
    .status-in { background-color: #28a745; }
    .status-low { background-color: #ffc107; }
    .status-out { background-color: #dc3545; }
    .column-hidden {
        display: none !important;
    }

    .live-table-loader-card {
        display: inline-block;
        min-width: 260px;
        max-width: 420px;
        padding: 14px 18px;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
    }
    .live-table-loader-head {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 700;
        color: #1f2937;
    }
    .live-table-loader-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        background: #eef4ff;
        animation: liveTableSpin .9s linear infinite;
    }
    .live-table-loader-detail {
        color: #6c757d;
        font-size: 12px;
        margin-top: 6px;
    }
    .live-table-progress {
        height: 4px;
        border-radius: 999px;
        background: #e9ecef;
        overflow: hidden;
        margin-top: 10px;
    }
    .live-table-progress span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #0d6efd, #20c997);
        transition: width .35s ease;
    }
    .table-shimmer {
        display: inline-block;
        height: 12px;
        width: 70%;
        border-radius: 999px;
        background: linear-gradient(90deg, #edf1f5 0%, #f8fafc 45%, #edf1f5 100%);
        background-size: 220% 100%;
        animation: tableShimmer 1.25s ease-in-out infinite;
    }
    @keyframes tableShimmer {
        0% { background-position: 100% 0; }
        100% { background-position: -100% 0; }
    }
    @keyframes liveTableSpin {
        to { transform: rotate(360deg); }
    }

</style>

<script>
$(function() {
    const storeId = {{ $store->id }};
    const apiUrl = '{{ route("manager.stock-data.data", ["store" => $store->id]) }}';
    const exportUrl = '{{ route("manager.stock-data.export", ["store" => $store->id]) }}';

    // State
    let currentPage = 1;
    let perPage = 100;
    let totalPages = 1;
    let totalItems = 0;
    let lastUpdate = new Date();
    let searchTimeout = null;
    let allCategories = new Set();
    let allTypes = new Set();
    let columnVisibility = {
        product: true,
        article: true,
        category: true,
        type: true,
        material: true,
        brand: true,
        color: true,
        size: true,
        purchase: true,
        sold: true,
        stock: true,
        price: true,
        value: true,
        status: true
    };

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


    let tableLoadingTimers = [];

    function clearTableLoadingTimers() {
        tableLoadingTimers.forEach((timerId) => clearTimeout(timerId));
        tableLoadingTimers = [];
    }

    function liveTableLoaderMarkup(colspan, title, detail, progress = 25) {
        const skeletonRows = Array.from({ length: 4 }).map(() => `
            <tr>
                ${Array.from({ length: colspan }).map((_, index) => `<td><span class="table-shimmer" style="width:${index % 3 === 0 ? 54 : index % 3 === 1 ? 78 : 64}%"></span></td>`).join('')}
            </tr>
        `).join('');

        return `
            <tr>
                <td colspan="${colspan}" class="text-center py-4">
                    <div class="live-table-loader-card">
                        <div class="live-table-loader-head">
                            <span class="live-table-loader-icon"><i class="ri-refresh-line"></i></span>
                            <span>${escapeHtml(title)}</span>
                        </div>
                        <div class="live-table-loader-detail">${escapeHtml(detail)}</div>
                        <div class="live-table-progress"><span style="width:${progress}%"></span></div>
                    </div>
                </td>
            </tr>
            ${skeletonRows}
        `;
    }

    function showTableLoading(colspan, dataLabel = 'data') {
        clearTableLoadingTimers();

        const stages = [
            ['Checking secure connection', 'Checking saved token before requesting ' + dataLabel + '.', 25, 0],
            ['Generating connection if needed', 'Refreshing token in the background if the session expired.', 45, 700],
            ['Connecting to store server', 'Opening the remote POS connection safely.', 68, 1500],
            ['Fetching latest ' + dataLabel, 'Waiting for the store server response.', 88, 2400],
        ];

        stages.forEach(([title, detail, progress, delay]) => {
            const timerId = setTimeout(() => {
                const tbodyId = colspan === 15 ? '#stockTableBody' : '#salesTableBody';
                $(tbodyId).html(liveTableLoaderMarkup(colspan, title, detail, progress));
            }, delay);
            tableLoadingTimers.push(timerId);
        });
    }

    function setButtonLoading(button, isLoading, text = 'Refreshing...') {
        const $button = $(button);

        if (!$button.data('original-html')) {
            $button.data('original-html', $button.html());
        }

        $button.prop('disabled', isLoading);

        if (isLoading) {
            $button.html(`<span class="spinner-border spinner-border-sm me-1"></span>${text}`);
        } else {
            $button.html($button.data('original-html'));
        }
    }

    function friendlyLoadError(message) {
        const text = String(message || '').trim();
        const lower = text.toLowerCase();

        if (lower.includes('401') || lower.includes('unauthorized')) {
            return 'Session expired. Please refresh again.';
        }

        if (lower.includes('timeout')) {
            return 'Store server is taking longer than usual.';
        }

        return text || 'Could not load data.';
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
    async function loadStockData(page = 1, showLoading = true) {
        if (showLoading) {
            showTableLoading(15, 'stock data');
            $('#pagination').empty();
            $('#pageInfo').text('Loading stock data...');
            $('#totalInfo').text('Please wait...');
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

            // Search
            const searchVal = $('#searchInput').val().trim();
            if (searchVal) {
                params.set('search[value]', searchVal);
                params.set('search_product', searchVal);
            }

            // Filters
            const category = $('#categoryFilter').val();
            const type = $('#typeFilter').val();
            const stockStatus = $('#stockStatusFilter').val();
            
            if (category) params.set('filter_category', category);
            if (type) params.set('filter_type', type);
            if (stockStatus) params.set('filter_status', stockStatus);

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
            const allDataSummary = data.all_data_summary || {}; // For TOP cards (ALL data)
            const currentPageSummary = data.current_page_summary || {}; // For FOOTER (current page)
            totalItems = data.recordsFiltered || data.recordsTotal || 0;
            totalPages = data.pagination?.last_page || Math.ceil(totalItems / perPage) || 1;

            // Extract categories and types from data
            extractCategoriesAndTypes(apiData);

            // Update UI
            renderStockTable(apiData);
            updatePagination();
            updateSummaryCards(allDataSummary); // TOP cards use ALL data summary
            updateCurrentPageSummary(currentPageSummary); // FOOTER uses current page summary
            updateFilterOptions();
            updatePageInfo();

            // Log performance
            console.log(`Loaded ${apiData.length || 0} items in ${loadTime}ms`);
            console.log('All Data Summary:', allDataSummary);
            console.log('Current Page Summary:', currentPageSummary);

        } catch (error) {
            console.error('Load error:', error);
            showError(friendlyLoadError(error.message));
        } finally {
            clearTableLoadingTimers();
        }
    }

    // Extract categories and types from data
    function extractCategoriesAndTypes(items) {
        allCategories.clear();
        allTypes.clear();
        
        items.forEach(item => {
            if (item.foot_ware_categories_name) {
                allCategories.add(item.foot_ware_categories_name);
            }
            if (item.type_name) {
                allTypes.add(item.type_name);
            }
        });
    }

    // Render Table
    function renderStockTable(items) {
        const tbody = $('#stockTableBody');
        tbody.empty();

        if (!items.length) {
            tbody.html(`
                <tr>
                    <td colspan="15" class="text-center py-5">
                        <i class="ri-inbox-line fs-1 text-muted"></i>
                        <p class="mt-2 mb-0">No stock data found</p>
                        <small class="text-muted">Try adjusting your search filters</small>
                    </td>
                </tr>
            `);
            return;
        }

        let rowNumber = (currentPage - 1) * perPage + 1;

        items.forEach((item, index) => {
            const stockQty = parseFloat(item.final_quantity) || 0;
            const unitPrice = parseFloat(item.sales_price) || 0;
            const purchased = parseFloat(item.total_purchased_quantity) || 0;
            const sold = parseFloat(item.total_sold_quantity) || 0;
            const value = stockQty * unitPrice;

            // Determine status
            let statusClass = 'stock-in';
            let statusDot = 'status-in';
            let statusText = 'In Stock';
            
            if (stockQty <= 0) {
                statusClass = 'stock-out';
                statusDot = 'status-out';
                statusText = 'Out of Stock';
            } else if (stockQty < 10) {
                statusClass = 'stock-low';
                statusDot = 'status-low';
                statusText = 'Low Stock';
            }

            const row = `
                <tr class="${statusClass}">
                    <td class="text-center">${rowNumber++}</td>
                    <td class="${!columnVisibility.product ? 'column-hidden' : ''}">${escapeHtml(item.product_material_name || '')}</td>
                    <td class="text-center ${!columnVisibility.article ? 'column-hidden' : ''}">${escapeHtml(item.barcode || '')}</td>
                    <td class="${!columnVisibility.category ? 'column-hidden' : ''}">${escapeHtml(item.foot_ware_categories_name || '')}</td>
                    <td class="${!columnVisibility.type ? 'column-hidden' : ''}">${escapeHtml(item.type_name || '')}</td>
                    <td class="${!columnVisibility.material ? 'column-hidden' : ''}">${escapeHtml(item.material_type_name || '')}</td>
                    <td class="${!columnVisibility.brand ? 'column-hidden' : ''}">${escapeHtml(item.brand_type_name || '')}</td>
                    <td class="text-center ${!columnVisibility.color ? 'column-hidden' : ''}">${escapeHtml(item.colors_name || '')}</td>
                    <td class="text-center ${!columnVisibility.size ? 'column-hidden' : ''}">${escapeHtml(item.size_name || '')}</td>
                    <td class="text-end ${!columnVisibility.purchase ? 'column-hidden' : ''}">${formatNumber(purchased, 2)}</td>
                    <td class="text-end ${!columnVisibility.sold ? 'column-hidden' : ''}">${formatNumber(sold, 2)}</td>
                    <td class="text-end fw-bold ${!columnVisibility.stock ? 'column-hidden' : ''}">${formatNumber(stockQty, 2)}</td>
                    <td class="text-end ${!columnVisibility.price ? 'column-hidden' : ''}">${formatCurrency(unitPrice)}</td>
                    <td class="text-end fw-bold ${!columnVisibility.value ? 'column-hidden' : ''}">${formatCurrency(value)}</td>
                    <td class="text-center ${!columnVisibility.status ? 'column-hidden' : ''}">
                        <span class="stock-status ${statusDot}"></span>
                        <small>${statusText}</small>
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
        $('#totalItems').text(formatNumber(summary.total_items || 0, 0));
        $('#totalStock').text(formatNumber(summary.total_quantity || 0, 2));
        $('#totalValue').text(formatCurrency(summary.total_value || 0));
        $('#totalSold').text(formatNumber(summary.total_sold || 0, 2));
    }

    // Update Current Page Summary (FOOTER - CURRENT PAGE ONLY)
    function updateCurrentPageSummary(summary) {
        $('#footerTotalItems').text(formatNumber(summary.total_items || 0, 0));
        $('#footerTotalStock').text(formatNumber(summary.total_quantity || 0, 2));
        $('#footerTotalValue').text(formatCurrency(summary.total_value || 0));
    }

    // Update Filter Options
    function updateFilterOptions() {
        // Update category filter
        const categorySelect = $('#categoryFilter');
        const currentCategory = categorySelect.val();
        
        // Store current options except the first one
        const currentOptions = categorySelect.find('option').slice(1);
        const currentValues = currentOptions.map((i, opt) => $(opt).val()).get();
        
        // Add new categories
        allCategories.forEach(cat => {
            if (!currentValues.includes(cat)) {
                categorySelect.append(`<option value="${cat}">${cat}</option>`);
            }
        });
        
        // Set back to current value if it exists
        if (currentCategory && Array.from(allCategories).includes(currentCategory)) {
            categorySelect.val(currentCategory);
        }
        
        // Update type filter
        const typeSelect = $('#typeFilter');
        const currentType = typeSelect.val();
        
        // Store current options except the first one
        const currentTypeOptions = typeSelect.find('option').slice(1);
        const currentTypeValues = currentTypeOptions.map((i, opt) => $(opt).val()).get();
        
        // Add new types
        allTypes.forEach(type => {
            if (!currentTypeValues.includes(type)) {
                typeSelect.append(`<option value="${type}">${type}</option>`);
            }
        });
        
        // Set back to current value if it exists
        if (currentType && Array.from(allTypes).includes(currentType)) {
            typeSelect.val(currentType);
        }
    }

    // Update Page Info
    function updatePageInfo() {
        const start = (currentPage - 1) * perPage + 1;
        const end = Math.min(currentPage * perPage, totalItems);
        
        $('#pageInfo').html(`
            Showing <strong>${start.toLocaleString()} - ${end.toLocaleString()}</strong> of <strong>${totalItems.toLocaleString()}</strong> items
        `);
        
        $('#totalInfo').text(`${totalItems.toLocaleString()} total items`);
    }

    // Show Error
    function showError(message) {
        $('#stockTableBody').html(`
            <tr>
                <td colspan="15" class="text-center py-5 text-danger">
                    <i class="ri-error-warning-line fs-1"></i>
                    <p class="mt-2 mb-0">${escapeHtml(message)}</p>
                    <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadStockData(currentPage)">
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
            
            const searchVal = $('#searchInput').val().trim();
            if (searchVal) {
                params.set('search', searchVal);
            }
            
            const category = $('#categoryFilter').val();
            if (category) {
                params.set('category', category);
            }
            
            const type = $('#typeFilter').val();
            if (type) {
                params.set('type', type);
            }
            
            const stockStatus = $('#stockStatusFilter').val();
            if (stockStatus) {
                params.set('status', stockStatus);
            }
            
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
            a.download = `stock_report_${storeId}_${new Date().toISOString().slice(0,10)}.csv`;
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

    // Apply Column Visibility
    function applyColumnVisibility() {
        columnVisibility = {
            product: $('#toggleProduct').is(':checked'),
            article: $('#toggleArticle').is(':checked'),
            category: $('#toggleCategory').is(':checked'),
            type: $('#toggleType').is(':checked'),
            material: $('#toggleMaterial').is(':checked'),
            brand: $('#toggleBrand').is(':checked'),
            color: $('#toggleColor').is(':checked'),
            size: $('#toggleSize').is(':checked'),
            purchase: true,
            sold: true,
            stock: true,
            price: true,
            value: true,
            status: true
        };
        
        // Re-render table to apply visibility
        loadStockData(currentPage, false);
    }

    // Event Listeners
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadStockData(1);
        }, 500);
    });

    $('#clearSearch').on('click', function() {
        $('#searchInput').val('');
        loadStockData(1);
    });

    $('#categoryFilter, #typeFilter, #stockStatusFilter').on('change', function() {
        loadStockData(1);
    });

    $('#perPageSelect').on('change', function() {
        perPage = parseInt($(this).val());
        loadStockData(1);
    });

    $('#refreshBtn').on('click', function() {
        setButtonLoading(this, true, 'Refreshing stock...');
        loadStockData(1).finally(() => {
            setButtonLoading(this, false);
        });
    });

    $('#exportBtn').on('click', exportData);

    $('#toggleColumnsBtn').on('click', function() {
        // Set checkbox states
        $('#toggleProduct').prop('checked', columnVisibility.product);
        $('#toggleArticle').prop('checked', columnVisibility.article);
        $('#toggleCategory').prop('checked', columnVisibility.category);
        $('#toggleType').prop('checked', columnVisibility.type);
        $('#toggleMaterial').prop('checked', columnVisibility.material);
        $('#toggleBrand').prop('checked', columnVisibility.brand);
        $('#toggleColor').prop('checked', columnVisibility.color);
        $('#toggleSize').prop('checked', columnVisibility.size);
        
        $('#columnsModal').modal('show');
    });

    $('#applyColumns').on('click', function() {
        applyColumnVisibility();
        $('#columnsModal').modal('hide');
    });

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page >= 1 && page <= totalPages) {
            loadStockData(page);
        }
    });

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            loadStockData(currentPage);
        }
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            exportData();
        }
        if (e.key === 'Escape') {
            $('#searchInput').val('').trigger('input');
        }
    });

    // Initialize
    loadStockData(1);
    
    // Auto-refresh every 5 minutes
    setInterval(() => {
        if (document.visibilityState === 'visible') {
            loadStockData(currentPage, false);
        }
    }, 5 * 60 * 1000);

    // Update time every minute
    setInterval(updateTime, 60000);
});
</script>
@endsection