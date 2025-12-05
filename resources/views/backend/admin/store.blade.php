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

  const TABLES = 'products,suppliers,cart_informtion,expense_details,banner_information';
  const $summaryContainer = $('#store-aggregates').empty();

  function log(msg){ $summaryContainer.append($('<div class="small text-muted"></div>').text(msg)); console.log('[dashboard] ' + msg); }
  function toNumber(v){ const n = Number(v); return Number.isFinite(n) ? n : 0; }
  function formatTaka(val){ const n = toNumber(val); if (n === 0) return '-'; return '৳ ' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
  function normalizeDate(dstr){ if (!dstr) return null; const s = String(dstr).trim(); const iso = s.match(/\d{4}-\d{2}-\d{2}/); if (iso) return iso[0]; const dmy = s.match(/(\d{1,2})[^\d](\d{1,2})[^\d](\d{4})/); if (dmy) { const dd = String(dmy[1]).padStart(2,'0'); const mm = String(dmy[2]).padStart(2,'0'); const yy = dmy[3]; return `${yy}-${mm}-${dd}`;} const y = s.match(/(19|20)\d{2}/); return y ? y[0] : null; }

  function extractArray(payload, tableName){
    if (!payload) return [];
    if (payload.results && payload.results[tableName] && Array.isArray(payload.results[tableName].data)) return payload.results[tableName].data;
    if (payload[tableName] && Array.isArray(payload[tableName].data)) return payload[tableName].data;
    if (payload.data && payload.data[tableName] && Array.isArray(payload.data[tableName].data)) return payload.data[tableName].data;
    if (payload[tableName] && Array.isArray(payload[tableName])) return payload[tableName];
    if (Array.isArray(payload)) return payload;
    return [];
  }

  // Try GET then fallback to POST when calling store proxy summary
  function callStoreSummaryProxy(storeId, bodyOrQuery) {
    const urlBase = '/stores/' + storeId + '/fetch-summary';
    // Try GET (query params)
    return new Promise(function(resolve) {
      // Build query if params provided (bodyOrQuery is object)
      const q = typeof bodyOrQuery === 'object' ? ('?'+ $.param(bodyOrQuery)) : '';
      $.ajax({
        url: urlBase + q,
        method: 'GET',
        dataType: 'json',
        timeout: 15000,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      }).done(function(res){
        resolve({ ok:true, method:'GET', res:res });
      }).fail(function(xhr){
        // if method not allowed or other server error, fall back to POST
        log(`GET summary failed for store ${storeId} (${xhr.status}) — trying POST fallback`);
        $.ajax({
          url: urlBase,
          method: 'POST',
          contentType: 'application/json',
          data: JSON.stringify(bodyOrQuery || { tables: TABLES }),
          dataType: 'json',
          timeout: 20000,
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        }).done(function(res2){
          resolve({ ok:true, method:'POST', res:res2 });
        }).fail(function(xhr2){
          resolve({ ok:false, status: xhr2.status || xhr.status, error: xhr2.responseText || xhr.statusText });
        });
      });
    });
  }

  // load stores and fetch summaries
  $.get("{{ route('stores.index') }}")
    .done(function(res){
      if (!res || !res.ok){ log('Failed to load stores'); return; }
      const stores = res.data || [];
      $('#total-stores').text(stores.length);
      log('Found ' + stores.length + ' stores. Fetching summaries...');

      // aggregates
      let aggProducts = 0, aggSuppliers = 0, aggSales2025 = 0;
      let aggTodaySales = 0, aggTodayIncome = 0, aggYesterdaySales = 0, aggYesterdayIncome = 0;
      let aggTotalExpenses = 0, aggYesterdayExpenses = 0, aggTotalProfit = 0;

      const today = new Date();
      const todayYMD = today.getFullYear() + '-' + String(today.getMonth()+1).padStart(2,'0') + '-' + String(today.getDate()).padStart(2,'0');
      const yesterday = new Date(today); yesterday.setDate(today.getDate() - 1);
      const yesterdayYMD = yesterday.getFullYear() + '-' + String(yesterday.getMonth()+1).padStart(2,'0') + '-' + String(yesterday.getDate()).padStart(2,'0');

      const $tbody = $('#stores-info-tbody').empty();
      let done = 0;

      stores.forEach(function(st, idx){
        const storeId = st.id;
        const $row = $(`<tr data-id="${storeId}">
            <td class="text-center">${String(idx+1).padStart(2,'0')}</td>
            <td class="text-center">${$('<div>').text(st.name).html()}</td>
            <td class="text-center" data-store-sales>-</td>
            <td class="text-center" data-store-profit>-</td>
            <td class="text-center"><button class="btn btn-success px-2 py-1 btn-view" data-store-id="${storeId}"><i class="ri-eye-fill"></i> View</button></td>
          </tr>`);
        $tbody.append($row);

        // call proxy: try GET then POST
        const query = { tables: TABLES };
        callStoreSummaryProxy(storeId, query).then(function(result){
          if (!result.ok) {
            log(`Store ${st.name} (${storeId}) summary fetch failed: ${result.status || 'unknown'}`);
            done++; if (done === stores.length) finalize(); return;
          }

          const r = result.res;
          // proxy wrapper often returns { ok:true, data: {...} } or { ok:true, results: {...} }
          const payload = r && r.data ? (r.data.results || r.data) : (r.results || r);
          // banner name
          let bannerName = st.banner_name || st.name || ('Store ' + storeId);
          try {
            const b = payload.banner_information;
            if (b) {
              if (b.banner && b.banner.banner_name) bannerName = b.banner.banner_name;
              else if (Array.isArray(b.data) && b.data[0] && b.data[0].banner_name) bannerName = b.data[0].banner_name;
              else if (b.banner_name) bannerName = b.banner_name;
            }
          } catch(e){}

          // cart summary fields returned by controller
          const cart = payload.cart_informtion || payload.cart_information || payload.cart || {};
          const totalAmountYear = toNumber(cart.total_amount_year || cart.total_amount || 0);
          const totalAmountToday = toNumber(cart.total_amount_today || 0);
          const totalAmountYesterday = toNumber(cart.total_amount_yesterday || 0);
          const totalProfit = toNumber(cart.total_profit || 0);
          const totalCount = Number(cart.total_count || 0);

          // expenses
          const exp = payload.expense_details || payload.expenses || {};
          const totalExpenses = toNumber(exp.total_amount || 0);

          // products & suppliers counts
          const productsCount = payload.products ? Number(payload.products.total_count || 0) : 0;
          const suppliersCount = payload.suppliers ? Number(payload.suppliers.total_count || 0) : 0;

          // accumulate
          aggProducts += productsCount;
          aggSuppliers += suppliersCount;
          aggSales2025 += totalAmountYear;
          aggTodaySales += totalAmountToday;
          aggTodayIncome += totalAmountToday; // paid_amount == income
          aggYesterdaySales += totalAmountYesterday;
          aggYesterdayIncome += totalAmountYesterday;
          aggTotalExpenses += totalExpenses;
          aggTotalProfit += totalProfit;

          // update row
          $row.find('td').eq(1).text(bannerName);
          $row.find('[data-store-sales]').text(totalAmountYear ? formatTaka(totalAmountYear) : '-');
          $row.find('[data-store-profit]').text(totalProfit ? formatTaka(totalProfit) : '-');

          log(`Store ${bannerName} OK — products:${productsCount}, suppliers:${suppliersCount}, sales2025:${formatTaka(totalAmountYear)}`);

          done++;
          if (done === stores.length) finalize();
        });
      });

      function finalize(){
        $('#total-products').text(aggProducts || '-');
        $('#total-sales-year').text(aggSales2025 ? formatTaka(aggSales2025) : '-');
        $('#today-sales').text(aggTodaySales ? formatTaka(aggTodaySales) : '-');
        $('#today-income').text(aggTodayIncome ? formatTaka(aggTodayIncome) : '-');
        $('#total-expenses').text(aggTotalExpenses ? formatTaka(aggTotalExpenses) : '-');

        $('#yesterday-sales').text(aggYesterdaySales ? formatTaka(aggYesterdaySales) : '-');
        $('#yesterday-income').text(aggYesterdayIncome ? formatTaka(aggYesterdayIncome) : '-');
        $('#yesterday-expenses').text(aggYesterdayExpenses ? formatTaka(aggYesterdayExpenses) : '-');
      }
    })
    .fail(function(){
      log('Failed to load stores from mother app (network).');
    });

  // view button handler
  $(document).on('click', '.btn-view', function(){ alert('View details for store ' + $(this).data('store-id')); });

});
</script>


@endsection

