@extends('layouts.master')

@section('title', 'Dashboard || Step Shoe Pos')
@section('body-class', '')

@section('content')
<div class="row m-0">
    <div class="col-md-3" data-aos="fade-up-right" data-aos-duration="2000">
        <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
                <div class="d-flex gap-3 align-items-center">
                    <div class="icon-box"><i class="ri-store-line"></i></div>
                    <div class="info-text">
                        <p class="m-0 text-muted">Total Store</p>
                        <h6 id="total-stores" class="m-0 fw-semibold">-</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3" data-aos="fade-up-left" data-aos-duration="2000">
        <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
                <div class="d-flex gap-3 align-items-center">
                    <div class="icon-box"><i class="ri-box-3-line"></i></div>
                    <div class="info-text">
                        <p class="m-0 text-muted">Total Product</p>
                        <h6 id="total-products" class="m-0 fw-semibold">-</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3" data-aos="fade-up-left" data-aos-duration="2000">
        <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
                <div class="d-flex gap-3 align-items-center">
                    <div class="icon-box"><i class="ri-target-line"></i></div>
                    <div class="info-text">
                        <p class="m-0 text-muted">Today's Target</p>
                        <h6 id="store-today-target" class="m-0 fw-semibold text-danger">-</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3" data-aos="fade-up-left" data-aos-duration="2000">
        <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
                <div class="d-flex gap-3 align-items-center">
                    <div class="icon-box"><i class="ri-line-chart-line"></i></div>
                    <div class="info-text">
                        <p class="m-0 text-muted">Today's Achievement</p>
                        <h6 id="store-today-achievement" class="m-0 fw-semibold text-primary">-</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <h5 class="mb-0">Store Information</h5>
        <div class="d-flex gap-2 align-items-center">
            <small class="text-muted">Server-side optimized overview cache</small>
            <button class="btn btn-outline-primary btn-sm" id="refresh-store-overview">
                <i class="ri-refresh-line me-1"></i> Refresh
            </button>
        </div>
    </div>

    <div class="card theme-shadow" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1200">
        <div class="table-responsive">
            <table class="table align-middle" id="stores-info-table">
                <thead>
                    <tr class="text-center">
                        <th class="bg-dark text-white store-code-sort-header" id="store-code-sort" role="button" tabindex="0" title="Sort by Store Code">
                            Store Code <i id="store-code-sort-icon" class="ri-arrow-up-line ms-1"></i>
                        </th>
                        <th class="bg-dark text-white">Store Name</th>
                        <th class="bg-dark text-white">Today's Target</th>
                        <th class="bg-dark text-white">Today's Achievement</th>
                        <th class="bg-dark text-white">Today's %</th>
                        <th class="bg-dark text-white">Salesmen</th>
                        <th class="bg-dark text-white">Today Discount</th>
                        <th class="bg-dark text-white">Month Discount</th>
                        <th class="bg-dark text-white">Monthly Target</th>
                        <th class="bg-dark text-white">Monthly Achieved</th>
                        <th class="bg-dark text-white">Monthly %</th>
                        <th class="bg-dark text-white">Yearly Target</th>
                        <th class="bg-dark text-white">Yearly Achieved</th>
                        <th class="bg-dark text-white">Yearly %</th>
                        <th class="bg-dark text-white">Status</th>
                        <th class="bg-dark text-white">Action</th>
                    </tr>
                </thead>
                <tbody id="stores-info-tbody">
                    <tr><td colspan="16" class="text-center py-5"><span class="badge bg-warning text-dark"><span class="spinner-border spinner-border-sm me-1"></span>Generating new token...</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="store-aggregates" class="mt-3"></div>
</div>

