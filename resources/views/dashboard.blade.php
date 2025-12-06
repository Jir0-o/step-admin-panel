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
  // -----------------------
  // Configuration
  // -----------------------
  const TABLES_SUMMARY = 'products,suppliers,cart_informtion,expense_details,banner_information';
  const CHART_CDN = 'https://cdn.jsdelivr.net/npm/chart.js';
  const NO_LIMIT = true; // not used for summary but kept for parity
  const REQUEST_LIMIT = '';

  // UI containers
  const $summaryContainer = $('<div id="store-aggregates" class="mt-3"></div>');
  $('.p-4').first().append($summaryContainer);

  // -----------------------
  // Small helpers (clear & beginner-friendly)
  // -----------------------
  function log(msg) {
    $summaryContainer.append($('<div class="small text-muted"></div>').text(msg));
    console.log('[dashboard] ' + msg);
  }

  function toNumber(v) {
    const n = Number(v);
    return Number.isFinite(n) ? n : 0;
  }

  function formatTaka(v) {
    const n = toNumber(v);
    if (n === 0) return '-';
    return '৳ ' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }

  function isoDate(d) {
    if (!(d instanceof Date)) return null;
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
  }

  // -----------------------
  // Shared state & utilities
  // -----------------------
  let STORES_CACHE = null;
  let ensureChartPromise = null;
  let salesChartInstance = null;
  let chartBusy = false;

  // idempotent Chart.js loader
  function ensureChartJs() {
    if (typeof Chart !== 'undefined') return Promise.resolve();
    if (ensureChartPromise) return ensureChartPromise;

    ensureChartPromise = new Promise(function (resolve, reject) {
      const s = document.createElement('script');
      s.src = CHART_CDN;
      s.async = true;
      s.onload = function () {
        ensureChartPromise = null;
        resolve();
      };
      s.onerror = function () {
        ensureChartPromise = null;
        reject(new Error('Failed to load Chart.js'));
      };
      document.head.appendChild(s);
    });

    return ensureChartPromise;
  }

  // get stores list (cached)
  function getStoresList() {
    if (STORES_CACHE) return Promise.resolve(STORES_CACHE);
    return $.get("{{ route('stores.index') }}").then(function (res) {
      if (!res || !res.ok) return [];
      STORES_CACHE = res.data || [];
      $('#total-stores').text(STORES_CACHE.length);
      return STORES_CACHE;
    }).catch(function () {
      return [];
    });
  }

  // small concurrency runner (p-limit style)
  function runWithLimit(tasks, limit = 6) {
    let i = 0;
    const results = new Array(tasks.length);
    const active = [];

    function next() {
      if (i >= tasks.length) return Promise.resolve();
      const idx = i++;
      const p = Promise.resolve().then(() => tasks[idx]()).then(res => { results[idx] = res; });
      active.push(p);
      const cleanup = () => {
        const pos = active.indexOf(p);
        if (pos !== -1) active.splice(pos, 1);
      };
      p.then(cleanup, cleanup);
      let slot = Promise.resolve();
      if (active.length >= limit) slot = Promise.race(active);
      return slot.then(next);
    }

    return next().then(() => Promise.all(active)).then(() => results);
  }

  // -----------------------
  // Date helpers for ranges
  // -----------------------
  function rangeToStartDate(range) {
    const now = new Date();
    const d = new Date(now);
    if (range === '7d') d.setDate(now.getDate() - 6);
    else if (range === '1m') d.setMonth(now.getMonth() - 1);
    else if (range === '6m') d.setMonth(now.getMonth() - 6);
    else if (range === '1y') d.setFullYear(now.getFullYear() - 1);
    else d.setMonth(now.getMonth() - 6);
    d.setHours(0, 0, 0, 0);
    return d;
  }

  // -----------------------
  // New: fetch aggregated summary for all stores (fast)
  // Uses store proxy: GET /stores/{id}/fetch-summary?tables=...
  // -----------------------
