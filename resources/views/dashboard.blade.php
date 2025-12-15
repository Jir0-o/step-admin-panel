@extends('layouts.master')

@section('title', 'Dashboard || Step Shoe Pos')
@section('body-class', '')

@section('content')
  <!-- sidebar + nav are included by master -->

  <div class="row m-0 inria-sans">
    <div class="col-md-6" data-aos="fade-up-right" data-aos-duration="2000">
      <div class="row m-0">
        <div class="col-lg-12 pb-2">
          <div class="theme-alert card">
            <p class="m-0 p-2">
              You Have a New Notification.
              <a href="#" class="text-decoration-underline">Click For View</a>
            </p>
          </div>
        </div>

        <div class="col-lg-6 py-2">
          <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
              <div class="d-flex gap-3 align-items-center">
                <div class="icon-box">
                  <i class="ri-shopping-bag-line"></i>
                </div>
                <div class="info-text">
                  <p class="m-0 text-muted">Total Sales of 2025</p>
                  <h6 id="total-sales-year" class="m-0 fw-semibold">-</h6>
                </div>
              </div>
            </div>
            <p class="card-secondary text-center p-1 m-0">Last Year: <span id="last-year-sales">-</span></p>
          </div>
        </div>

        <div class="col-lg-6 py-2">
          <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
              <div class="d-flex gap-3 align-items-center">
                <div class="icon-box">
                  <i class="ri-currency-line"></i>
                </div>
                <div class="info-text">
                  <p class="m-0 text-muted">Today Sales</p>
                  <h6 id="today-sales" class="m-0 fw-semibold">-</h6>
                </div>
              </div>
            </div>
            <p class="card-secondary text-center p-1 m-0">Yesterday: <span id="yesterday-sales">-</span></p>
          </div>
        </div>

        <div class="col-lg-6 py-2">
          <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
              <div class="d-flex gap-3 align-items-center">
                <div class="icon-box">
                  <i class="ri-wallet-line"></i>
                </div>
                <div class="info-text">
                  <p class="m-0 text-muted">Today’s Income</p>
                  <h6 id="today-income" class="m-0 fw-semibold">-</h6>
                </div>
              </div>
            </div>
            <p class="card-secondary text-center p-1 m-0">Yesterday: <span id="yesterday-income">-</span></p>
          </div>
        </div>

        <div class="col-lg-6 py-2">
          <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
              <div class="d-flex gap-3 align-items-center">
                <div class="icon-box">
                  <i class="ri-receipt-line"></i>
                </div>
                <div class="info-text">
                  <p class="m-0 text-muted">Total Expenses</p>
                  <h6 id="total-expenses" class="m-0 fw-semibold">-</h6>
                </div>
              </div>
            </div>
            <p class="card-secondary text-center p-1 m-0">Yesterday: <span id="yesterday-expenses">-</span></p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6" data-aos="fade-up-left" data-aos-duration="2000">
      <div class="card theme-shadow overflow-hidden">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="fw-semibold">Store Sales Chart</h5>
            <div class="chart-filter d-flex gap-2">
              <button class="btn" data-range="7d">7D</button>
              <button class="btn" data-range="1m">1M</button>
              <button class="btn active" data-range="6m">6M</button>
              <button class="btn" data-range="1y">1Y</button>
            </div>
          </div>
          <div style="position: relative; height: 250px;">
            <canvas id="storeSalesChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6" data-aos="fade-up-right" data-aos-delay="2000" data-aos-duration="2000">
      <div class="p-2">
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
    </div>

    <div class="col-md-6" data-aos="fade-up-left" data-aos-delay="2000" data-aos-duration="2000">
      <div class="p-2">
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

  </div>

  <div class="p-4">
    <h5>Store Information</h5>
    <div class="card theme-shadow" data-aos="fade-up" data-aos-delay="4000" data-aos-duration="2000">
      <div class="table-responsive">
        <table class="table" id="stores-info-table">
          <thead>
            <tr class="text-center">
              <th class="bg-dark text-white inria-serif">SR</th>
              <th class="bg-dark text-white inria-serif">Store Name</th>
              <th class="bg-dark text-white inria-serif">Total Sale</th>
              <th class="bg-dark text-white inria-serif">Total Profit</th>
              <th class="bg-dark text-white inria-serif">Open At</th>
            </tr>
          </thead>
          <tbody id="stores-info-tbody">
            <!-- will be filled by JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