<div class="modal fade" id="storeDetailsModal" tabindex="-1" aria-labelledby="storeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="storeDetailsModalLabel">Store Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="storeDetailsContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading store data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="exportStoreDetailsBtn">
                    <i class="ri-download-line me-1"></i> Export
                </button>
                <button type="button" class="btn btn-success" id="printStoreDetailsBtn">
                    <i class="ri-printer-line me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="storeTargetsModal" tabindex="-1" aria-labelledby="storeTargetsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="storeTargetsLabel">Salesman Targets</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="storeTargetsSummaryCards" class="mb-3"></div>

                <div class="target-filter-bar border rounded bg-light p-3 mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label for="storeTargetsDateFrom" class="form-label small text-muted mb-1">From Date</label>
                            <input type="date" class="form-control form-control-sm" id="storeTargetsDateFrom">
                        </div>
                        <div class="col-md-3">
                            <label for="storeTargetsDateTo" class="form-label small text-muted mb-1">To Date</label>
                            <input type="date" class="form-control form-control-sm" id="storeTargetsDateTo">
                        </div>
                        <div class="col-md-auto">
                            <button type="button" class="btn btn-sm btn-primary" id="applyStoreTargetsFilter">
                                <i class="ri-filter-3-line me-1"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-auto">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="resetStoreTargetsFilter">Current Month</button>
                        </div>
                        <div class="col-md">
                            <small class="text-muted" id="storeTargetsFilterInfo">Default: current month data</small>
                        </div>
                    </div>
                </div>

                <h6 class="mb-2">Salesmen Summary</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped target-modal-table">
                        <thead>
                            <tr>
                                <th>Salesman</th>
                                <th class="text-center">Targets</th>
                                <th class="text-end">Total Target</th>
                                <th class="text-end">Total Achieved</th>
                                <th class="text-center">Achievement</th>
                                <th>Last Target Date</th>
                            </tr>
                        </thead>
                        <tbody id="storeTargetsSummaryBody">
                            <tr><td colspan="6" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                    <small class="text-muted" id="storeTargetsSummaryInfo">Waiting for target summary...</small>
                    <ul class="pagination pagination-sm mb-0" id="storeTargetsSummaryPagination"></ul>
                </div>

                <h6 class="mb-2">Target Details</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped target-modal-table">
                        <thead>
                            <tr>
                                <th>Salesman</th>
                                <th>Period</th>
                                <th class="text-end">Target</th>
                                <th class="text-end">Achieved</th>
                                <th class="text-center">Sales Count</th>
                                <th class="text-center">%</th>
                                <th>Status</th>
                                <th>Date Range</th>
                            </tr>
                        </thead>
                        <tbody id="storeTargetsDetailsBody">
                            <tr><td colspan="8" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted" id="storeTargetsDetailsInfo">Waiting for target details...</small>
                    <ul class="pagination pagination-sm mb-0" id="storeTargetsDetailsPagination"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(() => {
    const overviewUrl = @json(route('dashboard.overview'));
    const tokenStatusRoute = @json(route('dashboard.token-status'));
    const summaryRoute = @json(route('stores.fetch-summary', ['store' => '__ID__']));
    const storeOverviewRoute = @json(route('dashboard.overview.store', ['store' => '__ID__']));
    const stockRoute = @json(route('manager.stock-data.index', ['store' => '__ID__']));
    const salesRoute = @json(route('manager.sales.index', ['store' => '__ID__']));
    const TABLES = 'products,suppliers,cart_informtion,expense_details,banner_information,salesman_target_summary';
    const STORE_REFRESH_CONCURRENCY = 4;

    const $logBox = $('#store-aggregates').empty();
    let overviewRows = {};
    let tokenStateTimer = null;
    let tokenStateTimers = [];
    let detailLoadingTimers = [];
    let targetLoadingTimers = [];
    let targetSummaryRows = [];
    let targetDetailRows = [];
    let targetSummaryPage = 1;
    let targetDetailPage = 1;
    let currentTargetStore = { id: null, name: '' };
    let storeCodeSortDirection = 'asc';
    const TARGET_SUMMARY_PER_PAGE = 5;
    const TARGET_DETAIL_PER_PAGE = 8;


    function log(msg) {
        $logBox.append(`<div class="small text-muted">${msg}</div>`);
    }

    function num(v) {
        v = Number(v);
        return Number.isFinite(v) ? v : 0;
    }

    function taka(v) {
        return '৳ ' + num(v).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function integer(v) {
        return num(v).toLocaleString();
    }

    function formatDateInput(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function currentMonthDateRange() {
        const now = new Date();
        return {
            date_from: formatDateInput(new Date(now.getFullYear(), now.getMonth(), 1)),
            date_to: formatDateInput(new Date(now.getFullYear(), now.getMonth() + 1, 0)),
        };
    }

    function setDefaultTargetDateFilter() {
        const range = currentMonthDateRange();
        $('#storeTargetsDateFrom').val(range.date_from);
        $('#storeTargetsDateTo').val(range.date_to);
    }

    function selectedTargetFilter() {
        const range = currentMonthDateRange();
        return {
            date_from: $('#storeTargetsDateFrom').val() || range.date_from,
            date_to: $('#storeTargetsDateTo').val() || range.date_to,
        };
    }

    function targetFilterLabel(filter = selectedTargetFilter()) {
        return `${filter.date_from} to ${filter.date_to}`;
    }

    function targetMoney(value) {
        return `<span class="text-danger fw-semibold">${taka(value)}</span>`;
    }

    function achievedMoney(value) {
        return `<span class="text-primary fw-semibold">${taka(value)}</span>`;
    }

    function storeBannerCode(store) {
        return escapeHtml(store?.banner_code || store?.store_code || '-');
    }

    function rawStoreCode(store) {
        return String(store?.banner_code || store?.store_code || '').trim();
    }

    function compareStoreCodes(a, b) {
        const codeA = rawStoreCode(a);
        const codeB = rawStoreCode(b);

        // Rows whose remote Store Code has not loaded yet stay at the bottom.
        if (!codeA && !codeB) {
            return (a?._index ?? 0) - (b?._index ?? 0);
        }

        if (!codeA) return 1;
        if (!codeB) return -1;

        // Natural sorting: G2 < G10, G002 < G010, etc.
        let result = codeA.localeCompare(codeB, undefined, {
            numeric: true,
            sensitivity: 'base',
        });

        if (result === 0) {
            result = (a?._index ?? 0) - (b?._index ?? 0);
        }

        return storeCodeSortDirection === 'desc' ? -result : result;
    }

    function sortedOverviewRows() {
        return Object.values(overviewRows).slice().sort(compareStoreCodes);
    }

    function updateStoreCodeSortIcon() {
        const $icon = $('#store-code-sort-icon');
        $icon.removeClass('ri-arrow-up-line ri-arrow-down-line');
        $icon.addClass(storeCodeSortDirection === 'asc' ? 'ri-arrow-up-line' : 'ri-arrow-down-line');
    }

    function rerenderStoreRows() {
        const rows = sortedOverviewRows();

        if (!rows.length) {
            $('#stores-info-tbody').html('<tr><td colspan="16" class="text-center py-5 text-muted">No stores found.</td></tr>');
            updateStoreCodeSortIcon();
            return;
        }

        $('#stores-info-tbody').html(rows.map((row) => renderStoreRow(row)).join(''));
        updateStoreCodeSortIcon();
    }

    function summaryUrl(storeId) {
        return summaryRoute.replace('__ID__', storeId);
    }

    function getResultsFromPayload(payload) {
        if (!payload) return {};
        return payload.results || payload.data || payload.raw || payload;
    }

    function detailHtmlFromResults(results, storeName, storeId) {
        const cartInfo = results.cart_informtion || {};
        const targetInfo = results.salesman_target_summary || {};
        const expenseDetails = results.expense_details || {};
        const products = results.products || {};
        const suppliers = results.suppliers || {};
        const bannerInfo = results.banner_information || {};
        const todayDiscount = num(cartInfo.total_discount_today || 0);
        const monthDiscount = num(cartInfo.total_discount_month || 0);

        const totalAmount = num(cartInfo.total_amount || cartInfo.total_amount_year || 0);
        const totalCount = num(cartInfo.total_count || 0);
        const avgTransaction = totalCount > 0 ? totalAmount / totalCount : 0;
        const profitMargin = totalAmount > 0 ? (num(cartInfo.total_profit) / totalAmount) * 100 : 0;
        const expenseRatio = totalAmount > 0 ? (num(expenseDetails.total_amount) / totalAmount) * 100 : 0;
        const profitPerTransaction = totalCount > 0 ? num(cartInfo.total_profit) / totalCount : 0;
        const now = new Date();
        const start = new Date(now.getFullYear(), 0, 0);
        const dayOfYear = Math.max(1, Math.floor((now - start) / (1000 * 60 * 60 * 24)));
        const dailyAvg = dayOfYear > 0 ? num(cartInfo.total_amount_year) / dayOfYear : 0;

        return `
            <div class="store-details-content">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-light border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h5 class="mb-1">${escapeHtml(storeName || 'Store')}</h5>
                                        <p class="mb-0 text-muted">Store ID: ${storeId || '-'} | Code: ${escapeHtml(bannerInfo.banner_code || bannerInfo.banner?.banner_code || '-')} | Last Updated: ${now.toLocaleString()}</p>
                                    </div>
                                    <div class="badge bg-success px-3 py-2">
                                        <i class="ri-check-line me-1"></i> Connected
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12"><h5 class="mb-3"><i class="ri-team-line me-2"></i>Sales Target Summary</h5></div>

                    <div class="col-md-3 mb-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body text-center">
                                <small>Salesmen</small>
                                <h4 class="mt-2 mb-0">${integer(targetInfo.salesmen_count || 0)}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body text-center">
                                <small>Monthly Target</small>
                                <h4 class="mt-2 mb-0 text-danger">${taka(targetInfo.store_target_monthly || 0)}</h4>
                                <small class="text-primary fw-semibold">Achieved: ${taka(targetInfo.monthly_achievement || 0)}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body text-center">
                                <small>Monthly %</small>
                                <h4 class="mt-2 mb-0">${Number(targetInfo.monthly_percentage || 0).toFixed(2)}%</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body text-center">
                                <small>Yearly Target</small>
                                <h4 class="mt-2 mb-0 text-danger">${taka(targetInfo.store_target_yearly || 0)}</h4>
                                <small>${Number(targetInfo.yearly_percentage || 0).toFixed(2)}%</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12"><h5 class="mb-3"><i class="ri-line-chart-line me-2"></i>Sales Summary</h5></div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 bg-info text-white border-0 shadow-sm">
                            <div class="card-body text-center">
                                <small>Total Sales (Year)</small>
                                <h4 class="mt-2 mb-1">${taka(cartInfo.total_amount_year)}</h4>
                                <small>Daily Avg: ${taka(dailyAvg)}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 bg-success text-white border-0 shadow-sm">
                            <div class="card-body text-center">
                                <small>Today's Sales</small>
                                <h4 class="mt-2 mb-1">${taka(cartInfo.today_total_amount || cartInfo.total_amount_today)}</h4>
                                <small>Transactions: ${integer(cartInfo.today_total_count || 0)}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 bg-warning text-dark border-0 shadow-sm">
                            <div class="card-body text-center">
                                <small>Total Profit</small>
                                <h4 class="mt-2 mb-1">${taka(cartInfo.total_profit)}</h4>
                                <small>Margin: ${profitMargin.toFixed(1)}%</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12"><h5 class="mb-3"><i class="ri-store-2-line me-2"></i>Store Inventory</h5></div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="ri-shopping-bag-line me-2"></i>Products</h6></div>
                            <div class="card-body text-center">
                                <div class="display-6 text-primary mb-2">${integer(products.total_count)}</div>
                                <p class="mb-0">Total Products</p>
                                <small class="text-muted">Available in store</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-info text-white"><h6 class="mb-0"><i class="ri-truck-line me-2"></i>Suppliers</h6></div>
                            <div class="card-body text-center">
                                <div class="display-6 text-info mb-2">${integer(suppliers.total_count)}</div>
                                <p class="mb-0">Registered Suppliers</p>
                                <small class="text-muted">Active vendors</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-warning text-white"><h6 class="mb-0"><i class="ri-image-line me-2"></i>Store Banner</h6></div>
                            <div class="card-body text-center">
                                <div class="display-6 text-warning mb-2">${integer(bannerInfo.total_count)}</div>
                                <p class="mb-0">Active Banners</p>
                                ${bannerInfo.banner && bannerInfo.banner.banner_name ? `<div class="alert alert-light mt-3 mb-0"><strong>Current:</strong> ${bannerInfo.banner.banner_name}</div>` : '<p class="text-muted mb-0">No active banner</p>'}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12"><h5 class="mb-3"><i class="ri-money-dollar-circle-line me-2"></i>Expenses</h5></div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-danger text-white"><h6 class="mb-0"><i class="ri-bank-card-line me-2"></i>Total Expenses</h6></div>
                            <div class="card-body text-center">
                                <div class="display-6 text-danger mb-2">${taka(expenseDetails.total_amount)}</div>
                                <p class="mb-0">Total Expenses Recorded</p>
                                <small class="text-muted">${integer(expenseDetails.total_count)} expense entries</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-secondary text-white"><h6 class="mb-0"><i class="ri-pie-chart-line me-2"></i>Expense Ratio</h6></div>
                            <div class="card-body text-center">
                                <div class="display-6 text-secondary mb-2">${expenseRatio.toFixed(1)}%</div>
                                <p class="mb-2">Expenses to Sales Ratio</p>
                                <div class="progress" style="height: 10px;"><div class="progress-bar bg-danger" style="width: ${Math.min(Math.max(expenseRatio, 0), 100)}%"></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-12"><h5 class="mb-3"><i class="ri-dashboard-line me-2"></i>Quick Statistics</h5></div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card text-center shadow-sm border-0 h-100"><div class="card-body"><i class="ri-exchange-dollar-line fs-2 text-primary mb-2"></i><h5 class="mb-1">${integer(totalCount)}</h5><p class="mb-0 small text-muted">Total Transactions</p></div></div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card text-center shadow-sm border-0 h-100"><div class="card-body"><i class="ri-coins-line fs-2 text-success mb-2"></i><h5 class="mb-1">${taka(profitPerTransaction)}</h5><p class="mb-0 small text-muted">Profit / Transaction</p></div></div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card text-center shadow-sm border-0 h-100"><div class="card-body"><i class="ri-pie-chart-line fs-2 text-info mb-2"></i><h5 class="mb-1">${profitMargin.toFixed(1)}%</h5><p class="mb-0 small text-muted">Profit Margin</p></div></div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card text-center shadow-sm border-0 h-100"><div class="card-body"><i class="ri-bank-line fs-2 text-warning mb-2"></i><h5 class="mb-1">${taka(avgTransaction)}</h5><p class="mb-0 small text-muted">Avg Transaction</p></div></div>
                    </div>
                </div>
            </div>`;
    }

    function renderStoreDetailsFromSource(source, storeName, storeId) {
        clearDetailLoadingTimers();
        const results = getResultsFromPayload(source);
        $('#storeDetailsContent').html(detailHtmlFromResults(results, storeName, storeId));
    }

    function downloadStoreDetails() {
        const storeName = $('#storeDetailsModalLabel').text().replace(' - Store Details', '').trim() || 'Store';
        const text = $('#storeDetailsContent').text().replace(/\s+\n/g, '\n').trim();
        const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${storeName.replace(/\s+/g, '_')}_details.txt`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    }

    function printStoreDetails() {
        const printWindow = window.open('', '_blank');
        if (!printWindow) return;
        printWindow.document.write(`<!DOCTYPE html><html><head><title>Store Details</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"></head><body class="p-4">${$('#storeDetailsContent').html()}</body></html>`);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }



    function setStoreRefreshLoading(isLoading) {
        setRefreshButtonState('#refresh-store-overview', isLoading);
    }


    function setRefreshButtonState(selector, isLoading, loadingText = 'Refreshing stores...') {
        const buttons = $(selector);
        buttons.prop('disabled', isLoading);

        buttons.each(function () {
            const $button = $(this);

            if (!$button.data('original-html')) {
                $button.data('original-html', $button.html());
            }

            if (isLoading) {
                $button.html(`<span class="spinner-border spinner-border-sm me-1"></span>${loadingText}`);
            } else {
                $button.html($button.data('original-html'));
            }
        });
    }

    function clearTokenStateTimers() {
        if (tokenStateTimer) {
            clearTimeout(tokenStateTimer);
            tokenStateTimer = null;
        }

        tokenStateTimers.forEach((timerId) => clearTimeout(timerId));
        tokenStateTimers = [];
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function friendlyFailureMessage(message) {
        const text = String(message || '').trim();
        const lower = text.toLowerCase();

        if (lower.includes('401') || lower.includes('unauthorized')) {
            return 'Session expired. Please refresh again.';
        }

        if (lower.includes('timeout')) {
            return 'Store server is taking too long.';
        }

        if (lower.includes('credential')) {
            return 'Store login credential needs attention.';
        }

        return text || 'Could not load store data.';
    }

    const STATUS_STEPS = ['Token', 'Refresh', 'Connect', 'Fetch', 'Ready'];
    const STATUS_META = {
        preparing: {
            title: 'Preparing store list',
            detail: 'Getting stores ready for background checks.',
            icon: 'ri-list-check-2',
            progress: 10,
            step: 0,
            active: true,
        },
        checking: {
            title: 'Checking saved token',
            detail: 'Looking for an existing secure connection.',
            icon: 'ri-search-eye-line',
            progress: 22,
            step: 0,
            active: true,
        },
        expired: {
            title: 'Token expired',
            detail: 'Old connection found. A new token is needed.',
            icon: 'ri-lock-unlock-line',
            progress: 35,
            step: 0,
            active: false,
        },
        refreshing: {
            title: 'Generating new token',
            detail: 'Creating a fresh secure connection.',
            icon: 'ri-refresh-line',
            progress: 55,
            step: 1,
            active: true,
            spin: true,
        },
        connecting: {
            title: 'Connecting to store',
            detail: 'Opening the remote store connection.',
            icon: 'ri-wifi-line',
            progress: 70,
            step: 2,
            active: true,
        },
        fetching: {
            title: 'Fetching summary',
            detail: 'Collecting sales, stock and target data.',
            icon: 'ri-database-2-line',
            progress: 86,
            step: 3,
            active: true,
        },
        ready: {
            title: 'Ready',
            detail: 'Latest store data loaded.',
            icon: 'ri-check-line',
            progress: 100,
            step: 4,
            active: false,
        },
        failed: {
            title: 'Could not load',
            detail: 'Please refresh this store again.',
            icon: 'ri-error-warning-line',
            progress: 100,
            step: 4,
            active: false,
        },
    };

    function liveStatusMarkup(state, detail = null) {
        const meta = STATUS_META[state] || STATUS_META.preparing;
        const dots = meta.active ? '<span class="typing-dots"><i></i><i></i><i></i></span>' : '';
        const stepDots = STATUS_STEPS.map((label, index) => {
            const active = index <= meta.step ? 'active' : '';
            return `<span class="${active}" title="${label}"></span>`;
        }).join('');

        return `
            <div class="live-status-card status-${state}">
                <div class="live-status-head">
                    <span class="live-status-icon ${meta.spin ? 'status-spin' : ''}"><i class="${meta.icon}"></i></span>
                    <span class="live-status-title">${escapeHtml(meta.title)}${dots}</span>
                </div>
                <div class="live-status-detail">${escapeHtml(detail || meta.detail)}</div>
                <div class="live-progress"><span style="width:${meta.progress}%"></span></div>
                <div class="live-step-dots">${stepDots}</div>
            </div>`;
    }

    function loadingCell(width = 70) {
        return `<span class="data-skeleton" style="width:${width}%"></span>`;
    }

    function loadingRowMessage(state = 'preparing') {
        return `<tr><td colspan="16" class="text-center py-4">${liveStatusMarkup(state)}</td></tr>`;
    }

    function tokenExpiredBadge() {
        return liveStatusMarkup('expired');
    }

    function generatingTokenBadge() {
        return liveStatusMarkup('refreshing');
    }


    function clearDetailLoadingTimers() {
        detailLoadingTimers.forEach((timerId) => clearTimeout(timerId));
        detailLoadingTimers = [];
    }

    function detailLoadingMarkup(state = 'checking') {
        return `
            <div class="modal-live-loader">
                <div class="d-flex justify-content-center mb-3">${liveStatusMarkup(state)}</div>
                <div class="row g-3">
                    ${[1, 2, 3].map(() => `
                        <div class="col-md-4">
                            <div class="live-card-skeleton">
                                <span class="skeleton-line line-sm mb-3"></span>
                                <span class="skeleton-line line-lg mb-2"></span>
                                <span class="skeleton-line line-md"></span>
                            </div>
                        </div>
                    `).join('')}
                </div>
                <div class="small text-muted text-center mt-3">Background: token check → secure connection → summary request → view preparation</div>
            </div>`;
    }

    function startDetailLoading(selector) {
        clearDetailLoadingTimers();
        const stages = [
            ['checking', 0],
            ['refreshing', 650],
            ['connecting', 1450],
            ['fetching', 2300],
        ];

        stages.forEach(([state, delay]) => {
            const timerId = setTimeout(() => {
                $(selector).html(detailLoadingMarkup(state));
            }, delay);
            detailLoadingTimers.push(timerId);
        });
    }

    function targetLoadingRow(colspan = 8, state = 'checking', detail = null) {
        return `<tr><td colspan="${colspan}" class="text-center py-4">${liveStatusMarkup(state, detail)}</td></tr>`;
    }

    function clearTargetLoadingTimers() {
        targetLoadingTimers.forEach((timerId) => clearTimeout(timerId));
        targetLoadingTimers = [];
    }

    function setTargetLoadingState(state, detail = null) {
        $('#storeTargetsSummaryBody').html(targetLoadingRow(6, state, detail));
        $('#storeTargetsDetailsBody').html(targetLoadingRow(8, state, detail));
        $('#storeTargetsSummaryInfo').text('Loading target summary...');
        $('#storeTargetsDetailsInfo').text('Loading target details...');
        $('#storeTargetsSummaryPagination, #storeTargetsDetailsPagination').empty();
    }

    function startTargetLoading(storeName = 'Store') {
        clearTargetLoadingTimers();
        targetSummaryRows = [];
        targetDetailRows = [];
        targetSummaryPage = 1;
        targetDetailPage = 1;
        $('#storeTargetsSummaryCards').html(`
            <div class="modal-live-loader mb-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="mb-1">${escapeHtml(storeName)} target loading</h6>
                        <small class="text-muted">Background activity is visible below while POS data is loading.</small>
                    </div>
                    <span class="live-mini-pill"><i class="ri-radar-line"></i> Live status</span>
                </div>
                <div class="row g-3 mt-1">
                    ${[1, 2, 3, 4].map(() => `
                        <div class="col-md-3">
                            <div class="live-card-skeleton">
                                <span class="skeleton-line line-sm mb-3"></span>
                                <span class="skeleton-line line-lg"></span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `);

        const stages = [
            ['checking', 0, 'Checking saved token before loading target tables.'],
            ['refreshing', 750, 'Generating a fresh token if the previous session expired.'],
            ['connecting', 1600, 'Connecting to the remote store target API.'],
            ['fetching', 2500, 'Fetching salesman summary and target details.'],
        ];

        stages.forEach(([state, delay, detail]) => {
            const timerId = setTimeout(() => setTargetLoadingState(state, detail), delay);
            targetLoadingTimers.push(timerId);
        });
    }

    function paginateRows(rows, page, perPage) {
        const start = (page - 1) * perPage;
        return rows.slice(start, start + perPage);
    }

    function renderTargetPager(selector, type, page, totalRows, perPage) {
        const totalPages = Math.max(1, Math.ceil(totalRows / perPage));
        const $pager = $(selector).empty();

        if (totalPages <= 1) return;

        const prevDisabled = page <= 1 ? 'disabled' : '';
        const nextDisabled = page >= totalPages ? 'disabled' : '';

        $pager.append(`<li class="page-item ${prevDisabled}"><a href="#" class="page-link target-page-link" data-target-table="${type}" data-page="${Math.max(1, page - 1)}"><i class="ri-arrow-left-s-line"></i></a></li>`);

        const maxVisible = 5;
        let start = Math.max(1, page - Math.floor(maxVisible / 2));
        let end = Math.min(totalPages, start + maxVisible - 1);

        if (end - start + 1 < maxVisible) {
            start = Math.max(1, end - maxVisible + 1);
        }

        for (let p = start; p <= end; p++) {
            const active = p === page ? 'active' : '';
            $pager.append(`<li class="page-item ${active}"><a href="#" class="page-link target-page-link" data-target-table="${type}" data-page="${p}">${p}</a></li>`);
        }

        $pager.append(`<li class="page-item ${nextDisabled}"><a href="#" class="page-link target-page-link" data-target-table="${type}" data-page="${Math.min(totalPages, page + 1)}"><i class="ri-arrow-right-s-line"></i></a></li>`);
    }

    function renderTargetSummaryPage() {
        const rows = paginateRows(targetSummaryRows, targetSummaryPage, TARGET_SUMMARY_PER_PAGE);
        const html = rows.map((s) => `
            <tr>
                <td>
                    <strong>${escapeHtml(s.name || 'N/A')}</strong><br>
                    <small class="text-muted">${escapeHtml(s.office_id || '')}</small>
                </td>
                <td class="text-center">${integer(s.targets_count || 0)}</td>
                <td class="text-end">${targetMoney(s.total_target || 0)}</td>
                <td class="text-end">${achievedMoney(s.total_achieved || 0)}</td>
                <td class="text-center">${Number(s.percent || 0).toFixed(2)}%</td>
                <td>${escapeHtml(s.last_date || '-')}</td>
            </tr>
        `).join('');

        $('#storeTargetsSummaryBody').html(
            html || '<tr><td colspan="6" class="text-center py-4">No salesman target found.</td></tr>'
        );

        const total = targetSummaryRows.length;
        const from = total ? ((targetSummaryPage - 1) * TARGET_SUMMARY_PER_PAGE) + 1 : 0;
        const to = Math.min(targetSummaryPage * TARGET_SUMMARY_PER_PAGE, total);
        $('#storeTargetsSummaryInfo').text(total ? `Showing ${from}-${to} of ${total} salesmen` : 'No salesman target found.');
        renderTargetPager('#storeTargetsSummaryPagination', 'summary', targetSummaryPage, total, TARGET_SUMMARY_PER_PAGE);
    }

    function renderTargetDetailsPage() {
        const rows = paginateRows(targetDetailRows, targetDetailPage, TARGET_DETAIL_PER_PAGE);
        const html = rows.map((row) => `
            <tr>
                <td>
                    <strong>${escapeHtml(row.salesman_name || 'N/A')}</strong><br>
                    <small class="text-muted">${escapeHtml(row.office_id || '')}</small>
                </td>
                <td>${escapeHtml(targetPeriodLabel(row))}</td>
                <td class="text-end">${targetMoney(row.target_amount || 0)}</td>
                <td class="text-end">${achievedMoney(row.achieved_amount || 0)}</td>
                <td class="text-center">${integer(row.sales_count || 0)}</td>
                <td class="text-center">${Number(row.percent || 0).toFixed(2)}%</td>
                <td><span class="badge bg-primary">${escapeHtml(row.status || '-')}</span></td>
                <td>
                    <small>${escapeHtml(row.filter_start_date || row.start_date || '-')}<br>to ${escapeHtml(row.filter_end_date || row.end_date || '-')}</small>
                </td>
            </tr>
        `).join('');

        $('#storeTargetsDetailsBody').html(
            html || '<tr><td colspan="8" class="text-center py-4">No target details found.</td></tr>'
        );

        const total = targetDetailRows.length;
        const from = total ? ((targetDetailPage - 1) * TARGET_DETAIL_PER_PAGE) + 1 : 0;
        const to = Math.min(targetDetailPage * TARGET_DETAIL_PER_PAGE, total);
        $('#storeTargetsDetailsInfo').text(total ? `Showing ${from}-${to} of ${total} target rows` : 'No target details found.');
        renderTargetPager('#storeTargetsDetailsPagination', 'details', targetDetailPage, total, TARGET_DETAIL_PER_PAGE);
    }

    function renderTargetsModal(payload, storeName) {
        clearTargetLoadingTimers();

        const detail = payload?.salesman_target_details || {};
        $('#storeTargetsFilterInfo').text(`Showing ${targetFilterLabel({ date_from: detail.date_from || selectedTargetFilter().date_from, date_to: detail.date_to || selectedTargetFilter().date_to })} data`);
        targetSummaryRows = Array.isArray(detail.summaries) ? detail.summaries : [];
        targetDetailRows = Array.isArray(detail.targets) ? detail.targets : [];
        targetSummaryPage = 1;
        targetDetailPage = 1;

        $('#storeTargetsSummaryCards').html(`
            <div class="row">
                <div class="col-md-3 mb-2">
                    <div class="card border-0 shadow-sm"><div class="card-body text-center">
                        <small>Salesmen</small>
                        <h5 class="mb-0">${integer(detail.salesmen_count || 0)}</h5>
                    </div></div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="card border-0 shadow-sm"><div class="card-body text-center">
                        <small>Total Target</small>
                        <h5 class="mb-0 text-danger">${taka(detail.total_target || 0)}</h5>
                    </div></div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="card border-0 shadow-sm"><div class="card-body text-center">
                        <small>Total Achieved</small>
                        <h5 class="mb-0 text-primary">${taka(detail.total_achieved || 0)}</h5>
                    </div></div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="card border-0 shadow-sm"><div class="card-body text-center">
                        <small>Overall %</small>
                        <h5 class="mb-0">${Number(detail.overall_percent || 0).toFixed(2)}%</h5>
                    </div></div>
                </div>
            </div>
        `);

        renderTargetSummaryPage();
        renderTargetDetailsPage();
    }

    function showTargetFailure(message = 'Failed to load target data.') {
        clearTargetLoadingTimers();
        const friendlyMessage = friendlyFailureMessage(message);
        $('#storeTargetsSummaryCards').html('');
        $('#storeTargetsSummaryBody').html(targetLoadingRow(6, 'failed', friendlyMessage));
        $('#storeTargetsDetailsBody').html(targetLoadingRow(8, 'failed', friendlyMessage));
        $('#storeTargetsSummaryInfo').text('Target summary could not be loaded.');
        $('#storeTargetsDetailsInfo').text('Target details could not be loaded.');
        $('#storeTargetsSummaryPagination, #storeTargetsDetailsPagination').empty();
    }



    function scheduleTokenState(selector, state, delay) {
        const timerId = setTimeout(() => {
            const $target = $(selector);

            if ($target.length) {
                $target.html(liveStatusMarkup(state));
            }
        }, delay);

        tokenStateTimers.push(timerId);
    }

    function renderTokenStatusRows(statuses, forceRefresh = false) {
        clearTokenStateTimers();

        const rows = (statuses || []).map((store) => {
            const willRefresh = forceRefresh || store.needs_refresh;
            const firstState = willRefresh ? 'expired' : 'checking';
            const statusSelector = `#store-token-status-${store.id}`;

            if (willRefresh) {
                scheduleTokenState(statusSelector, 'refreshing', 650);
                scheduleTokenState(statusSelector, 'connecting', 1550);
                scheduleTokenState(statusSelector, 'fetching', 2450);
            } else {
                scheduleTokenState(statusSelector, 'connecting', 650);
                scheduleTokenState(statusSelector, 'fetching', 1450);
            }

            return `
            <tr class="table-light live-status-row">
                <td class="text-center">${loadingCell(50)}</td>
                <td class="text-center fw-semibold">${escapeHtml(store.name || '-')}</td>
                <td class="text-center">${loadingCell(66)}</td>
                <td class="text-center">${loadingCell(72)}</td>
                <td class="text-center">${loadingCell(74)}</td>
                <td class="text-center">${loadingCell(44)}</td>
                <td class="text-center">${loadingCell(62)}</td>
                <td class="text-center">${loadingCell(66)}</td>
                <td class="text-center">${loadingCell(76)}</td>
                <td class="text-center">${loadingCell(76)}</td>
                <td class="text-center">${loadingCell(42)}</td>
                <td class="text-center">${loadingCell(76)}</td>
                <td class="text-center">${loadingCell(76)}</td>
                <td class="text-center">${loadingCell(42)}</td>
                <td class="text-center" id="store-token-status-${store.id}">${liveStatusMarkup(firstState)}</td>
                <td class="text-center"><span class="text-muted small">Waiting</span></td>
            </tr>`;
        }).join('');

        $('#stores-info-tbody').html(rows || loadingRowMessage('refreshing'));
    }

    function tableRowId(storeId) {
        return `store-info-row-${storeId}`;
    }

    function storeOverviewUrl(storeId, forceRefresh = false) {
        const url = storeOverviewRoute.replace('__ID__', encodeURIComponent(storeId));
        return forceRefresh ? `${url}?refresh=1` : url;
    }

    function safeStoreNameAttr(store) {
        return escapeHtml(store?.name || '');
    }

    function buildClientOverview() {
        const rows = Object.values(overviewRows).sort((a, b) => (a._index || 0) - (b._index || 0));
        const totals = {
            store_count: rows.length,
            products: 0,
            target_today: 0,
            achievement_today: 0,
            failed_stores: 0,
            pending_stores: 0,
        };

        rows.forEach((row) => {
            if (row.pending) {
                totals.pending_stores += 1;
                return;
            }

            if (!row.ok) {
                totals.failed_stores += 1;
                return;
            }

            totals.products += num(row.products_total);
            totals.target_today += num(row.store_target_today);
            totals.achievement_today += num(row.today_achievement);
        });

        return totals;
    }

    function renderStoreTotals() {
        const totals = buildClientOverview();
        $('#total-stores').text(integer(totals.store_count || 0));
        $('#total-products').text(integer(totals.products || 0));
        $('#store-today-target').text(taka(totals.target_today || 0));
        $('#store-today-achievement').text(taka(totals.achievement_today || 0));
    }

    function renderStoreRow(store) {
        const statusBadge = store.pending
            ? liveStatusMarkup('fetching', 'Waiting to fetch this store only.')
            : (store.ok ? liveStatusMarkup('ready') : liveStatusMarkup('failed', friendlyFailureMessage(store.message)));
        const safeName = safeStoreNameAttr(store);

        return `
            <tr id="${tableRowId(store.id)}" data-store-id="${store.id}">
                <td class="text-center fw-semibold">${store.ok ? storeBannerCode(store) : '-'}</td>
                <td class="text-center">${escapeHtml(store.name || '-')}</td>
                <td class="text-center">${store.ok ? targetMoney(store.store_target_today) : '-'}</td>
                <td class="text-center">${store.ok ? achievedMoney(store.today_achievement) : '-'}</td>
                <td class="text-center">${store.ok ? (Number(store.today_percentage || 0).toFixed(2) + '%') : '-'}</td>
                <td class="text-center">${store.ok ? integer(store.salesmen_count) : '-'}</td>
                <td class="text-center">${store.ok ? taka(store.discount_today) : '-'}</td>
                <td class="text-center">${store.ok ? taka(store.discount_month) : '-'}</td>
                <td class="text-center">${store.ok ? targetMoney(store.store_target_monthly) : '-'}</td>
                <td class="text-center">${store.ok ? achievedMoney(store.monthly_achievement) : '-'}</td>
                <td class="text-center">${store.ok ? (Number(store.monthly_percentage || 0).toFixed(2) + '%') : '-'}</td>
                <td class="text-center">${store.ok ? targetMoney(store.store_target_yearly) : '-'}</td>
                <td class="text-center">${store.ok ? achievedMoney(store.yearly_achievement) : '-'}</td>
                <td class="text-center">${store.ok ? (Number(store.yearly_percentage || 0).toFixed(2) + '%') : '-'}</td>
                <td class="text-center js-row-status">${statusBadge}</td>
                <td class="text-center">
                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                        <button class="btn btn-sm btn-outline-secondary js-refresh-store" data-store-id="${store.id}" data-store-name="${safeName}"><i class="ri-refresh-line"></i></button>
                        <button class="btn btn-sm btn-success js-view-store" data-store-id="${store.id}" data-store-name="${safeName}">View</button>
                        <a class="btn btn-sm btn-info js-report-link" data-loading-label="Opening stock..." href="${stockRoute.replace('__ID__', store.id)}">Stock</a>
                        <a class="btn btn-sm btn-primary js-report-link" data-loading-label="Opening sales..." href="${salesRoute.replace('__ID__', store.id)}">Sales</a>
                        <button class="btn btn-sm btn-warning js-view-targets" data-store-id="${store.id}" data-store-name="${safeName}">Targets</button>
                    </div>
                </td>
            </tr>`;
    }

    function renderStoreTable(stores) {
        overviewRows = {};
        $logBox.empty();

        if (!stores.length) {
            $('#stores-info-tbody').html('<tr><td colspan="16" class="text-center py-5 text-muted">No stores found.</td></tr>');
            renderStoreTotals();
            updateStoreCodeSortIcon();
            return;
        }

        stores.forEach((store, index) => {
            const row = { ...store, _index: index };
            overviewRows[String(row.id)] = row;

            if (row.ok) log(`Store ${row.name} synced from cache`);
            else if (row.pending) log(`Store ${row.name} waiting for live refresh`);
            else log(`Store ${row.name} failed: ${row.message || 'Unknown error'}`);
        });

        // Auto-sort immediately if Store Codes are already available from cache.
        rerenderStoreRows();
        renderStoreTotals();
    }

    function setStoreRowLoading(storeId) {
        const $row = $(`#${tableRowId(storeId)}`);
        $row.find('.js-row-status').html(liveStatusMarkup('fetching', 'Refreshing this store only.'));
        $row.find('.js-refresh-store').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
    }

    function updateStoreRow(store) {
        const key = String(store.id);
        const previous = overviewRows[key] || {};
        const index = Number.isInteger(previous._index) ? previous._index : Object.keys(overviewRows).length;
        const row = { ...previous, ...store, pending: false, _index: index };
        overviewRows[key] = row;

        if (row.ok) log(`Store ${row.name} refreshed`);
        else log(`Store ${row.name} failed: ${row.message || 'Unknown error'}`);

        // banner_code/store_code arrives asynchronously from the overview API.
        // Re-rendering here makes G001, G002, G003... automatically fall into order
        // as soon as each real code becomes available.
        rerenderStoreRows();
        renderStoreTotals();
    }

    function markStoreRowFailed(storeId, message) {
        const previous = overviewRows[String(storeId)] || { id: storeId, name: `Store #${storeId}` };
        updateStoreRow({
            ...previous,
            ok: false,
            pending: false,
            message: friendlyFailureMessage(message || 'Failed to load this store.'),
        });
    }

    function refreshStoreRow(storeId, forceRefresh = false) {
        setStoreRowLoading(storeId);

        return $.ajax({
            url: storeOverviewUrl(storeId, forceRefresh),
            method: 'GET',
            dataType: 'json',
            timeout: 90000,
            headers: { Accept: 'application/json' },
        })
        .done((response) => updateStoreRow(response?.data || {}))
        .fail((jqXHR, textStatus) => {
            markStoreRowFailed(storeId, textStatus || jqXHR?.responseJSON?.message || 'Failed to refresh this store.');
        });
    }

    function refreshStoreRows(stores, forceRefresh = false) {
        const queue = (stores || []).slice();
        const deferred = $.Deferred();
        let active = 0;
        let completed = 0;
        const total = queue.length;

        if (!total) {
            deferred.resolve();
            return deferred.promise();
        }

        function next() {
            if (completed >= total) {
                deferred.resolve();
                return;
            }

            while (active < STORE_REFRESH_CONCURRENCY && queue.length) {
                const store = queue.shift();
                active += 1;
                refreshStoreRow(store.id, forceRefresh)
                    .always(() => {
                        active -= 1;
                        completed += 1;
                        next();
                    });
            }
        }

        next();
        return deferred.promise();
    }

    function loadOverview(forceRefresh = false) {
        const quickParam = forceRefresh ? 'quick=1&refresh=1' : 'quick=1';
        const url = `${overviewUrl}?${quickParam}`;
        const tokenStatusUrl = forceRefresh ? `${tokenStatusRoute}?refresh=1` : tokenStatusRoute;

        setStoreRefreshLoading(true);
        clearTokenStateTimers();
        $('#stores-info-tbody').html(loadingRowMessage('preparing'));

        $.getJSON(tokenStatusUrl)
            .done((response) => {
                renderTokenStatusRows(response?.data || [], forceRefresh);
            })
            .fail(() => {
                $('#stores-info-tbody').html(loadingRowMessage('refreshing'));
            })
            .always(() => {
                $.getJSON(url)
                    .done((response) => {
                        const data = response?.data || {};
                        const stores = Array.isArray(data.stores) ? data.stores : [];
                        renderStoreTable(stores);

                        if (!stores.length) {
                            setStoreRefreshLoading(false);
                            return;
                        }

                        refreshStoreRows(stores, forceRefresh)
                            .always(() => {
                                rerenderStoreRows();
                                clearTokenStateTimers();
                                setStoreRefreshLoading(false);
                            });
                    })
                    .fail(() => {
                        log('Store overview API failed');
                        $('#stores-info-tbody').html('<tr><td colspan="16" class="text-center py-5 text-danger">Failed to load store overview.</td></tr>');
                        setStoreRefreshLoading(false);
                    });
            });
    }

    function showBootstrapModal(id) {
        const el = document.getElementById(id);

        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(el).show();
        } else {
            $('#' + id).modal('show');
        }
    }

    function targetPeriodLabel(row) {
        if (row.period_type === 'daily') {
            return row.start_date || '-';
        }

        if (row.period_type === 'weekly') {
            return `Week ${row.period_value || '-'}, ${row.year || '-'}`;
        }

        if (row.period_type === 'monthly') {
            const monthName = row.period_value
                ? new Date(row.year || new Date().getFullYear(), Number(row.period_value) - 1, 1).toLocaleString('default', { month: 'long' })
                : '-';

            return `${monthName}, ${row.year || '-'}`;
        }

        if (row.period_type === 'yearly') {
            return `Full Year, ${row.year || '-'}`;
        }

        return row.period_type || '-';
    }

    function loadTargetsModalData(storeId, storeName) {
        const filter = selectedTargetFilter();
        $('#storeTargetsFilterInfo').text(`Showing ${targetFilterLabel(filter)} data`);
        startTargetLoading(storeName);

        $.ajax({
            url: summaryRoute.replace('__ID__', storeId),
            method: 'GET',
            data: {
                tables: 'salesman_target_details',
                date_from: filter.date_from,
                date_to: filter.date_to
            },
            headers: { Accept: 'application/json' },
        })
        .done((response) => {
            const remote = response?.data || {};
            const results = remote.results || remote.data?.results || {};
            renderTargetsModal(results, storeName);
        })
        .fail((jqXHR, textStatus) => {
            showTargetFailure(textStatus || jqXHR?.responseJSON?.message || 'Failed to load target data.');
        });
    }

    $(document).on('click', '.js-view-targets', function () {
        const storeId = $(this).data('store-id');
        const storeName = $(this).data('store-name');

        currentTargetStore = { id: storeId, name: storeName };
        $('#storeTargetsLabel').text(`${storeName} - Salesman Targets`);
        setDefaultTargetDateFilter();
        showBootstrapModal('storeTargetsModal');
        loadTargetsModalData(storeId, storeName);
    });

    $(document).on('click', '#applyStoreTargetsFilter', function () {
        if (!currentTargetStore.id) return;
        loadTargetsModalData(currentTargetStore.id, currentTargetStore.name);
    });

    $(document).on('click', '#resetStoreTargetsFilter', function () {
        setDefaultTargetDateFilter();

        if (!currentTargetStore.id) return;
        loadTargetsModalData(currentTargetStore.id, currentTargetStore.name);
    });

    function loadStoreDetails(storeId, storeName) {
        $('#storeDetailsModalLabel').text(`${storeName} - Store Details`);
        startDetailLoading('#storeDetailsContent');
        $('#storeDetailsModal').modal('show');

        const cachedRow = overviewRows[String(storeId)];
        if (cachedRow && cachedRow.ok && cachedRow.raw) {
            renderStoreDetailsFromSource(cachedRow.raw, storeName, storeId);
            return;
        }

        $.ajax({
            url: summaryUrl(storeId),
            method: 'GET',
            data: { tables: TABLES },
            dataType: 'json',
            timeout: 90000,
            headers: { Accept: 'application/json' }
        })
        .done((response) => renderStoreDetailsFromSource(response?.data || response, storeName, storeId))
        .fail((jqXHR, textStatus) => {
            clearDetailLoadingTimers();
            $('#storeDetailsContent').html(`<div class="alert alert-danger"><i class="ri-error-warning-line me-2"></i>${escapeHtml(friendlyFailureMessage(textStatus || jqXHR?.responseJSON?.message || 'Failed to load store data.'))}</div>`);
        });
    }

    $(document).on('click', '.js-view-store', function (e) {
        e.preventDefault();
        loadStoreDetails($(this).data('store-id'), $(this).data('store-name'));
    });


    $(document).on('click', '.target-page-link', function (e) {
        e.preventDefault();
        const type = $(this).data('target-table');
        const page = parseInt($(this).data('page'), 10);

        if (!page) return;

        if (type === 'summary') {
            targetSummaryPage = page;
            renderTargetSummaryPage();
            return;
        }

        targetDetailPage = page;
        renderTargetDetailsPage();
    });

    $(document).on('click', '.js-report-link', function () {
        const $link = $(this);
        const label = $link.data('loading-label') || 'Opening...';

        if (!$link.data('original-html')) {
            $link.data('original-html', $link.html());
        }

        $link.addClass('disabled action-loading').html(`<span class="spinner-border spinner-border-sm me-1"></span>${label}`);
    });

    $(document).on('click', '#exportStoreDetailsBtn', downloadStoreDetails);
    $(document).on('click', '#printStoreDetailsBtn', printStoreDetails);
    $(document).on('click', '.js-refresh-store', function (e) {
        e.preventDefault();
        refreshStoreRow($(this).data('store-id'), true);
    });

    $('#refresh-store-overview').on('click', () => loadOverview(true));

    $(document).on('click', '#store-code-sort', function () {
        storeCodeSortDirection = storeCodeSortDirection === 'asc' ? 'desc' : 'asc';
        rerenderStoreRows();
    });

    $(document).on('keydown', '#store-code-sort', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        e.preventDefault();
        $(this).trigger('click');
    });

    loadOverview();
})();
</script>

<style>
.store-code-sort-header {
    cursor: pointer;
    user-select: none;
    white-space: nowrap;
}

.store-code-sort-header:hover {
    background-color: #343a40 !important;
}

.store-code-sort-header i {
    font-size: 14px;
    vertical-align: middle;
}

.icon-box {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.store-details-content {
    max-height: 70vh;
    overflow-y: auto;
    padding-right: 10px;
}

.live-status-card {
    min-width: 210px;
    max-width: 260px;
    margin: 0 auto;
    padding: 10px 12px;
    border: 1px solid #e9ecef;
    border-radius: 14px;
    background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
    text-align: left;
    overflow: hidden;
}

.live-status-card.status-expired,
.live-status-card.status-failed {
    background: linear-gradient(135deg, #fff8f0 0%, #fff 100%);
}

.live-status-card.status-refreshing,
.live-status-card.status-connecting,
.live-status-card.status-fetching {
    animation: liveStatusPulse 1.35s ease-in-out infinite;
}

.live-status-head {
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.live-status-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #eef4ff;
    color: #0d6efd;
    flex: 0 0 auto;
}

.status-expired .live-status-icon { background: #fff3cd; color: #b58100; }
.status-refreshing .live-status-icon { background: #e7f1ff; color: #0d6efd; }
.status-connecting .live-status-icon { background: #edf7ff; color: #0aa2c0; }
.status-fetching .live-status-icon { background: #f0eaff; color: #6f42c1; }
.status-ready .live-status-icon { background: #eaf7ef; color: #198754; }
.status-failed .live-status-icon { background: #fdecef; color: #dc3545; }

.live-status-title {
    font-weight: 700;
    font-size: 12.5px;
    color: #1f2937;
}

.live-status-detail {
    margin-top: 5px;
    font-size: 11px;
    line-height: 1.35;
    color: #6c757d;
}

.live-progress {
    height: 4px;
    border-radius: 999px;
    background: #e9ecef;
    margin-top: 9px;
    overflow: hidden;
}

.live-progress span {
    display: block;
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg, #0d6efd, #20c997);
    transition: width .45s ease;
}

.status-expired .live-progress span { background: linear-gradient(90deg, #ffc107, #fd7e14); }
.status-failed .live-progress span { background: linear-gradient(90deg, #dc3545, #ff6b6b); }
.status-ready .live-progress span { background: linear-gradient(90deg, #198754, #20c997); }

.live-step-dots {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 7px;
}

.live-step-dots span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #d8dee7;
    display: inline-block;
}

.live-step-dots span.active {
    background: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, .12);
}

.status-expired .live-step-dots span.active { background: #ffc107; box-shadow: 0 0 0 3px rgba(255, 193, 7, .18); }
.status-ready .live-step-dots span.active { background: #198754; box-shadow: 0 0 0 3px rgba(25, 135, 84, .15); }
.status-failed .live-step-dots span.active { background: #dc3545; box-shadow: 0 0 0 3px rgba(220, 53, 69, .14); }

.status-spin {
    animation: liveStatusSpin .8s linear infinite;
}

.typing-dots {
    display: inline-flex;
    gap: 2px;
    margin-left: 3px;
    vertical-align: middle;
}

.typing-dots i {
    width: 3px;
    height: 3px;
    border-radius: 50%;
    background: currentColor;
    opacity: .35;
    animation: liveDot 1.1s infinite ease-in-out;
}

.typing-dots i:nth-child(2) { animation-delay: .18s; }
.typing-dots i:nth-child(3) { animation-delay: .36s; }

.data-skeleton {
    display: inline-block;
    height: 13px;
    max-width: 92px;
    border-radius: 999px;
    background: linear-gradient(90deg, #edf1f5 0%, #f8fafc 45%, #edf1f5 100%);
    background-size: 220% 100%;
    animation: dataSkeleton 1.25s ease-in-out infinite;
}

.live-status-row td {
    vertical-align: middle;
}

@keyframes liveStatusPulse {
    0%, 100% { transform: translateY(0); box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06); }
    50% { transform: translateY(-1px); box-shadow: 0 12px 26px rgba(13, 110, 253, 0.12); }
}

@keyframes liveStatusSpin {
    to { transform: rotate(360deg); }
}

@keyframes liveDot {
    0%, 80%, 100% { opacity: .25; transform: translateY(0); }
    40% { opacity: 1; transform: translateY(-2px); }
}

@keyframes dataSkeleton {
    0% { background-position: 100% 0; }
    100% { background-position: -100% 0; }
}


/* Live async loaders used by dashboard/store details and target modal */
.modal-live-loader {
    padding: 22px;
    border: 1px solid #edf1f5;
    border-radius: 18px;
    background: linear-gradient(135deg, #fbfdff 0%, #ffffff 100%);
}

.live-card-skeleton {
    min-height: 94px;
    border-radius: 16px;
    border: 1px solid #edf1f5;
    background: #fff;
    padding: 16px;
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
}

.skeleton-line {
    display: block;
    height: 12px;
    border-radius: 999px;
    background: linear-gradient(90deg, #edf1f5 0%, #f8fafc 45%, #edf1f5 100%);
    background-size: 220% 100%;
    animation: dataSkeleton 1.25s ease-in-out infinite;
}

.skeleton-line.line-sm { width: 42%; }
.skeleton-line.line-md { width: 68%; }
.skeleton-line.line-lg { width: 88%; height: 18px; }

.chart-live-loader {
    position: absolute;
    inset: 0;
    z-index: 5;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.86);
    backdrop-filter: blur(2px);
}

.target-modal-table tbody tr {
    transition: background-color .2s ease, transform .2s ease;
}

.target-modal-table tbody tr:hover {
    background-color: #f8fbff;
}

.live-mini-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    padding: 5px 10px;
    background: #eef4ff;
    color: #0d6efd;
    font-weight: 600;
    font-size: 12px;
}

.action-loading {
    pointer-events: none;
    opacity: .78;
}

</style>
@endsection