function fetchAllStoresSummary() {
  return getStoresList().then(function (stores) {
    if (!stores || stores.length === 0) {
      log('No stores found or failed to fetch stores');
      return Promise.resolve([]);
    }
    log('Found ' + stores.length + ' stores. Fetching summaries...');

    // tasks: each calls /stores/{id}/fetch-summary?tables=...
    const tasks = stores.map(function (st) {
      return function () {
        const url = '/stores/' + st.id + '/fetch-summary?tables=' + encodeURIComponent('products,suppliers,cart_informtion,expense_details,banner_information');
        return $.ajax({ url: url, method: 'GET', dataType: 'json', timeout: 15000 })
          .then(function (r) {
            // 'r' is proxy wrapper; actual POS payload is usually in r.data or r.results
            const payload = r && r.data ? r.data : (r || {});
            const results = payload.results || payload;

            // banner name: try multiple places
            let bannerName = st.banner_name || st.name || ('Store ' + st.id);
            if (results && results.banner_information && results.banner_information.banner) {
              bannerName = results.banner_information.banner.banner_name || bannerName;
            } else if (results && results.banner_information && results.banner_information.total_count && payload.banner_information && payload.banner_information.data && payload.banner_information.data.length) {
              // fallback: if banner returned in data array
              const b = payload.banner_information.data[0];
              if (b && b.banner_name) bannerName = b.banner_name;
            }

            // cart sums
            const cart = results && results.cart_informtion ? results.cart_informtion : (payload.cart_informtion || {});
            const totalSales = toNumber(cart.total_amount || cart.total_amount_year || 0);
            const totalSalesYear = toNumber(cart.total_amount_year || 0);
            const todaySales = toNumber(cart.total_amount_today || 0);
            const yesterdaySales = toNumber(cart.total_amount_yesterday || 0);
            const totalProfit = toNumber(cart.total_profit || 0);
            const totalCartCount = Number(cart.total_count || 0);

            // expenses
            const exp = results && results.expense_details ? results.expense_details : (payload.expense_details || {});
            const totalExpenses = toNumber(exp.total_amount || 0);

            // products / suppliers counts
            const productsCount = results && results.products ? Number(results.products.total_count || 0) : 0;
            const suppliersCount = results && results.suppliers ? Number(results.suppliers.total_count || 0) : 0;

            return {
              ok: true,
              storeId: st.id,
              storeName: bannerName,
              productsCount: productsCount,
              suppliersCount: suppliersCount,
              salesThisYear: totalSalesYear || totalSales, // prefer year field
              salesTotal: totalSales,
              todaySales: todaySales,
              yesterdaySales: yesterdaySales,
              todayIncome: todaySales, // per your instruction paid_amount is income
              yesterdayIncome: yesterdaySales,
              totalExpenses: totalExpenses,
              totalProfit: totalProfit,
              totalCartCount: totalCartCount
            };
          })
          .catch(function (err) {
            log('Store ' + (st.name || st.id) + ' summary fetch error: ' + (err && err.status ? err.status : 'network'));
            return {
              ok: false,
              storeId: st.id,
              storeName: st.banner_name || st.name || ('Store ' + st.id),
              productsCount: 0,
              suppliersCount: 0,
              salesThisYear: 0,
              salesTotal: 0,
              todaySales: 0,
              yesterdaySales: 0,
              todayIncome: 0,
              yesterdayIncome: 0,
              totalExpenses: 0,
              totalProfit: 0,
              totalCartCount: 0
            };
          });
      };
    });

    return runWithLimit(tasks, 6);
  });
}

  // -----------------------
  // Calculate per-store counts/amounts for a date range using summary (fast)
  // We rely on summary endpoint supporting date_from/date_to filters (ISO Y-M-D)
  // -----------------------
  function calculateSalesPerStoreForRange(range) {
    const start = rangeToStartDate(range);
    const end = new Date(); end.setHours(23, 59, 59, 999);

    const dateFrom = isoDate(start);
    const dateTo = isoDate(end);

    return getStoresList().then(function (stores) {
      if (!stores || stores.length === 0) return [];

      const tasks = stores.map(function (st) {
        return function () {
          const url = `/stores/${st.id}/fetch-summary?tables=cart_informtion&date_from=` + encodeURIComponent(dateFrom) + '&date_to=' + encodeURIComponent(dateTo);
          return $.ajax({ url: url, method: 'GET', dataType: 'json', timeout: 15000 })
            .then(function (r) {
              const payload = r && r.data ? r.data : (r || {});
              const results = payload.results || payload;
              let amount = 0;
              let count = 0;

              if (results && results.cart_informtion) {
                amount = toNumber(results.cart_informtion.total_amount || results.cart_informtion.total || 0);
                count = Number(results.cart_informtion.total_count || 0);
              } else if (payload && payload.cart_informtion) {
                amount = toNumber(payload.cart_informtion.total_amount || 0);
                count = Number(payload.cart_informtion.total_count || 0);
              }

              return { storeId: st.id, name: st.banner_name || st.name || `Store ${st.id}`, totalCount: count, totalAmount: amount };
            })
            .catch(function (err) {
              console.warn('summary fetch error for store', st.id, err && err.status);
              return { storeId: st.id, name: st.banner_name || st.name || `Store ${st.id}`, totalCount: 0, totalAmount: 0 };
            });
        };
      });

      return runWithLimit(tasks, 6);
    }).catch(function () {
      return [];
    });
  }

  // -----------------------
  // Rendering helpers (unchanged, but reading new fields)
  // -----------------------
  function renderDashboard(storeResults) {
    let aggProducts = 0, aggSuppliers = 0, aggSalesThisYear = 0;
    let aggTodaySales = 0, aggTodayIncome = 0, aggYesterdaySales = 0, aggYesterdayIncome = 0;
    let aggTotalExpenses = 0, aggYesterdayExpenses = 0, aggTotalProfit = 0;

    const $tbody = $('#stores-info-tbody').empty();

    storeResults.forEach(function (r, idx) {
      if (!r) return;
      const $row = $(`
        <tr data-id="${r.storeId}">
          <td class="text-center">${String(idx + 1).padStart(2, '0')}</td>
          <td class="text-center">${$('<div>').text(r.storeName).html()}</td>
          <td class="text-center" data-store-sales>-</td>
          <td class="text-center" data-store-profit>-</td>
          <td class="text-center" data-store-open>-</td>
        </tr>
      `);
      $tbody.append($row);

      $row.find('[data-store-sales]').text(r.salesThisYear ? formatTaka(r.salesThisYear) : '-');
      $row.find('[data-store-profit]').text(r.totalProfit ? formatTaka(r.totalProfit) : '-');

      aggProducts += r.productsCount || 0;
      aggSuppliers += r.suppliersCount || 0;
      aggSalesThisYear += r.salesThisYear || 0;
      aggTodaySales += r.todaySales || 0;
      aggTodayIncome += r.todayIncome || 0;
      aggYesterdaySales += r.yesterdaySales || 0;
      aggYesterdayIncome += r.yesterdayIncome || 0;
      aggTotalExpenses += r.totalExpenses || 0;
      aggYesterdayExpenses += r.yesterdayExpenses || 0;
      aggTotalProfit += r.totalProfit || 0;
    });

    $('#total-products').text(aggProducts || '-');
    $('#total-sales-year').text(aggSalesThisYear ? formatTaka(aggSalesThisYear) : '-');
    $('#today-sales').text(aggTodaySales ? formatTaka(aggTodaySales) : '-');
    $('#today-income').text(aggTodayIncome ? formatTaka(aggTodayIncome) : '-');
    $('#total-expenses').text(aggTotalExpenses ? formatTaka(aggTotalExpenses) : '-');

    $('#yesterday-sales').text(aggYesterdaySales ? formatTaka(aggYesterdaySales) : '-');
    $('#yesterday-income').text(aggYesterdayIncome ? formatTaka(aggYesterdayIncome) : '-');
    $('#yesterday-expenses').text(aggYesterdayExpenses ? formatTaka(aggYesterdayExpenses) : '-');

    return storeResults;
  }

  // -----------------------
  // Chart: render counts (reused from previous improved version)
  // -----------------------
  function renderStoreSalesChart(items, range) {
    items = Array.isArray(items) ? items.slice() : [];
    const canvasEl = document.getElementById('storeSalesChart');
    if (!canvasEl) return;

    const existing = (typeof Chart !== 'undefined') ? Chart.getChart(canvasEl) : null;
    if (existing) {
      try { existing.destroy(); } catch (e) { console.warn('destroy failed', e); }
    }
    salesChartInstance = null;

    if (items.length === 0) {
      const ctx = canvasEl.getContext && canvasEl.getContext('2d');
      if (ctx) ctx.clearRect(0, 0, canvasEl.width, canvasEl.height);
      return;
    }

    items = items.map(it => ({ storeId: it.storeId, name: it.name || 'Store', count: Number(it.totalCount || 0) }));
    items.sort((a, b) => b.count - a.count);

    const TOP_N = 10;
    const isSingle = items.length === 1;
    const displayItems = isSingle ? items : items.slice(0, TOP_N);
    const labels = displayItems.map(i => i.name);
    const data = displayItems.map(i => i.count);
    const indexAxis = isSingle ? 'y' : 'x';

    const chartWrapper = canvasEl.parentElement;
    if (chartWrapper) {
      chartWrapper.style.position = 'relative';
      const baseHeight = 250;
      const perBar = 28;
      const desired = Math.max(baseHeight, Math.min(800, displayItems.length * perBar));
      chartWrapper.style.height = desired + 'px';
    }

    salesChartInstance = new Chart(canvasEl, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Sales Count (' + range + ')',
          data: data,
          backgroundColor: 'rgba(54,162,235,0.65)',
          borderColor: 'rgba(54,162,235,1)',
          borderWidth: 1
        }]
      },
      options: {
        indexAxis: indexAxis,
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (ctx) {
                const val = ctx.parsed && (ctx.parsed.x ?? ctx.parsed.y);
                return (ctx.dataset.label ? ctx.dataset.label + ': ' : '') + Number(val || 0).toLocaleString('en-IN') + ' orders';
              }
            }
          }
        },
        scales: {
          x: (indexAxis === 'x') ? {
            beginAtZero: true,
            ticks: { callback: v => Number(v).toLocaleString('en-IN'), maxTicksLimit: 8 },
            grid: { display: true, drawBorder: false }
          } : { display: true, grid: { display: true, drawBorder: false } },

          y: (indexAxis === 'y') ? {
            beginAtZero: true,
            ticks: { callback: v => Number(v).toLocaleString('en-IN'), maxTicksLimit: 8 },
            grid: { display: true, drawBorder: false }
          } : { display: true, grid: { display: true, drawBorder: false } }
        },
        layout: { padding: { top: 6, right: 12, left: 6, bottom: 6 } },
        elements: { bar: { borderSkipped: false } }
      }
    });
  }

  // -----------------------
  // refreshChart uses summary with date range parameters
  // -----------------------
  function refreshChart(range) {
    if (chartBusy) return;
    chartBusy = true;
    $('.chart-filter .btn').removeClass('active');
    $('.chart-filter .btn[data-range="' + range + '"]').addClass('active');

    ensureChartJs()
      .then(() => calculateSalesPerStoreForRange(range))
      .then(function (items) {
        renderStoreSalesChart(items, range);
      })
      .catch(function (err) {
        console.error('Chart error', err);
      })
      .finally(function () {
        setTimeout(function () { chartBusy = false; }, 250);
      });
  }

  // -----------------------
  // Init: use summary fetch to populate dashboard and chart
  // -----------------------
  fetchAllStoresSummary().then(function (storeResults) {
    renderDashboard(storeResults || []);
    const defaultRange = $('.chart-filter .btn.active').data('range') || '6m';
    refreshChart(defaultRange);
  });

  // chart range button click
  $(document).on('click', '.chart-filter .btn', function () {
    const r = $(this).data('range') || '6m';
    refreshChart(r);
  });

});
</script>


@endsection
