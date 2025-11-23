{{-- resources/views/dashboard/index.blade.php --}}
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
          <canvas id="storeSalesChart" height="120"></canvas>
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
  const TABLES = 'products,suppliers,cart_informtion,expense_details';
  const CHART_CDN = 'https://cdn.jsdelivr.net/npm/chart.js';
  const NO_LIMIT = true; // set true to not pass limit param, false to pass numeric limit
  const REQUEST_LIMIT =  '';

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
    // two decimals, comma group using en-IN for lakh formatting
    return '৳ ' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }

  // Best-effort date -> YYYY-MM-DD
  function normalizeDate(s) {
    if (!s) return null;
    const str = String(s).trim();
    const iso = str.match(/\d{4}-\d{2}-\d{2}/);
    if (iso) return iso[0];
    const dmy = str.match(/(\d{1,2})[^\d](\d{1,2})[^\d](\d{4})/);
    if (dmy) {
      const dd = String(dmy[1]).padStart(2,'0');
      const mm = String(dmy[2]).padStart(2,'0');
      const yy = dmy[3];
      return `${yy}-${mm}-${dd}`;
    }
    const y = str.match(/(19|20)\d{2}/);
    return y ? y[0] : null;
  }

  // Extract an array for a table name from various payload shapes
  function extractArray(payload, tableName) {
    if (!payload) return [];
    // shape: { results: { tablename: { data: [...] } } }
    if (payload.results && payload.results[tableName] && Array.isArray(payload.results[tableName].data)) {
      return payload.results[tableName].data;
    }
    // shape: { tablename: { data: [...] } }
    if (payload[tableName] && Array.isArray(payload[tableName].data)) {
      return payload[tableName].data;
    }
    // shape: { tablename: [...] }
    if (payload[tableName] && Array.isArray(payload[tableName])) {
      return payload[tableName];
    }
    // top-level array
    if (Array.isArray(payload)) return payload;
    // nested under data
    if (payload.data) return extractArray(payload.data, tableName);
    return [];
  }

  // Pick numeric values from cart rows (sensible fallbacks)
  function cartSalesValue(cart) {
    if (!cart) return 0;
    if (cart.final_total_amount != null) return toNumber(cart.final_total_amount);
    if (cart.total_payable_amount != null) return toNumber(cart.total_payable_amount);
    if (cart.total_cart_amount != null) return toNumber(cart.total_cart_amount);
    if (cart.paid_amount != null) return toNumber(cart.paid_amount);
    return 0;
  }
  function cartPaidValue(cart) {
    if (!cart) return 0;
    if (cart.paid_amount != null) return toNumber(cart.paid_amount);
    if (cart.final_total_amount != null) return toNumber(cart.final_total_amount);
    return 0;
  }
  function cartProfitValue(cart) {
    if (!cart) return 0;
    if (cart.net_profit != null) return toNumber(cart.net_profit);
    if (cart.gross_profit != null) return toNumber(cart.gross_profit);
    return 0;
  }

  // Expense amount extractor
  function expenseValue(row) {
    if (!row) return 0;
    if (row.amount != null) return toNumber(row.amount);
    return 0;
  }

  // -----------------------
  // Fetch all stores and then fetch remote data per store
  // -----------------------
  function fetchAllStoresData() {
    return $.get("{{ route('stores.index') }}").then(function (res) {
      if (!res || !res.ok) {
        log('Failed to load stores from mother app');
        return Promise.resolve([]);
      }
      const stores = res.data || [];
      $('#total-stores').text(stores.length);
      log('Found ' + stores.length + ' stores. Fetching remote data...');
      if (stores.length === 0) return Promise.resolve([]);

      // create an array of promises, each always resolves to per-store result object
      const promises = stores.map(function (st, idx) {
        // proxy url: omit limit param if NO_LIMIT true
        const base = '/stores/' + st.id + '/fetch-data?tables=' + encodeURIComponent(TABLES);
        const url = NO_LIMIT ? base : base + '&limit=' + encodeURIComponent(REQUEST_LIMIT);

        return $.ajax({
          url: url,
          method: 'GET',
          dataType: 'json',
          timeout: 25000,
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) {
          // normalize payload shape
          const payload = r.results ? r : (r.data ? r.data : r);

          // extract arrays
          const products = extractArray(payload, 'products');
          const suppliers = extractArray(payload, 'suppliers');
          const carts = extractArray(payload, 'cart_informtion');
          const expenseDetails = extractArray(payload, 'expense_details');
          const expenses = extractArray(payload, 'expenses');

          // now compute per-store metrics
          let storeProducts = products.length;
          let storeSuppliers = suppliers.length;

          // date helpers
          const today = new Date();
          const todayYMD = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
          const yesterday = new Date(today);
          yesterday.setDate(today.getDate() - 1);
          const yesterdayYMD = yesterday.getFullYear() + '-' + String(yesterday.getMonth() + 1).padStart(2, '0') + '-' + String(yesterday.getDate()).padStart(2, '0');

          let storeSales2025 = 0;
          let storeTodaySales = 0;
          let storeTodayIncome = 0;
          let storeYesterdaySales = 0;
          let storeYesterdayIncome = 0;
          let storeTotalProfit = 0;
          let storeTotalExpenses = 0;
          let storeYesterdayExpenses = 0;

          if (Array.isArray(carts)) {
            carts.forEach(function (cart) {
              const d = normalizeDate(cart.cart_date || cart.date || cart.cartDate || '');
              const sale = cartSalesValue(cart);
              const paid = cartPaidValue(cart); // income should be paid amount
              const profit = cartProfitValue(cart);

              storeTotalProfit += profit;

              // sales in year 2025
              if (d && d.startsWith('2025')) {
                storeSales2025 += sale;
              } else if (!d && String(cart.cart_date || '').includes('2025')) {
                storeSales2025 += sale;
              }

              if (d === todayYMD) {
                storeTodaySales += sale;
                storeTodayIncome += paid;
              }
              if (d === yesterdayYMD) {
                storeYesterdaySales += sale;
                storeYesterdayIncome += paid;
              }
            });
          }

          // expenses arrays (merge both shapes)
          const expensesArr = [];
          if (Array.isArray(expenseDetails)) expensesArr.push(...expenseDetails);
          if (Array.isArray(expenses)) expensesArr.push(...expenses);

          if (Array.isArray(expensesArr)) {
            expensesArr.forEach(function (e) {
              const amt = expenseValue(e);
              const d = normalizeDate(e.date || e.created_at || e.createdAt || '');
              storeTotalExpenses += amt;
              if (d === yesterdayYMD) storeYesterdayExpenses += amt;
            });
          }

          // return structured result for this store
          return {
            ok: true,
            storeId: st.id,
            storeName: st.name,
            productsCount: storeProducts,
            suppliersCount: storeSuppliers,
            sales2025: storeSales2025,
            todaySales: storeTodaySales,
            todayIncome: storeTodayIncome,
            yesterdaySales: storeYesterdaySales,
            yesterdayIncome: storeYesterdayIncome,
            totalExpenses: storeTotalExpenses,
            yesterdayExpenses: storeYesterdayExpenses,
            totalProfit: storeTotalProfit
          };
        }).catch(function (xhr) {
          // always resolve with a safe object on error
          log(`Store ${st.name} (${st.id}) fetch error: ${xhr && xhr.status ? xhr.status : 'network'}`);
          return {
            ok: false,
            storeId: st.id,
            storeName: st.name,
            productsCount: 0,
            suppliersCount: 0,
            sales2025: 0,
            todaySales: 0,
            todayIncome: 0,
            yesterdaySales: 0,
            yesterdayIncome: 0,
            totalExpenses: 0,
            yesterdayExpenses: 0,
            totalProfit: 0
          };
        });
      });

      // Promise.all -> single array of store results
      return Promise.all(promises);
    });
  }

  // -----------------------
  // Render table and aggregates
  // -----------------------
  function renderDashboard(storeResults) {
    // aggregates
    let aggProducts = 0, aggSuppliers = 0, aggSales2025 = 0;
    let aggTodaySales = 0, aggTodayIncome = 0, aggYesterdaySales = 0, aggYesterdayIncome = 0;
    let aggTotalExpenses = 0, aggYesterdayExpenses = 0, aggTotalProfit = 0;

    const $tbody = $('#stores-info-tbody').empty();

    storeResults.forEach(function (r, idx) {
      if (!r) return;
      // append row
      const $row = $(`
        <tr data-id="${r.storeId}">
          <td class="text-center">${String(idx+1).padStart(2,'0')}</td>
          <td class="text-center">${$('<div>').text(r.storeName).html()}</td>
          <td class="text-center" data-store-sales>-</td>
          <td class="text-center" data-store-profit>-</td>
          <td class="text-center" data-store-open>-</td>
        </tr>
      `);
      $tbody.append($row);

      // fill per-store values
      $row.find('[data-store-sales]').text(r.sales2025 ? formatTaka(r.sales2025) : '-');
      $row.find('[data-store-profit]').text(r.totalProfit ? formatTaka(r.totalProfit) : '-');

      // accumulate
      aggProducts += r.productsCount || 0;
      aggSuppliers += r.suppliersCount || 0;
      aggSales2025 += r.sales2025 || 0;
      aggTodaySales += r.todaySales || 0;
      aggTodayIncome += r.todayIncome || 0;
      aggYesterdaySales += r.yesterdaySales || 0;
      aggYesterdayIncome += r.yesterdayIncome || 0;
      aggTotalExpenses += r.totalExpenses || 0;
      aggYesterdayExpenses += r.yesterdayExpenses || 0;
      aggTotalProfit += r.totalProfit || 0;
    });

    // update top widgets
    $('#total-products').text(aggProducts || '-');
    $('#total-sales-year').text(aggSales2025 ? formatTaka(aggSales2025) : '-');
    $('#today-sales').text(aggTodaySales ? formatTaka(aggTodaySales) : '-');
    $('#today-income').text(aggTodayIncome ? formatTaka(aggTodayIncome) : '-');
    $('#total-expenses').text(aggTotalExpenses ? formatTaka(aggTotalExpenses) : '-');

    $('#yesterday-sales').text(aggYesterdaySales ? formatTaka(aggYesterdaySales) : '-');
    $('#yesterday-income').text(aggYesterdayIncome ? formatTaka(aggYesterdayIncome) : '-');
    $('#yesterday-expenses').text(aggYesterdayExpenses ? formatTaka(aggYesterdayExpenses) : '-');


    return storeResults;
  }

  // -----------------------
  // Chart: per-store sales for selected range
  // -----------------------
  let salesChartInstance = null;

  function ensureChartJs() {
    return new Promise(function (resolve, reject) {
      if (typeof Chart !== 'undefined') return resolve();
      const s = document.createElement('script');
      s.src = CHART_CDN;
      s.async = true;
      s.onload = () => resolve();
      s.onerror = () => reject(new Error('Failed to load Chart.js'));
      document.head.appendChild(s);
    });
  }

  function rangeToStartDate(range) {
    const now = new Date();
    const d = new Date(now);
    if (range === '7d') d.setDate(now.getDate() - 6);
    else if (range === '1m') d.setMonth(now.getMonth() - 1);
    else if (range === '6m') d.setMonth(now.getMonth() - 6);
    else if (range === '1y') d.setFullYear(now.getFullYear() - 1);
    else d.setMonth(now.getMonth() - 6);
    d.setHours(0,0,0,0);
    return d;
  }

  function isDateInRange(cartRow, startDate, endDate) {
    const raw = cartRow.cart_date || cartRow.date || cartRow.cartDate || '';
    const nd = normalizeDate(raw);
    if (!nd) return false;
    const d = new Date(nd);
    d.setHours(0,0,0,0);
    return d >= startDate && d <= endDate;
  }

  function calculateSalesPerStoreForRange(range) {
    const start = rangeToStartDate(range);
    const end = new Date(); end.setHours(23,59,59,999);

    // reuse fetchAllStoresData for store list and do per-store cart fetch
    return $.get("{{ route('stores.index') }}").then(function (res) {
      if (!res || !res.ok) return [];
      const stores = res.data || [];
      const tasks = stores.map(function (st) {
        const base = '/stores/' + st.id + '/fetch-data?tables=cart_informtion';
        const url = NO_LIMIT ? base : base + '&limit=' + encodeURIComponent(REQUEST_LIMIT);

        return $.ajax({ url, method: 'GET', dataType: 'json', timeout: 20000 }).then(function (r) {
          const payload = r.results ? r : (r.data ? r.data : r);
          let arr = [];
          if (payload.results && payload.results.cart_informtion && Array.isArray(payload.results.cart_informtion.data)) {
            arr = payload.results.cart_informtion.data;
          } else if (payload.cart_informtion && Array.isArray(payload.cart_informtion.data)) {
            arr = payload.cart_informtion.data;
          } else if (payload.cart_informtion && Array.isArray(payload.cart_informtion)) {
            arr = payload.cart_informtion;
          } else if (Array.isArray(payload)) arr = payload;

          let sum = 0;
          if (Array.isArray(arr)) {
            arr.forEach(function (row) {
              if (isDateInRange(row, start, end)) sum += cartSalesValue(row);
            });
          }
          return { storeId: st.id, name: st.name, total: sum };
        }).catch(function () {
          return { storeId: st.id, name: st.name, total: 0 };
        });
      });
      return Promise.all(tasks);
    });
  }

  function renderStoreSalesChart(items, range) {
    // sort desc
    items.sort((a,b) => b.total - a.total);
    const labels = items.map(i => i.name);
    const data = items.map(i => Number(i.total.toFixed(2)));

    const ctx = document.getElementById('storeSalesChart');
    if (!ctx) return;
    if (salesChartInstance) {
      try { salesChartInstance.destroy(); } catch(e) {}
      salesChartInstance = null;
    }

    salesChartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{ label: 'Sales (' + range + ')', data: data }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            ticks: {
              callback: function (value) { return '৳ ' + Number(value).toLocaleString('en-IN'); }
            }
          }
        },
        plugins: {
          tooltip: {
            callbacks: {
              label: function (ctx) {
                const v = ctx.parsed.x;
                return '৳ ' + Number(v).toLocaleString('en-IN');
              }
            }
          }
        }
      }
    });
  }

  function refreshChart(range) {
    $('.chart-filter .btn').removeClass('active');
    $('.chart-filter .btn[data-range="' + range + '"]').addClass('active');
    ensureChartJs().then(function () {
      return calculateSalesPerStoreForRange(range);
    }).then(function (items) {
      renderStoreSalesChart(items, range);
    }).catch(function (err) {
      console.error('Chart error', err);
    });
  }

  // init: load all store data, render dashboard, then load chart for default range
  fetchAllStoresData().then(function (storeResults) {
    renderDashboard(storeResults || []);
    // init chart after dashboard populated
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
