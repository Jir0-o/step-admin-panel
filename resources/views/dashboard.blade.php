@extends('layouts.master')

@section('title', 'Dashboard || Step Shoe Pos')

@section('content')
<div class="row m-0 g-3 inria-sans">
    <div class="col-md-6 col-xl-3">
        <div class="card theme-shadow h-100">
            <div class="card-body">
                <p class="m-0 text-muted">Total Sales (Year)</p>
                <h4 id="total-sales-year" class="mt-2 mb-1">-</h4>
                <small class="text-muted">Today: <span id="today-sales">-</span></small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card theme-shadow h-100">
            <div class="card-body">
                <p class="m-0 text-muted">Total Profit</p>
                <h4 id="total-profit" class="mt-2 mb-1">-</h4>
                <small class="text-muted">Today income: <span id="today-income">-</span></small>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card theme-shadow h-100">
            <div class="card-body">
                <p class="m-0 text-muted">All Store Today's Target</p>
                <h4 id="dashboard-month-target" class="mt-2 mb-1 text-danger">-</h4>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card theme-shadow h-100">
            <div class="card-body">
                <p class="m-0 text-muted">All Store Today's Achievement</p>
                <h4 id="dashboard-month-achievement" class="mt-2 mb-1 text-primary">-</h4>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card theme-shadow h-100">
            <div class="card-body">
                <p class="m-0 text-muted">Today's Achievement %</p>
                <h4 id="dashboard-month-percentage" class="mt-2 mb-1">-</h4>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card theme-shadow h-100">
            <div class="card-body">
                <p class="m-0 text-muted">Total Products</p>
                <h4 id="total-products" class="mt-2 mb-1">-</h4>
                <small class="text-muted">Suppliers: <span id="total-suppliers">-</span></small>
            </div>
        </div>
    </div>
    {{-- <div class="col-md-6 col-xl-3">
        <div class="card theme-shadow h-100">
            <div class="card-body">
                <p class="m-0 text-muted">Stores</p>
                <h4 id="total-stores" class="mt-2 mb-1">-</h4>
                <small class="text-muted">Failed sync: <span id="failed-stores">-</span></small>
            </div>
        </div>
    </div> --}}

    <div class="col-lg-7">
        <div class="card theme-shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Top Store Sales</h5>
                    <button class="btn btn-sm btn-outline-primary" id="refresh-dashboard">Refresh</button>
                </div>
                <div style="height: 320px; position: relative;">
                    <canvas id="storeSalesChart"></canvas>
                    <div id="dashboard-chart-loader" class="chart-live-loader d-none"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card theme-shadow h-100">
            <div class="card-body">
                <h5 class="mb-3">Quick Totals</h5>
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Today Sales</span><strong id="today-sales-quick">-</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Yesterday Sales</span><strong id="yesterday-sales">-</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Today Income</span><strong id="today-income-quick">-</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Yesterday Income</span><strong id="yesterday-income">-</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Total Expenses</span><strong id="total-expenses">-</strong></div>
            </div>
        </div>
    </div>
</div>

<div class="p-4">
    <div class="card theme-shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="mb-0">Store Information</h5>
                    <small class="text-muted">Optimized server-side summary for all stores</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" id="refresh-dashboard-table" type="button">
                        <i class="ri-refresh-line me-1"></i> Refresh
                    </button>
                    <a href="{{ route('store.index') }}" class="btn btn-outline-secondary btn-sm">Open full store page</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-center">
                            <th class="bg-dark text-white">
                                <button type="button" id="dashboard-store-code-sort" class="btn btn-link btn-sm text-white text-decoration-none p-0 fw-semibold" title="Sort by store code">
                                    Store Code <i id="dashboard-store-code-sort-icon" class="ri-sort-asc"></i>
                                </button>
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
                    <tbody id="dashboard-store-tbody">
                        <tr><td colspan="16" class="text-center py-4"><span class="badge bg-warning text-dark"><span class="spinner-border spinner-border-sm me-1"></span>Generating new token...</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dashboardStoreDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dashboardStoreDetailsLabel">Store Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="dashboardStoreDetailsBody">
                <div class="text-center py-4">Loading...</div>
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

