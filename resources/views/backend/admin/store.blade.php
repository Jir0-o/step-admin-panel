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

<!-- Store Information -->
<div class="p-4">
    <h5>Store Information</h5>
    <div class="card theme-shadow" data-aos="fade-up" data-aos-delay="2000" data-aos-duration="2000">
        <div class="table-responsive">
            <table class="table align-middle" id="stores-info-table">
                <thead>
                    <tr class="text-center">
                        <th class="bg-dark text-white inria-serif">SR</th>
                        <th class="bg-dark text-white inria-serif">Store Name</th>
                        <th class="bg-dark text-white inria-serif">Total Sale (2025)</th>
                        <th class="bg-dark text-white inria-serif">Total Profit</th>
                        <th class="bg-dark text-white inria-serif">Action</th>
                    </tr>
                </thead>
                <tbody id="stores-info-tbody">
                    <!-- filled by JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- log / aggregates area -->
    <div id="store-aggregates" class="mt-3"></div>
</div>

<!-- jQuery required by your code -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
window.ROUTES = {
    storeSummary: "{{ route('stores.fetch-summary', ['store' => '__ID__']) }}",
    storeDetails: "{{ route('store.details', ['store' => '__ID__']) }}"
};
</script>

<script>
$(function () {

    const TABLES = 'products,suppliers,cart_informtion,expense_details,banner_information';
    const $logBox = $('#store-aggregates').empty();

    function log(msg) {
        console.log('[dashboard]', msg);
        $logBox.append(`<div class="small text-muted">${msg}</div>`);
    }

    function num(v) {
        v = Number(v);
        return Number.isFinite(v) ? v : 0;
    }

    function taka(v) {
        if (!v) return '-';
        return '৳ ' + v.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function summaryUrl(storeId) {
        return window.ROUTES.storeSummary.replace('__ID__', storeId);
    }

    function fetchStoreSummary(storeId) {
        const url = summaryUrl(storeId);

        // Try GET first
        return $.ajax({
            url,
            method: 'GET',
            data: { tables: TABLES },
            dataType: 'json',
            timeout: 15000,
            headers: { 'Accept': 'application/json' }
        }).catch(() => {
            // fallback POST
            return $.ajax({
                url,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ tables: TABLES }),
                dataType: 'json',
                timeout: 20000,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    }

    // ================= LOAD STORES =================
    $.get("{{ route('stores.index') }}")
        .done(function (res) {

            if (!res?.ok || !Array.isArray(res.data)) {
                log('Failed to load stores');
                return;
            }

            const stores = res.data;
            $('#total-stores').text(stores.length);

            let totals = {
                products: 0,
                suppliers: 0,
                salesYear: 0,
                profit: 0,
                expenses: 0
            };

            const $tbody = $('#stores-info-tbody').empty();
            let completed = 0;

            stores.forEach((store, i) => {

                const $row = $(`
                    <tr>
                        <td class="text-center">${String(i + 1).padStart(2, '0')}</td>
                        <td class="text-center">${store.name}</td>
                        <td class="text-center" data-sales>-</td>
                        <td class="text-center" data-profit>-</td>
                        <td class="text-center">
                            <a href="${window.ROUTES.storeDetails.replace('__ID__', store.id)}" 
                               class="btn btn-success px-2 py-1 btn-view" 
                               data-store-id="${store.id}">
                                <i class="ri-eye-fill"></i> View Details
                            </a>
                        </td>
                    </tr>
                `);

                $tbody.append($row);

                fetchStoreSummary(store.id)
                    .done(function (r) {

                        const data = r?.data?.results || r?.data || r?.results || {};

                        const cart = data.cart_informtion || {};
                        const exp  = data.expense_details || {};

                        const salesYear = num(cart.total_amount_year);
                        const profit    = num(cart.total_profit);
                        const expenses  = num(exp.total_amount);

                        totals.products  += num(data.products?.total_count);
                        totals.suppliers += num(data.suppliers?.total_count);
                        totals.salesYear += salesYear;
                        totals.profit    += profit;
                        totals.expenses  += expenses;

                        $row.find('[data-sales]').text(taka(salesYear));
                        $row.find('[data-profit]').text(taka(profit));

                        log(`Store ${store.name} synced`);
                    })
                    .fail(() => {
                        log(`Store ${store.name} failed`);
                    })
                    .always(() => {
                        completed++;
                        if (completed === stores.length) finalize();
                    });
            });

            function finalize() {
                $('#total-products').text(totals.products || '-');
                log('All stores processed');
            }
        })
        .fail(() => log('Store index API failed'));

});
</script>
@endsection