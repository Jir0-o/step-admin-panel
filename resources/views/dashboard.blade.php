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
                <p class="m-0 text-muted">Total This Month Target</p>
                <h4 id="dashboard-month-target" class="mt-2 mb-1">-</h4>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card theme-shadow h-100">
            <div class="card-body">
                <p class="m-0 text-muted">All Store Achievement</p>
                <h4 id="dashboard-month-achievement" class="mt-2 mb-1">-</h4>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card theme-shadow h-100">
            <div class="card-body">
                <p class="m-0 text-muted">Achievement Percentage</p>
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
                <a href="{{ route('store.index') }}" class="btn btn-outline-secondary btn-sm">Open full store page</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-center">
                            <th class="bg-dark text-white">SR</th>
                            <th class="bg-dark text-white">Store Name</th>
                            <th class="bg-dark text-white">Today Sales</th>
                            <th class="bg-dark text-white">This Month</th>
                            <th class="bg-dark text-white">This Year</th>
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
                        <tr><td colspan="7" class="text-center py-4">Loading store overview...</td></tr>
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

<script>
(() => {
    const overviewUrl = @json(route('dashboard.overview'));
    const summaryRoute = @json(route('stores.fetch-summary', ['store' => '__ID__']));
    const stockRoute = @json(route('manager.stock-data.index', ['store' => '__ID__']));
    const salesRoute = @json(route('manager.sales.index', ['store' => '__ID__']));

    let chart = null;
    let overviewRows = {};

    const taka = (value) => '৳ ' + Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const integer = (value) => Number(value || 0).toLocaleString();
    const TABLES = 'products,suppliers,cart_informtion,expense_details,banner_information,salesman_target_summary';

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
        $('#dashboard-month-target').text(taka(totals.target_monthly));
        $('#dashboard-month-achievement').text(taka(totals.achievement_monthly));
        $('#dashboard-month-percentage').text((Number(totals.percentage_monthly || 0)).toFixed(2) + '%');
    }

    function renderTable(stores) {
        overviewRows = {};
        const rows = stores.map((store, index) => {
            overviewRows[String(store.id)] = store;
            const statusBadge = store.ok
                ? '<span class="badge bg-success">Ready</span>'
                : `<span class="badge bg-danger">${store.message || 'Failed'}</span>`;

            return `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td class="text-center">${store.name}</td>
                <td class="text-center">${store.ok ? taka(store.sales_today) : '-'}</td>
                <td class="text-center">${store.ok ? taka(store.sales_month) : '-'}</td>
                <td class="text-center">${store.ok ? taka(store.sales_year) : '-'}</td>
                <td class="text-center">${store.ok ? integer(store.salesmen_count) : '-'}</td>
                <td class="text-center">${store.ok ? taka(store.discount_today) : '-'}</td>
                <td class="text-center">${store.ok ? taka(store.discount_month) : '-'}</td>
                <td class="text-center">${store.ok ? taka(store.store_target_monthly) : '-'}</td>
                <td class="text-center">${store.ok ? taka(store.monthly_achievement) : '-'}</td>
                <td class="text-center">${store.ok ? (Number(store.monthly_percentage || 0).toFixed(2) + '%') : '-'}</td>
                <td class="text-center">${store.ok ? taka(store.store_target_yearly) : '-'}</td>
                <td class="text-center">${store.ok ? taka(store.yearly_achievement) : '-'}</td>
                <td class="text-center">${store.ok ? (Number(store.yearly_percentage || 0).toFixed(2) + '%') : '-'}</td>
                <td class="text-center">${statusBadge}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-success js-view-store" data-store-id="${store.id}" data-store-name="${store.name}">View</button>
                    <a class="btn btn-sm btn-info" href="${stockRoute.replace('__ID__', store.id)}">Stock</a>
                    <a class="btn btn-sm btn-primary" href="${salesRoute.replace('__ID__', store.id)}">Sales</a>
                </td>
            </tr>`;
        }).join('');

        $('#dashboard-store-tbody').html(rows || '<tr><td colspan="7" class="text-center py-4">No stores found.</td></tr>');
    }

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

    function loadOverview(forceRefresh = false) {
        const url = forceRefresh ? `${overviewUrl}?refresh=1` : overviewUrl;
        $('#dashboard-store-tbody').html('<tr><td colspan="7" class="text-center py-4">Loading store overview...</td></tr>');

        $.getJSON(url)
            .done((response) => {
                const data = response?.data || {};
                renderCards(data.totals || {});
                renderTable(data.stores || []);
                renderChart(data.chart || []);
            })
            .fail(() => {
                $('#dashboard-store-tbody').html('<tr><td colspan="7" class="text-center text-danger py-4">Failed to load dashboard overview.</td></tr>');
            });
    }

    function getResultsFromPayload(payload) {
        if (!payload) return {};
        return payload.results || payload.data || payload.raw || payload;
    }

    function renderDetailModal(source, storeName, storeId) {
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
                                    <h5 class="mb-1">${storeName || 'Store'}</h5>
                                    <p class="mb-0 text-muted">Store ID: ${storeId || '-'} | Last Updated: ${new Date().toLocaleString()}</p>
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
                    <div class="col-md-4 mb-3"><div class="card h-100 shadow-sm border-0"><div class="card-header bg-warning text-white"><h6 class="mb-0">Banner</h6></div><div class="card-body text-center"><div class="display-6 text-warning mb-2">${integer(banner.total_count || 0)}</div>${banner.banner && banner.banner.banner_name ? `<div class="alert alert-light mt-3 mb-0"><strong>Current:</strong> ${banner.banner.banner_name}</div>` : '<p class="text-muted mb-0">No active banner</p>'}</div></div></div>
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
        $('#dashboardStoreDetailsBody').html('<div class="text-center py-4">Loading...</div>');
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
        .fail(() => {
            $('#dashboardStoreDetailsBody').html('<div class="alert alert-danger">Failed to load store details.</div>');
        });
    });

    $('#refresh-dashboard').on('click', () => loadOverview(true));
    loadOverview();
})();
</script>
@endsection
