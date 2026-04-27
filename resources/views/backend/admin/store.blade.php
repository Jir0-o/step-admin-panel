@extends('layouts.master')

@section('title', 'Dashboard || Step Shoe Pos')
@section('body-class', '')

@section('content')
<div class="row m-0">
    <div class="col-md-6" data-aos="fade-up-right" data-aos-duration="2000">
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

    <div class="col-md-6" data-aos="fade-up-left" data-aos-duration="2000">
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
                <tbody id="stores-info-tbody">
                    <tr><td colspan="7" class="text-center py-5 text-muted">Loading store overview...</td></tr>
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

<script>
(() => {
    const overviewUrl = @json(route('dashboard.overview'));
    const summaryRoute = @json(route('stores.fetch-summary', ['store' => '__ID__']));
    const stockRoute = @json(route('manager.stock-data.index', ['store' => '__ID__']));
    const salesRoute = @json(route('manager.sales.index', ['store' => '__ID__']));
    const TABLES = 'products,suppliers,cart_informtion,expense_details,banner_information,salesman_target_summary';

    const $logBox = $('#store-aggregates').empty();
    let overviewRows = {};

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
                                        <h5 class="mb-1">${storeName || 'Store'}</h5>
                                        <p class="mb-0 text-muted">Store ID: ${storeId || '-'} | Last Updated: ${now.toLocaleString()}</p>
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
                                <h4 class="mt-2 mb-0">${taka(targetInfo.store_target_monthly || 0)}</h4>
                                <small>Achieved: ${taka(targetInfo.monthly_achievement || 0)}</small>
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
                                <h4 class="mt-2 mb-0">${taka(targetInfo.store_target_yearly || 0)}</h4>
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

    function loadOverview(forceRefresh = false) {
        overviewRows = {};
        $logBox.empty();
        $('#stores-info-tbody').html('<tr><td colspan="7" class="text-center py-5 text-muted">Loading store overview...</td></tr>');

        $.getJSON(forceRefresh ? `${overviewUrl}?refresh=1` : overviewUrl)
            .done((response) => {
                const data = response?.data || {};
                const totals = data.totals || {};
                const stores = Array.isArray(data.stores) ? data.stores : [];

                $('#total-stores').text(integer(totals.store_count || stores.length));
                $('#total-products').text(integer(totals.products || 0));

                if (!stores.length) {
                    $('#stores-info-tbody').html('<tr><td colspan="7" class="text-center py-5 text-muted">No stores found.</td></tr>');
                    return;
                }

                const rows = stores.map((store, index) => {
                    overviewRows[String(store.id)] = store;
                    const statusBadge = store.ok
                        ? '<span class="badge bg-success">Ready</span>'
                        : `<span class="badge bg-danger">${store.message || 'Failed'}</span>`;

                    if (store.ok) {
                        log(`Store ${store.name} synced`);
                    } else {
                        log(`Store ${store.name} failed: ${store.message || 'Unknown error'}`);
                    }

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

                $('#stores-info-tbody').html(rows);
            })
            .fail(() => {
                log('Store overview API failed');
                $('#stores-info-tbody').html('<tr><td colspan="7" class="text-center py-5 text-danger">Failed to load store overview.</td></tr>');
            });
    }

    function loadStoreDetails(storeId, storeName) {
        $('#storeDetailsModalLabel').text(`${storeName} - Store Details`);
        $('#storeDetailsContent').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                <p class="mt-2">Loading store data...</p>
            </div>`);
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
            timeout: 20000,
            headers: { Accept: 'application/json' }
        })
        .done((response) => renderStoreDetailsFromSource(response?.data || response, storeName, storeId))
        .fail((jqXHR, textStatus) => {
            $('#storeDetailsContent').html(`<div class="alert alert-danger"><i class="ri-error-warning-line me-2"></i>Failed to load store data: ${textStatus}</div>`);
        });
    }

    $(document).on('click', '.js-view-store', function (e) {
        e.preventDefault();
        loadStoreDetails($(this).data('store-id'), $(this).data('store-name'));
    });

    $(document).on('click', '#exportStoreDetailsBtn', downloadStoreDetails);
    $(document).on('click', '#printStoreDetailsBtn', printStoreDetails);
    $('#refresh-store-overview').on('click', () => loadOverview(true));

    loadOverview();
})();
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

.store-details-content {
    max-height: 70vh;
    overflow-y: auto;
    padding-right: 10px;
}
</style>
@endsection
