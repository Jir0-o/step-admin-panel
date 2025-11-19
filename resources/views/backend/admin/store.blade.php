{{-- resources/views/dashboard/index.blade.php --}}
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

<!-- KPI row -->
<div class="row m-0 mt-3">
  <div class="col-md-3">
    <div class="card theme-shadow">
      <div class="card-body">
        <p class="m-0 text-muted">Total Sales of 2025</p>
        <h6 id="total-sales-year" class="m-0 fw-semibold">-</h6>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card theme-shadow">
      <div class="card-body">
        <p class="m-0 text-muted">Today Sales</p>
        <h6 id="today-sales" class="m-0 fw-semibold">-</h6>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card theme-shadow">
      <div class="card-body">
        <p class="m-0 text-muted">Today’s Income</p>
        <h6 id="today-income" class="m-0 fw-semibold">-</h6>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card theme-shadow">
      <div class="card-body">
        <p class="m-0 text-muted">Total Expenses</p>
        <h6 id="total-expenses" class="m-0 fw-semibold">-</h6>
      </div>
    </div>
  </div>
</div>

<!-- Yesterday / Last Year line -->
<div class="row m-0 mt-2">
  <div class="col-md-4">
    <p class="small text-muted">Last Year Total Sales: <span id="last-year-sales">-</span></p>
  </div>
  <div class="col-md-4">
    <p class="small text-muted">Yesterday Sales: <span id="yesterday-sales">-</span></p>
  </div>
  <div class="col-md-4">
    <p class="small text-muted">Yesterday Income: <span id="yesterday-income">-</span></p>
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
$(function(){

  // -----------------------
  // Config: remote tables to ask each store for
  // -----------------------
  const tables = 'products,suppliers,cart_informtion,expense_details';

  // append a small area for logs & aggregated summary
  const $summaryContainer = $('#store-aggregates').empty();

  // simple logger helper
  function log(msg){
    $summaryContainer.append($('<div class="small text-muted"></div>').text(msg));
    console.log('[dashboard] ' + msg);
  }

  // defensive extractor: find array for tableName in many possible API shapes
  function extractArray(payload, tableName){
    if (!payload) return [];

    // shape: { results: { table: { data: [...] } } }
    if (payload.results && payload.results[tableName] && Array.isArray(payload.results[tableName].data)) {
      return payload.results[tableName].data;
    }

    // shape: { table: { data: [...] } }
    if (payload[tableName] && Array.isArray(payload[tableName].data)) {
      return payload[tableName].data;
    }

    // shape: { data: { table: { data: [...] } } }
    if (payload.data) return extractArray(payload.data, tableName);

    // payload itself is an array -> single-table response
    if (Array.isArray(payload)) return payload;

    return [];
  }

  // numeric helpers
  function toNumber(v){ const n = Number(v); return Number.isFinite(n) ? n : 0; }
  function formatTaka(val){
    const n = toNumber(val);
    if (n === 0) return '-';
    return '৳ ' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }

  // normalize date strings to YYYY-MM-DD (best-effort)
  function normalizeDate(dstr){
    if (!dstr) return null;
    const s = String(dstr).trim();
    const iso = s.match(/\d{4}-\d{2}-\d{2}/);
    if (iso) return iso[0];
    const dmy = s.match(/(\d{1,2})[^\d](\d{1,2})[^\d](\d{4})/);
    if (dmy) {
      const dd = String(dmy[1]).padStart(2,'0');
      const mm = String(dmy[2]).padStart(2,'0');
      const yy = dmy[3];
      return `${yy}-${mm}-${dd}`;
    }
    const y = s.match(/(19|20)\d{2}/);
    return y ? y[0] : null;
  }

  // pickive value extractors from cart rows (safe)
  function cartSalesValue(cart){
    if (!cart) return 0;
    if (cart.final_total_amount != null) return toNumber(cart.final_total_amount);
    if (cart.total_payable_amount != null) return toNumber(cart.total_payable_amount);
    if (cart.total_cart_amount != null) return toNumber(cart.total_cart_amount);
    if (cart.paid_amount != null) return toNumber(cart.paid_amount);
    return 0;
  }
  // use paid_amount for income (fixed)
  function cartPaidValue(cart){
    if (!cart) return 0;
    if (cart.paid_amount != null) return toNumber(cart.paid_amount);
    if (cart.final_total_amount != null) return toNumber(cart.final_total_amount);
    return 0;
  }
  function cartProfitValue(cart){
    if (!cart) return 0;
    if (cart.net_profit != null) return toNumber(cart.net_profit);
    if (cart.gross_profit != null) return toNumber(cart.gross_profit);
    return 0;
  }
  function expenseValue(row){
    if (!row) return 0;
    if (row.amount != null) return toNumber(row.amount);
    return 0;
  }

  // -----------------------
  // Main: fetch stores list then query each store via proxy
  // -----------------------
  $.get("{{ route('stores.index') }}")
    .done(function(res){
      if (!res || !res.ok){
        log('Failed to load stores from mother app');
        return;
      }

      const stores = res.data || [];
      $('#total-stores').text(stores.length);
      log('Found ' + stores.length + ' stores. Fetching remote data...');

      if (stores.length === 0) return;

      // aggregated counters across all stores
      let aggProducts = 0;
      let aggSuppliers = 0;
      let aggSales2025 = 0;
      let aggTodaySales = 0;
      let aggTodayIncome = 0;
      let aggYesterdaySales = 0;
      let aggYesterdayIncome = 0;
      let aggTotalExpenses = 0;
      let aggYesterdayExpenses = 0;
      let aggTotalProfit = 0;

      const today = new Date();
      const todayYMD = today.getFullYear() + '-' + String(today.getMonth()+1).padStart(2,'0') + '-' + String(today.getDate()).padStart(2,'0');
      const yesterday = new Date(today); yesterday.setDate(today.getDate() - 1);
      const yesterdayYMD = yesterday.getFullYear() + '-' + String(yesterday.getMonth()+1).padStart(2,'0') + '-' + String(yesterday.getDate()).padStart(2,'0');

      const $tbody = $('#stores-info-tbody').empty();
      let completed = 0;

      stores.forEach(function(st, idx){
        const storeId = st.id;

        // placeholder row
        const $row = $(`
          <tr data-id="${storeId}">
            <td class="text-center">${String(idx+1).padStart(2,'0')}</td>
            <td class="text-center">${$('<div>').text(st.name).html()}</td>
            <td class="text-center" data-store-sales>-</td>
            <td class="text-center" data-store-profit>-</td>
            <td class="text-center">
              <button class="btn btn-success px-2 py-1 btn-view" data-store-id="${storeId}">
                <i class="ri-eye-fill"></i> View
              </button>
            </td>
          </tr>
        `);
        $tbody.append($row);

        // proxy URL - mother app must implement this route and use stored tokens
        const url = '/stores/' + storeId + '/fetch-data?tables=' + encodeURIComponent(tables);

        // call the proxy (GET)
        $.ajax({
          url: url,
          method: 'GET',
          dataType: 'json',
          timeout: 20000,
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).done(function(r){
          if (!(r && r.ok)){
            log(`Store ${st.name} (${storeId}) returned error: ${r?.message || 'no-ok'}`);
            return;
          }

          // payload normalization: some endpoints embed results under r.results or r.data
          const payload = r.results ? r : (r.data ? r.data : r);

          // extract arrays
          const products = extractArray(payload, 'products');
          const suppliers = extractArray(payload, 'suppliers');
          const carts = extractArray(payload, 'cart_informtion');
          // expense_details or expenses may be present
          const expenseDetails = extractArray(payload, 'expense_details').concat(extractArray(payload, 'expenses'));

          // counts
          const pCount = products.length;
          const sCount = suppliers.length;
          aggProducts += pCount;
          aggSuppliers += sCount;

          // per-store aggregations
          let storeSales2025 = 0;
          let storeTodaySales = 0;
          let storeTodayIncome = 0;
          let storeYesterdaySales = 0;
          let storeYesterdayIncome = 0;
          let storeProfitTotal = 0;
          let storeExpensesTotal = 0;
          let storeYesterdayExpenses = 0;

          // process carts
          if (Array.isArray(carts)){
            carts.forEach(function(cart){
              const dnorm = normalizeDate(cart.cart_date || cart.date || cart.cartDate || '');
              const sale = cartSalesValue(cart);
              const paid = cartPaidValue(cart);       // income uses paid amount
              const profit = cartProfitValue(cart);

              storeProfitTotal += profit;

              // 2025 sales
              if (dnorm && dnorm.startsWith('2025')) storeSales2025 += sale;
              else if (!dnorm && String(cart.cart_date || '').includes('2025')) storeSales2025 += sale;

              // today
              if (dnorm === todayYMD) {
                storeTodaySales += sale;
                storeTodayIncome += paid;
              }
              // yesterday
              if (dnorm === yesterdayYMD) {
                storeYesterdaySales += sale;
                storeYesterdayIncome += paid;
              }
            });
          }

          // process expenses
          if (Array.isArray(expenseDetails)){
            expenseDetails.forEach(function(e){
              const amt = expenseValue(e);
              const dnorm = normalizeDate(e.date || e.created_at || e.createdAt || '');
              storeExpensesTotal += amt;
              if (dnorm === yesterdayYMD) storeYesterdayExpenses += amt;
            });
          }

          // accumulate globals
          aggSales2025 += storeSales2025;
          aggTodaySales += storeTodaySales;
          aggTodayIncome += storeTodayIncome;
          aggYesterdaySales += storeYesterdaySales;
          aggYesterdayIncome += storeYesterdayIncome;
          aggTotalExpenses += storeExpensesTotal;
          aggYesterdayExpenses += storeYesterdayExpenses;
          aggTotalProfit += storeProfitTotal;

          // update per-store UI
          $row.find('[data-store-sales]').text(storeSales2025 ? formatTaka(storeSales2025) : '-');
          $row.find('[data-store-profit]').text(storeProfitTotal ? formatTaka(storeProfitTotal) : '-');

          log(`Store ${st.name} OK — products:${pCount}, suppliers:${sCount}, sales2025:${formatTaka(storeSales2025)}, today:${formatTaka(storeTodaySales)}, profit:${formatTaka(storeProfitTotal)}`);

        }).fail(function(xhr, statusText){
          log(`Store ${st.name} (${storeId}) fetch error: ${xhr.status || '??'} ${xhr.statusText || statusText}`);
          console.error('fetch-data error', xhr && xhr.responseText);
        }).always(function(){
          completed++;
          // when all stores processed update dashboard widgets
          if (completed === stores.length){
            $('#total-products').text(aggProducts || '-');
            $('#total-sales-year').text(aggSales2025 ? formatTaka(aggSales2025) : '-');
            $('#today-sales').text(aggTodaySales ? formatTaka(aggTodaySales) : '-');
            $('#today-income').text(aggTodayIncome ? formatTaka(aggTodayIncome) : '-');
            $('#total-expenses').text(aggTotalExpenses ? formatTaka(aggTotalExpenses) : '-');

            $('#yesterday-sales').text(aggYesterdaySales ? formatTaka(aggYesterdaySales) : '-');
            $('#yesterday-income').text(aggYesterdayIncome ? formatTaka(aggYesterdayIncome) : '-');
            $('#yesterday-expenses').text(aggYesterdayExpenses ? formatTaka(aggYesterdayExpenses) : '-');

            // aggregated summary card
            const summaryHtml = `
              <div id="store-aggregates-summary" class="card theme-shadow p-3 mt-3">
                <h6 class="mb-2">Aggregated Remote Data</h6>
                <div class="d-flex gap-3 align-items-center flex-wrap">
                  <div><strong>Total Stores:</strong> ${stores.length}</div>
                  <div><strong>Total Products:</strong> ${aggProducts}</div>
                  <div><strong>Total Suppliers:</strong> ${aggSuppliers}</div>
                  <div><strong>Sales (2025):</strong> ${formatTaka(aggSales2025)}</div>
                  <div><strong>Today Sales:</strong> ${formatTaka(aggTodaySales)}</div>
                  <div><strong>Today Income:</strong> ${formatTaka(aggTodayIncome)}</div>
                  <div><strong>Yesterday Sales:</strong> ${formatTaka(aggYesterdaySales)}</div>
                  <div><strong>Yesterday Income:</strong> ${formatTaka(aggYesterdayIncome)}</div>
                  <div><strong>Yesterday Expenses:</strong> ${formatTaka(aggYesterdayExpenses)}</div>
                  <div><strong>Total Expenses:</strong> ${formatTaka(aggTotalExpenses)}</div>
                  <div><strong>Total Profit:</strong> ${formatTaka(aggTotalProfit)}</div>
                </div>
              </div>`;
            $summaryContainer.html(summaryHtml);
            log('All stores processed.');
          }
        });

      }); // end stores.forEach
    })
    .fail(function(){
      log('Failed to load stores from mother app (network).');
    });

  // click handler for per-store "View" button (example: open remote store backoffice)
  $(document).on('click', '.btn-view', function(){
    const storeId = $(this).data('store-id');
    // implement as you like, e.g. open store base_url or show detail modal
    alert('View details for store ' + storeId);
  });

});
</script>

@endsection