<script>
$(document).ready(function () {

  /* -------------------------------------------------
   | Config & Routes
   ------------------------------------------------- */
  const TABLES_SUMMARY = 'products,suppliers,cart_informtion,expense_details,banner_information';
  const CHART_CDN = 'https://cdn.jsdelivr.net/npm/chart.js';

  const ROUTES = {
    storesIndex : @json(route('stores.index')),
    storeSummary: @json(route('stores.fetch-summary', ['store' => '__ID__']))
  };

  /* -------------------------------------------------
   | State (single source of truth)
   ------------------------------------------------- */
  let STORES_CACHE = null;
  let STORE_SUMMARY_CACHE = null;
  let chartInstance = null;
  let chartBusy = false;
  let chartLoader = null;

  /* -------------------------------------------------
   | Helpers
   ------------------------------------------------- */
  const toNumber = v => Number.isFinite(Number(v)) ? Number(v) : 0;

  const formatTaka = v =>
    v ? '৳ ' + Number(v).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '-';

  function ensureChartJs() {
    if (window.Chart) return Promise.resolve();
    if (chartLoader) return chartLoader;

    chartLoader = new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = CHART_CDN;
      s.onload = resolve;
      s.onerror = reject;
      document.head.appendChild(s);
    });

    return chartLoader;
  }

  /* -------------------------------------------------
   | Fetch stores (once)
   ------------------------------------------------- */
  function getStores() {
    if (STORES_CACHE) return Promise.resolve(STORES_CACHE);

    return $.getJSON(ROUTES.storesIndex)
      .then(res => {
        STORES_CACHE = res.ok ? res.data : [];
        $('#total-stores').text(STORES_CACHE.length);
        return STORES_CACHE;
      })
      .catch(() => []);
  }

  /* -------------------------------------------------
   | Fetch ALL summaries ONCE (core fix)
   ------------------------------------------------- */
  function fetchAllStoreSummaries() {
    if (STORE_SUMMARY_CACHE) return Promise.resolve(STORE_SUMMARY_CACHE);

    return getStores().then(stores => {
      if (!stores.length) return [];

      const tasks = stores.map(store => {
        const url =
          ROUTES.storeSummary.replace('__ID__', store.id) +
          '?tables=' + encodeURIComponent(TABLES_SUMMARY);

        return $.getJSON(url)
          .then(r => {
            const res = r.data?.results || r.data || {};

            const cart = res.cart_informtion || {};
            const exp  = res.expense_details || {};

            return {
              storeId: store.id,
              storeName: res.banner_information?.banner?.banner_name || store.name,

              products: toNumber(res.products?.total_count),
              suppliers: toNumber(res.suppliers?.total_count),

              salesYear: toNumber(cart.total_amount_year),
              salesTotal: toNumber(cart.total_amount),
              todaySales: toNumber(cart.total_amount_today),
              yesterdaySales: toNumber(cart.total_amount_yesterday),

              profit: toNumber(cart.total_profit),
              expenses: toNumber(exp.total_amount),

              orderCount: toNumber(cart.total_count)
            };
          })
          .catch(() => ({
            storeId: store.id,
            storeName: store.name,
            products: 0,
            suppliers: 0,
            salesYear: 0,
            salesTotal: 0,
            todaySales: 0,
            yesterdaySales: 0,
            profit: 0,
            expenses: 0,
            orderCount: 0
          }));
      });

      return Promise.all(tasks).then(data => {
        STORE_SUMMARY_CACHE = data;
        return data;
      });
    });
  }

  /* -------------------------------------------------
   | Render Dashboard (cards + table)
   ------------------------------------------------- */
  function renderDashboard(data) {
    let totals = {
      products: 0,
      salesYear: 0,
      todaySales: 0,
      yesterdaySales: 0,
      expenses: 0,
      profit: 0
    };

    const $tbody = $('#stores-info-tbody').empty();

    data.forEach((s, i) => {
      totals.products += s.products;
      totals.salesYear += s.salesYear;
      totals.todaySales += s.todaySales;
      totals.yesterdaySales += s.yesterdaySales;
      totals.expenses += s.expenses;
      totals.profit += s.profit;

      $tbody.append(`
        <tr>
          <td class="text-center">${i + 1}</td>
          <td class="text-center">${s.storeName}</td>
          <td class="text-center">${formatTaka(s.salesYear)}</td>
          <td class="text-center">${formatTaka(s.profit)}</td>
          <td class="text-center">-</td>
        </tr>
      `);
    });

    $('#total-products').text(totals.products);
    $('#total-sales-year').text(formatTaka(totals.salesYear));
    $('#today-sales').text(formatTaka(totals.todaySales));
    $('#today-income').text(formatTaka(totals.todaySales));
    $('#yesterday-income').text(formatTaka(totals.yesterdaySales));
    $('#yesterday-sales').text(formatTaka(totals.yesterdaySales));
    $('#total-expenses').text(formatTaka(totals.expenses));
  }

  /* -------------------------------------------------
   | Chart (uses cached data ONLY)
   ------------------------------------------------- */
  function renderChart(range) {
    if (chartBusy || !STORE_SUMMARY_CACHE) return;
    chartBusy = true;

    const map = {
      '7d': 'todaySales',
      '1m': 'salesTotal',
      '6m': 'salesYear',
      '1y': 'salesYear'
    };

    const field = map[range] || 'salesYear';

    const labels = STORE_SUMMARY_CACHE.map(s => s.storeName);
    const values = STORE_SUMMARY_CACHE.map(s => s[field]);

    ensureChartJs().then(() => {
      const ctx = document.getElementById('storeSalesChart');

      if (chartInstance) chartInstance.destroy();

      chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            data: values,
            backgroundColor: 'rgba(54,162,235,.7)'
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true } }
        }
      });

      chartBusy = false;
    });
  }

  /* -------------------------------------------------
   | Init
   ------------------------------------------------- */
  fetchAllStoreSummaries().then(data => {
    renderDashboard(data);
    renderChart('6m');
  });

  $(document).on('click', '.chart-filter .btn', function () {
    $('.chart-filter .btn').removeClass('active');
    $(this).addClass('active');
    renderChart($(this).data('range'));
  });

});
</script>
@endsection