<style>

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


.dashboard-store-sort-button {
    color: inherit;
}

#dashboard-store-code-sort:hover,
#dashboard-store-code-sort:focus {
    color: #fff;
    opacity: .85;
    box-shadow: none;
}

#dashboard-store-code-sort i {
    font-size: 16px;
    vertical-align: -2px;
}

</style>
<script>
(() => {
    const overviewUrl = @json(route('dashboard.overview'));
    const tokenStatusRoute = @json(route('dashboard.token-status'));
    const summaryRoute = @json(route('stores.fetch-summary', ['store' => '__ID__']));
    const storeOverviewRoute = @json(route('dashboard.overview.store', ['store' => '__ID__']));
    const stockRoute = @json(route('manager.stock-data.index', ['store' => '__ID__']));
    const salesRoute = @json(route('manager.sales.index', ['store' => '__ID__']));

    let chart = null;
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
    const TARGET_SUMMARY_PER_PAGE = 5;
    const TARGET_DETAIL_PER_PAGE = 8;
    let storeCodeSortDirection = 'asc';


    const num = (value) => {
        const number = Number(value);
        return Number.isFinite(number) ? number : 0;
    };
    const taka = (value) => '৳ ' + num(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const integer = (value) => num(value).toLocaleString();
    const TABLES = 'products,suppliers,cart_informtion,expense_details,banner_information,salesman_target_summary';
    const STORE_REFRESH_CONCURRENCY = 4;

    const STORE_CODE_COLLATOR = new Intl.Collator(undefined, {
        numeric: true,
        sensitivity: 'base',
    });

    function storeCodeValue(store) {
        return String(store?.banner_code || store?.store_code || '').trim();
    }

    function compareStoreRowsByCode(left, right) {
        const leftCode = storeCodeValue(left);
        const rightCode = storeCodeValue(right);

        // Rows whose real store code has not arrived yet stay at the bottom.
        // Once the async DB/API response arrives, updateStoreRow() calls the
        // sorter again and the row moves automatically to its correct place.
        if (!leftCode && !rightCode) {
            return (left._originalIndex || 0) - (right._originalIndex || 0);
        }

        if (!leftCode) return 1;
        if (!rightCode) return -1;

        let result = STORE_CODE_COLLATOR.compare(leftCode, rightCode);

        if (storeCodeSortDirection === 'desc') {
            result *= -1;
        }

        if (result !== 0) return result;

        return String(left.name || '').localeCompare(String(right.name || ''));
    }

    function applyLoadedStoreCodeSort() {
        const rows = Object.values(overviewRows).sort(compareStoreRowsByCode);
        const $tbody = $('#dashboard-store-tbody');

        rows.forEach((row, index) => {
            row._index = index;
            overviewRows[String(row.id)] = row;

            const $existing = $(`#${tableRowId(row.id)}`);
            if ($existing.length) {
                $tbody.append($existing);
            }
        });
    }

    function updateStoreCodeSortIcon() {
        const icon = $('#dashboard-store-code-sort-icon');
        icon.removeClass('ri-sort-asc ri-sort-desc')
            .addClass(storeCodeSortDirection === 'asc' ? 'ri-sort-asc' : 'ri-sort-desc');

        $('#dashboard-store-code-sort').attr(
            'title',
            storeCodeSortDirection === 'asc'
                ? 'Currently G0001 → G0002. Click for descending.'
                : 'Currently descending. Click for G0001 → G0002.'
        );
    }

    function renderCards(totals) {
        $('#total-sales-year').text(taka(totals.sales_year));
        $('#today-sales').text(taka(totals.sales_today));
        $('#total-profit').text(taka(totals.profit));
        $('#today-income').text(taka(totals.income_today));
        $('#dashboard-month-discount').text(taka(totals.discount_month));
        $('#dashboard-today-discount').text(taka(totals.discount_today));
        $('#total-products').text(integer(totals.products));
        $('#total-suppliers').text(integer(totals.suppliers));
        $('#total-stores').text(integer(totals.store_count));
        $('#failed-stores').text(integer(totals.failed_stores));
        $('#today-sales-quick').text(taka(totals.sales_today));
        $('#yesterday-sales').text(taka(totals.sales_yesterday));
        $('#today-income-quick').text(taka(totals.income_today));
        $('#yesterday-income').text(taka(totals.income_yesterday));
        $('#total-expenses').text(taka(totals.expenses));
        $('#dashboard-month-target').text(taka(totals.target_today));
        $('#dashboard-month-achievement').text(taka(totals.achievement_today));
        $('#dashboard-month-percentage').text((Number(totals.percentage_today || 0)).toFixed(2) + '%');
    }




    function cardSkeleton(width = 70) {
        return `<span class="data-skeleton" style="width:${width}%; height:18px;"></span>`;
    }

    function setDashboardDataLoading(isLoading) {
        const valueSelectors = [
            '#total-sales-year', '#today-sales', '#total-profit', '#today-income',
            '#dashboard-month-target', '#dashboard-month-achievement', '#dashboard-month-percentage',
            '#total-products', '#total-suppliers', '#today-sales-quick', '#yesterday-sales',
            '#today-income-quick', '#yesterday-income', '#total-expenses'
        ];

        if (isLoading) {
            valueSelectors.forEach((selector, index) => $(selector).html(cardSkeleton(index % 3 === 0 ? 62 : 45)));
            $('#dashboard-chart-loader').removeClass('d-none').html(liveStatusMarkup('fetching', 'Preparing chart and dashboard cards.'));
            return;
        }

        $('#dashboard-chart-loader').addClass('d-none').empty();
    }

    function setDashboardRefreshLoading(isLoading) {
        setRefreshButtonState('#refresh-dashboard, #refresh-dashboard-table', isLoading);
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

        const rows = (statuses || []).map((store, index) => {
            const willRefresh = forceRefresh || store.needs_refresh;
            const firstState = willRefresh ? 'expired' : 'checking';
            const statusSelector = `#dashboard-token-status-${store.id}`;

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
                <td class="text-center" id="dashboard-token-status-${store.id}">${liveStatusMarkup(firstState)}</td>
                <td class="text-center"><span class="text-muted small">Waiting</span></td>
            </tr>`;
        }).join('');

        $('#dashboard-store-tbody').html(rows || loadingRowMessage('refreshing'));
    }

    function tableRowId(storeId) {
        return `dashboard-store-row-${storeId}`;
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
            suppliers: 0,
            sales_year: 0,
            sales_month: 0,
            sales_today: 0,
            sales_yesterday: 0,
            discount_today: 0,
            discount_month: 0,
            profit: 0,
            expenses: 0,
            income_today: 0,
            income_yesterday: 0,
            target_today: 0,
            achievement_today: 0,
            percentage_today: 0,
            target_monthly: 0,
            achievement_monthly: 0,
            percentage_monthly: 0,
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
            totals.suppliers += num(row.suppliers_total);
            totals.sales_year += num(row.sales_year);
            totals.sales_month += num(row.sales_month);
            totals.sales_today += num(row.sales_today);
            totals.sales_yesterday += num(row.sales_yesterday);
            totals.discount_today += num(row.discount_today);
            totals.discount_month += num(row.discount_month);
            totals.profit += num(row.profit);
            totals.expenses += num(row.expenses);
            totals.income_today += num(row.income_today);
            totals.income_yesterday += num(row.income_yesterday);
            totals.target_today += num(row.store_target_today);
            totals.achievement_today += num(row.today_achievement);
            totals.target_monthly += num(row.store_target_monthly);
            totals.achievement_monthly += num(row.monthly_achievement);
        });

        totals.percentage_today = totals.target_today > 0 ? ((totals.achievement_today / totals.target_today) * 100) : 0;
        totals.percentage_monthly = totals.target_monthly > 0 ? ((totals.achievement_monthly / totals.target_monthly) * 100) : 0;

        const chart = rows
            .filter((row) => row.ok)
            .sort((a, b) => num(b.sales_year) - num(a.sales_year))
            .slice(0, 10)
            .map((row) => ({ label: row.name || '-', value: num(row.sales_year) }));

        return { totals, chart };
    }

    function refreshDashboardAggregates() {
        const overview = buildClientOverview();
        renderCards(overview.totals);
        renderChart(overview.chart);

        const rows = Object.values(overviewRows);
        const hasReadyData = rows.some((row) => row.ok);
        const hasPendingRows = rows.some((row) => row.pending);

        // Do not keep dashboard cards/chart locked until every store finishes.
        // One slow/down store should not keep the whole dashboard in loading mode.
        if (hasReadyData || !hasPendingRows) {
            setDashboardDataLoading(false);
        }
    }

    function renderStoreRow(store, index) {
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

    function renderTable(stores) {
        overviewRows = {};
        const rows = stores.map((store, index) => {
            const row = { ...store, _index: index, _originalIndex: index };
            overviewRows[String(row.id)] = row;
            return renderStoreRow(row, index);
        }).join('');

        $('#dashboard-store-tbody').html(rows || '<tr><td colspan="16" class="text-center py-4">No stores found.</td></tr>');
        applyLoadedStoreCodeSort();
        refreshDashboardAggregates();
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
        const originalIndex = Number.isInteger(previous._originalIndex) ? previous._originalIndex : index;
        const row = { ...previous, ...store, pending: false, _index: index, _originalIndex: originalIndex };
        overviewRows[key] = row;
        const $existing = $(`#${tableRowId(row.id)}`);

        if ($existing.length) {
            $existing.replaceWith(renderStoreRow(row, index));
        }

        // Store Code (banner_code/store_code) arrives asynchronously from the
        // store database. Re-sort immediately when the real code becomes known.
        applyLoadedStoreCodeSort();
        refreshDashboardAggregates();
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
        .done((response) => {
            updateStoreRow(response?.data || {});
        })
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

    function renderChart(chartRows) {
        const ctx = document.getElementById('storeSalesChart');
        if (!ctx || !window.Chart) return;

        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartRows.map(row => row.label),
                datasets: [{
                    label: 'Yearly Sales',
                    data: chartRows.map(row => row.value),
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
            },
        });
    }

    function applyStoreCodeSort() {
        applyLoadedStoreCodeSort();
        refreshDashboardAggregates();
        updateStoreCodeSortIcon();
    }

    function loadOverview(forceRefresh = false) {
        const quickParam = forceRefresh ? 'quick=1&refresh=1' : 'quick=1';
        const url = `${overviewUrl}?${quickParam}`;
        const tokenStatusUrl = forceRefresh
            ? `${tokenStatusRoute}?refresh=1`
            : tokenStatusRoute;

        setDashboardRefreshLoading(true);
        setDashboardDataLoading(true);
        clearTokenStateTimers();
        $('#dashboard-store-tbody').html(loadingRowMessage('preparing'));

        const aggregateLoaderFallback = setTimeout(() => {
            // If remote stores are taking longer, show the current partial totals instead of
            // leaving the cards/chart in skeleton loading forever. Rows will keep updating.
            refreshDashboardAggregates();
            setDashboardDataLoading(false);
        }, 8000);

        $.getJSON(tokenStatusUrl)
            .done((response) => {
                renderTokenStatusRows(response?.data || [], forceRefresh);
            })
            .fail(() => {
                $('#dashboard-store-tbody').html(loadingRowMessage('refreshing'));
            })
            .always(() => {
                $.getJSON(url)
                    .done((response) => {
                        const data = response?.data || {};
                        const stores = data.stores || [];
                        renderTable(stores);

                        if (!stores.length) {
                            clearTimeout(aggregateLoaderFallback);
                            setDashboardDataLoading(false);
                            setDashboardRefreshLoading(false);
                            return;
                        }

                        refreshStoreRows(stores, forceRefresh)
                            .always(() => {
                                // Final guarantee after every store has either loaded or failed.
                                applyLoadedStoreCodeSort();
                                clearTimeout(aggregateLoaderFallback);
                                clearTokenStateTimers();
                                setDashboardDataLoading(false);
                                setDashboardRefreshLoading(false);
                            });
                    })
                    .fail(() => {
                        $('#dashboard-store-tbody').html('<tr><td colspan="16" class="text-center text-danger py-4">Failed to load dashboard overview.</td></tr>');
                        [
                            '#total-sales-year', '#today-sales', '#total-profit', '#today-income',
                            '#dashboard-month-target', '#dashboard-month-achievement', '#dashboard-month-percentage',
                            '#total-products', '#total-suppliers', '#today-sales-quick', '#yesterday-sales',
                            '#today-income-quick', '#yesterday-income', '#total-expenses'
                        ].forEach((selector) => $(selector).text('-'));
                        clearTimeout(aggregateLoaderFallback);
                        clearTokenStateTimers();
                        setDashboardDataLoading(false);
                        setDashboardRefreshLoading(false);
                    });
            });
    }

    function getResultsFromPayload(payload) {
        if (!payload) return {};
        return payload.results || payload.data || payload.raw || payload;
    }

    function renderDetailModal(source, storeName, storeId) {
        clearDetailLoadingTimers();
        const results = getResultsFromPayload(source);
        const cart = results.cart_informtion || {};
        const expense = results.expense_details || {};
        const products = results.products || {};
        const suppliers = results.suppliers || {};
        const banner = results.banner_information || {};
        const totalAmount = Number(cart.total_amount || cart.total_amount_year || 0);
        const totalCount = Number(cart.total_count || 0);
        const avgTransaction = totalCount > 0 ? totalAmount / totalCount : 0;
        const profitMargin = totalAmount > 0 ? (Number(cart.total_profit || 0) / totalAmount) * 100 : 0;
        const expenseRatio = totalAmount > 0 ? (Number(expense.total_amount || 0) / totalAmount) * 100 : 0;
        const targetInfo = results.salesman_target_summary || {};

        $('#dashboardStoreDetailsBody').html(`
            <div class="store-details-content">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-light border-0 shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h5 class="mb-1">${escapeHtml(storeName || 'Store')}</h5>
                                    <p class="mb-0 text-muted">Store ID: ${storeId || '-'} | Code: ${escapeHtml(banner.banner_code || banner.banner?.banner_code || '-')} | Last Updated: ${new Date().toLocaleString()}</p>
                                </div>
                                <div class="badge bg-success px-3 py-2"><i class="ri-check-line me-1"></i> Connected</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3"><div class="card h-100 bg-info text-white border-0 shadow-sm"><div class="card-body text-center"><small>Total Sales (Year)</small><h4 class="mt-2 mb-1">${taka(cart.total_amount_year || 0)}</h4></div></div></div>
                    <div class="col-md-4 mb-3"><div class="card h-100 bg-success text-white border-0 shadow-sm"><div class="card-body text-center"><small>Today's Sales</small><h4 class="mt-2 mb-1">${taka(cart.today_total_amount || cart.total_amount_today || 0)}</h4></div></div></div>
                    <div class="col-md-4 mb-3"><div class="card h-100 bg-warning text-dark border-0 shadow-sm"><div class="card-body text-center"><small>Total Profit</small><h4 class="mt-2 mb-1">${taka(cart.total_profit || 0)}</h4><small>Margin: ${profitMargin.toFixed(1)}%</small></div></div></div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3"><div class="card h-100 shadow-sm border-0"><div class="card-header bg-primary text-white"><h6 class="mb-0">Products</h6></div><div class="card-body text-center"><div class="display-6 text-primary mb-2">${integer(products.total_count || 0)}</div><p class="mb-0">Available Products</p></div></div></div>
                    <div class="col-md-4 mb-3"><div class="card h-100 shadow-sm border-0"><div class="card-header bg-info text-white"><h6 class="mb-0">Suppliers</h6></div><div class="card-body text-center"><div class="display-6 text-info mb-2">${integer(suppliers.total_count || 0)}</div><p class="mb-0">Registered Suppliers</p></div></div></div>
                    <div class="col-md-4 mb-3"><div class="card h-100 shadow-sm border-0"><div class="card-header bg-warning text-white"><h6 class="mb-0">Banner</h6></div><div class="card-body text-center"><div class="display-6 text-warning mb-2">${integer(banner.total_count || 0)}</div>${banner.banner && (banner.banner.banner_name || banner.banner.banner_code) ? `<div class="alert alert-light mt-3 mb-0"><strong>Current:</strong> ${escapeHtml(banner.banner.banner_name || '-')}<br><strong>Code:</strong> ${escapeHtml(banner.banner.banner_code || banner.banner_code || '-')}</div>` : '<p class="text-muted mb-0">No active banner</p>'}</div></div></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4 col-6 mb-3"><div class="card text-center shadow-sm border-0 h-100"><div class="card-body"><i class="ri-exchange-dollar-line fs-2 text-primary mb-2"></i><h5 class="mb-1">${integer(totalCount)}</h5><p class="mb-0 small text-muted">Transactions</p></div></div></div>
                    <div class="col-md-4 col-6 mb-3"><div class="card text-center shadow-sm border-0 h-100"><div class="card-body"><i class="ri-bank-card-line fs-2 text-danger mb-2"></i><h5 class="mb-1">${taka(expense.total_amount || 0)}</h5><p class="mb-0 small text-muted">Expenses</p></div></div></div>
                    <div class="col-md-4 col-12 mb-3"><div class="card text-center shadow-sm border-0 h-100"><div class="card-body"><i class="ri-pie-chart-line fs-2 text-secondary mb-2"></i><h5 class="mb-1">${expenseRatio.toFixed(1)}%</h5><p class="mb-0 small text-muted">Expense Ratio</p><small class="text-muted">Avg transaction: ${taka(avgTransaction)}</small></div></div></div>
                </div>
            </div>
        `);
    }

    $(document).on('click', '.js-view-store', function () {
        const storeId = $(this).data('store-id');
        const storeName = $(this).data('store-name');
        $('#dashboardStoreDetailsLabel').text(`${storeName} Details`);
        startDetailLoading('#dashboardStoreDetailsBody');
        $('#dashboardStoreDetailsModal').modal('show');

        const cachedRow = overviewRows[String(storeId)];
        if (cachedRow && cachedRow.ok && cachedRow.raw) {
            renderDetailModal(cachedRow.raw, storeName, storeId);
            return;
        }

        $.ajax({
            url: summaryRoute.replace('__ID__', storeId),
            method: 'GET',
            data: { tables: TABLES },
            headers: { Accept: 'application/json' },
        })
        .done((response) => renderDetailModal(response?.data || response, storeName, storeId))
        .fail((jqXHR, textStatus) => {
            clearDetailLoadingTimers();
            $('#dashboardStoreDetailsBody').html(`<div class="alert alert-danger"><i class="ri-error-warning-line me-2"></i>${escapeHtml(friendlyFailureMessage(textStatus || jqXHR?.responseJSON?.message || 'Failed to load store details.'))}</div>`);
        });
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

    $(document).on('click', '.js-refresh-store', function (e) {
        e.preventDefault();
        refreshStoreRow($(this).data('store-id'), true);
    });

    $(document).on('click', '#dashboard-store-code-sort', function () {
        storeCodeSortDirection = storeCodeSortDirection === 'asc' ? 'desc' : 'asc';
        applyStoreCodeSort();
    });

    $('#refresh-dashboard, #refresh-dashboard-table').on('click', () => loadOverview(true));
    updateStoreCodeSortIcon();
    loadOverview();
})();
</script>
@endsection
