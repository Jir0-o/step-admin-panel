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

{{-- Add this modal HTML at the bottom of your blade --}}
<div class="modal fade" id="storeDetailsModal" tabindex="-1" aria-labelledby="storeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="storeDetailsModalLabel">Store Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="storeDetailsContent">
                <!-- Content will be loaded here -->
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


<div id="fetchProgress" style="margin-top:10px; display:none;">
  <div id="progressText" class="small text-muted">Starting...</div>
  <div style="width:100%; background:#f0f0f0; height:8px; border-radius:4px;">
    <div id="progressBar" style="width:0%; height:8px; background:#0d6efd; border-radius:4px;"></div>
  </div>
</div>

<!-- jQuery required by your code -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
window.ROUTES = {
    storeSummary: "{{ route('stores.fetch-summary', ['store' => '__ID__']) }}",
    storeStockTable: "{{ route('manager.stock-data.index', ['store' => '__ID__']) }}"
};
</script>

<script>

function downloadStoreDetails() {
    const storeName = $('#storeDetailsModalLabel').text().replace(' - Store Details', '');
    const content = $('#storeDetailsContent').html(); // Get HTML content for better formatting
    
    // Create a text version with all the data
    let textContent = `Store Details Report\n`;
    textContent += `====================\n`;
    textContent += `Store: ${storeName}\n`;
    textContent += `Report Date: ${new Date().toLocaleString()}\n\n`;
    
    // Extract key data from the modal
    $('.store-details-content .card').each(function() {
        const cardTitle = $(this).find('.card-header h6').text().trim() || 
                         $(this).find('.card-title').text().trim() ||
                         $(this).find('h5').text().trim();
        
        if (cardTitle) {
            textContent += `\n${cardTitle}:\n`;
            textContent += `${'-'.repeat(cardTitle.length + 1)}\n`;
        }
        
        // Extract amounts and numbers
        $(this).find('h4, .display-1, h5').each(function() {
            const text = $(this).text().trim();
            if (text) {
                textContent += `  ${text}\n`;
            }
        });
        
        $(this).find('p, small').each(function() {
            const text = $(this).text().trim();
            if (text && !text.includes('undefined')) {
                textContent += `  ${text}\n`;
            }
        });
    });
    
    // Create and download the file
    const blob = new Blob([textContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${storeName.replace(/\s+/g, '_')}_Details_${new Date().toISOString().split('T')[0]}.txt`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}

function printStoreDetails() {
    // Store the original modal content
    const originalContent = $('.store-details-content').html();
    
    // Create a print-friendly version
    const storeName = $('#storeDetailsModalLabel').text().replace(' - Store Details', '');
    const printDate = new Date().toLocaleString();
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>${storeName} - Store Details Report</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    color: #333;
                }
                .report-header {
                    text-align: center;
                    border-bottom: 2px solid #333;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                .report-header h1 {
                    margin: 0;
                    color: #2c3e50;
                }
                .report-header .subtitle {
                    color: #7f8c8d;
                    margin: 5px 0 15px 0;
                }
                .section {
                    margin-bottom: 30px;
                    page-break-inside: avoid;
                }
                .section-title {
                    background-color: #f8f9fa;
                    padding: 10px;
                    border-left: 4px solid #3498db;
                    margin-bottom: 15px;
                    font-weight: bold;
                }
                .metric-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 15px;
                    margin-bottom: 20px;
                }
                .metric-card {
                    border: 1px solid #ddd;
                    padding: 15px;
                    border-radius: 5px;
                    text-align: center;
                }
                .metric-value {
                    font-size: 24px;
                    font-weight: bold;
                    margin: 10px 0;
                }
                .metric-label {
                    color: #7f8c8d;
                    font-size: 14px;
                }
                .card-primary { border-left: 4px solid #3498db; }
                .card-success { border-left: 4px solid #2ecc71; }
                .card-warning { border-left: 4px solid #f39c12; }
                .card-danger { border-left: 4px solid #e74c3c; }
                .card-info { border-left: 4px solid #1abc9c; }
                @media print {
                    .no-print { display: none !important; }
                    body { margin: 0; }
                    .section { page-break-inside: avoid; }
                }
            </style>
        </head>
        <body>
            <div class="report-header">
                <h1>${storeName}</h1>
                <div class="subtitle">Store Details Report</div>
                <div>Report Generated: ${printDate}</div>
                <div class="no-print" style="margin-top: 20px; color: #95a5a6; font-size: 12px;">
                    This report was generated from Step Shoe POS System
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Sales Summary</div>
                <div class="metric-grid">
                    <div class="metric-card card-primary">
                        <div class="metric-label">Total Sales</div>
                        <div class="metric-value">${$('.store-details-content .card.bg-primary h4').text()}</div>
                        <div>${$('.store-details-content .card.bg-primary .opacity-75').text()}</div>
                    </div>
                    <div class="metric-card card-info">
                        <div class="metric-label">2025 Sales</div>
                        <div class="metric-value">${$('.store-details-content .card.bg-info h4').text()}</div>
                        <div>Year ${new Date().getFullYear()}</div>
                    </div>
                    <div class="metric-card card-success">
                        <div class="metric-label">Today's Sales</div>
                        <div class="metric-value">${$('.store-details-content .card.bg-success h4').text()}</div>
                        <div>${new Date().toLocaleDateString()}</div>
                    </div>
                    <div class="metric-card card-warning">
                        <div class="metric-label">Total Profit</div>
                        <div class="metric-value">${$('.store-details-content .card.bg-warning h4').text()}</div>
                        <div>Margin: ${$('.store-details-content .card.bg-warning .small').text().replace('Margin: ', '')}</div>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Store Inventory</div>
                <div class="metric-grid">
                    <div class="metric-card card-primary">
                        <div class="metric-label">Products</div>
                        <div class="metric-value">${$('.store-details-content .card-header.bg-primary + .card-body .display-1').text()}</div>
                        <div>Total Products</div>
                    </div>
                    <div class="metric-card card-info">
                        <div class="metric-label">Suppliers</div>
                        <div class="metric-value">${$('.store-details-content .card-header.bg-info + .card-body .display-1').text()}</div>
                        <div>Registered Suppliers</div>
                    </div>
                    <div class="metric-card card-warning">
                        <div class="metric-label">Banners</div>
                        <div class="metric-value">${$('.store-details-content .card-header.bg-warning + .card-body .display-1').text()}</div>
                        <div>Active Banners</div>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Expenses</div>
                <div class="metric-grid">
                    <div class="metric-card card-danger">
                        <div class="metric-label">Total Expenses</div>
                        <div class="metric-value">${$('.store-details-content .card-header.bg-danger + .card-body .display-1').text()}</div>
                        <div>${$('.store-details-content .card-header.bg-danger + .card-body small').text()}</div>
                    </div>
                    <div class="metric-card card-info">
                        <div class="metric-label">Expense Ratio</div>
                        <div class="metric-value">${$('.store-details-content .card-header.bg-secondary + .card-body .display-1').text()}</div>
                        <div>Expenses to Sales Ratio</div>
                    </div>
                </div>
            </div>
            
            <div class="section no-print">
                <div style="text-align: center; margin-top: 50px; color: #95a5a6; font-size: 12px;">
                    --- End of Report ---
                </div>
            </div>
            
            <script>
                window.addEventListener('load', function() {
                    window.print();
                    setTimeout(function() {
                        window.close();
                    }, 100);
                });
            <\/script>
        </body>
        </html>
    `;
    
    // Open a new window for printing
    const printWindow = window.open('', '_blank');
    printWindow.document.write(printContent);
    printWindow.document.close();
}

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

    // Add event listeners for export and print buttons
    $(document).on('click', '#exportStoreDetailsBtn', downloadStoreDetails);
    $(document).on('click', '#printStoreDetailsBtn', printStoreDetails);

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
                            <button class="btn btn-success px-2 py-1 btn-view" 
                                    data-store-id="${store.id}">
                                <i class="ri-eye-fill"></i> View Details
                            </button>
                            <a href="${window.ROUTES.storeStockTable.replace('__ID__', store.id)}" 
                            class="btn btn-info px-2 py-1">
                                <i class="ri-table-line"></i> Stock Data
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

           // Handle View Details button click
    $(document).on('click', '.btn-view', function(e) {
        e.preventDefault();
        const storeId = $(this).data('store-id');
        const storeName = $(this).closest('tr').find('td:nth-child(2)').text().trim();
        
        $('#storeDetailsModalLabel').text(storeName + ' - Store Details');
        $('#storeDetailsModal').modal('show');
        
        loadStoreDetails(storeId);
    });

    // Function to load store details
    function loadStoreDetails(storeId) {
        const url = window.ROUTES.storeSummary.replace('__ID__', storeId);
        
        $('#storeDetailsContent').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading store data...</p>
            </div>
        `);
        
        // Try GET first
        $.ajax({
            url: url,
            method: 'GET',
            data: { tables: 'products,suppliers,cart_informtion,expense_details,banner_information' },
            dataType: 'json',
            timeout: 15000,
            headers: { 'Accept': 'application/json' }
        })
        .done(function(r) {
            renderStoreDetails(r);
        })
        .fail(function() {
            // Try POST if GET fails
            $.ajax({
                url: url,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ 
                    tables: 'products,suppliers,cart_informtion,expense_details,banner_information' 
                }),
                dataType: 'json',
                timeout: 20000,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            .done(function(r) {
                renderStoreDetails(r);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                $('#storeDetailsContent').html(`
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line me-2"></i>
                        Failed to load store data: ${textStatus}
                    </div>
                `);
            });
        });
    }

    function renderStoreDetails(response) {
        console.log('Full API response:', response);
        
        // Extract data from the response structure
        const storeName = response.store_name || 'Store';
        const storeId = response.store_id || '';
        const data = response.data?.results || {};
        
        const cartInfo = data.cart_informtion || {};
        const expenseDetails = data.expense_details || {};
        const products = data.products || {};
        const suppliers = data.suppliers || {};
        const bannerInfo = data.banner_information || {};
        
        function formatCurrency(amount) {
            if (!amount && amount !== 0) return '৳ 0.00';
            const num = parseFloat(amount);
            if (isNaN(num)) return '৳ 0.00';
            return '৳ ' + num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
        
        function formatNumber(num) {
            const n = parseInt(num);
            return isNaN(n) ? 0 : n.toLocaleString();
        }
        
        // Calculate additional metrics in JavaScript
        const today = new Date().toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const yesterdayFormatted = yesterday.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        // Calculate metrics
        const avgTransaction = cartInfo.total_count > 0 
            ? cartInfo.total_amount / cartInfo.total_count 
            : 0;
        
        const profitMargin = cartInfo.total_amount > 0 
            ? (cartInfo.total_profit / cartInfo.total_amount) * 100 
            : 0;
        
        // Calculate days in current year
        const now = new Date();
        const start = new Date(now.getFullYear(), 0, 0);
        const diff = now - start;
        const oneDay = 1000 * 60 * 60 * 24;
        const dayOfYear = Math.floor(diff / oneDay);
        const daysInYear = dayOfYear;
        const dailyAvg = daysInYear > 0 ? cartInfo.total_amount_year / daysInYear : 0;
        
        // Calculate expense ratio
        const expenseRatio = cartInfo.total_amount > 0 
            ? (expenseDetails.total_amount / cartInfo.total_amount) * 100 
            : 0;
        
        // Calculate profit per transaction
        const profitPerTransaction = cartInfo.total_count > 0 
            ? cartInfo.total_profit / cartInfo.total_count 
            : 0;
        
        const html = `
            <div class="store-details-content">
                <!-- Store Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">${storeName}</h5>
                                        <p class="mb-0 text-muted">Store ID: ${storeId} | Last Updated: ${new Date().toLocaleTimeString()}</p>
                                    </div>
                                    <div class="badge bg-success">
                                        <i class="ri-check-line me-1"></i> Connected
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Summary -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="ri-bar-chart-line me-2"></i> Sales Summary</h5>
                        <div class="row">
                            <!-- Total Sales -->
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="card-title mb-1">Total Sales</h6>
                                                <h4 class="mb-0">${formatCurrency(cartInfo.total_amount)}</h4>
                                            </div>
                                            <div class="icon-box bg-white text-primary">
                                                <i class="ri-money-dollar-circle-line"></i>
                                            </div>
                                        </div>
                                        <p class="mb-0 small mt-2 opacity-75">
                                            <i class="ri-shopping-cart-line me-1"></i> ${formatNumber(cartInfo.total_count)} transactions
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- This Year Sales -->
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="card-title mb-1">2025 Sales</h6>
                                                <h4 class="mb-0">${formatCurrency(cartInfo.total_amount_year)}</h4>
                                            </div>
                                            <div class="icon-box bg-white text-info">
                                                <i class="ri-calendar-line"></i>
                                            </div>
                                        </div>
                                        <p class="mb-0 small mt-2 opacity-75">
                                            <i class="ri-calendar-event-line me-1"></i> Year ${new Date().getFullYear()}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Today's Sales -->
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="card-title mb-1">Today's Sales</h6>
                                                <h4 class="mb-0">${formatCurrency(cartInfo.total_amount_today)}</h4>
                                            </div>
                                            <div class="icon-box bg-white text-success">
                                                <i class="ri-sun-line"></i>
                                            </div>
                                        </div>
                                        <p class="mb-0 small mt-2 opacity-75">
                                            <i class="ri-calendar-2-line me-1"></i> ${today}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Profit -->
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card bg-warning text-dark">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="card-title mb-1">Total Profit</h6>
                                                <h4 class="mb-0">${formatCurrency(cartInfo.total_profit)}</h4>
                                            </div>
                                            <div class="icon-box bg-white text-warning">
                                                <i class="ri-profit-line"></i>
                                            </div>
                                        </div>
                                        <p class="mb-0 small mt-2">
                                            <i class="ri-percent-line me-1"></i> Margin: ${profitMargin.toFixed(1)}%
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Sales Metrics -->
                        <div class="row mt-2">
                            <!-- Yesterday's Sales -->
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-2">
                                            <i class="ri-arrow-left-line me-1"></i> Yesterday's Sales
                                        </h6>
                                        <h4 class="text-primary">${formatCurrency(cartInfo.total_amount_yesterday)}</h4>
                                        <p class="mb-0 small text-muted">${yesterdayFormatted}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Average Transaction -->
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-2">
                                            <i class="ri-calculator-line me-1"></i> Avg. Transaction
                                        </h6>
                                        <h4 class="text-info">${formatCurrency(avgTransaction)}</h4>
                                        <p class="mb-0 small text-muted">Per transaction</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profit per Transaction -->
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-2">
                                            <i class="ri-coins-line me-1"></i> Profit/Transaction
                                        </h6>
                                        <h4 class="text-success">${formatCurrency(profitPerTransaction)}</h4>
                                        <p class="mb-0 small text-muted">Average profit</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Daily Average -->
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-2">
                                            <i class="ri-line-chart-line me-1"></i> Daily Average
                                        </h6>
                                        <h4 class="text-warning">${formatCurrency(dailyAvg)}</h4>
                                        <p class="mb-0 small text-muted">This year average</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Store Inventory -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="ri-store-2-line me-2"></i> Store Inventory</h5>
                        <div class="row">
                            <!-- Products -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="ri-shopping-bag-line me-2"></i> Products</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="display-1 text-primary mb-3">${formatNumber(products.total_count)}</div>
                                        <p class="mb-0">Total Products</p>
                                        <small class="text-muted">Available</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Suppliers -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="ri-truck-line me-2"></i> Suppliers</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="display-1 text-info mb-3">${formatNumber(suppliers.total_count)}</div>
                                        <p class="mb-0">Registered Suppliers</p>
                                        <small class="text-muted">Active vendors</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Banner -->
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-warning text-white">
                                        <h6 class="mb-0"><i class="ri-image-line me-2"></i> Store Banner</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="display-1 text-warning mb-3">${formatNumber(bannerInfo.total_count)}</div>
                                        <p class="mb-0">Active Banners</p>
                                        ${bannerInfo.banner && bannerInfo.banner.banner_name ? 
                                            `<div class="alert alert-light mt-3">
                                                <i class="ri-information-line me-1"></i>
                                                <strong>Current:</strong> ${bannerInfo.banner.banner_name}
                                            </div>` : 
                                            '<p class="text-muted mb-0">No active banner</p>'
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expenses -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="ri-money-dollar-circle-line me-2"></i> Expenses</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0"><i class="ri-bank-card-line me-2"></i> Total Expenses</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="display-1 text-danger mb-3">${formatCurrency(expenseDetails.total_amount)}</div>
                                        <p class="mb-0">Total Expenses Recorded</p>
                                        <small class="text-muted">${formatNumber(expenseDetails.total_count)} expense entries</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Expense to Sales Ratio -->
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0"><i class="ri-pie-chart-line me-2"></i> Expense Ratio</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="display-1 text-secondary mb-3">${expenseRatio.toFixed(1)}%</div>
                                        <p class="mb-0">Expenses to Sales Ratio</p>
                                        <small class="text-muted">Lower is better</small>
                                        <div class="progress mt-3" style="height: 10px;">
                                            <div class="progress-bar bg-danger" style="width: ${Math.min(expenseRatio, 100)}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="ri-dashboard-line me-2"></i> Quick Statistics</h5>
                        <div class="row">
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="ri-exchange-dollar-line fs-2 text-primary mb-2"></i>
                                        <h5 class="mb-1">${formatNumber(cartInfo.total_count)}</h5>
                                        <p class="mb-0 small text-muted">Total Transactions</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="ri-coins-line fs-2 text-success mb-2"></i>
                                        <h5 class="mb-1">${formatCurrency(cartInfo.total_profit)}</h5>
                                        <p class="mb-0 small text-muted">Total Profit</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="ri-pie-chart-line fs-2 text-info mb-2"></i>
                                        <h5 class="mb-1">${profitMargin.toFixed(1)}%</h5>
                                        <p class="mb-0 small text-muted">Profit Margin</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <i class="ri-refresh-line fs-2 text-warning mb-2"></i>
                                        <h5 class="mb-1">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</h5>
                                        <p class="mb-0 small text-muted">Last Updated</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#storeDetailsContent').html(html);
    }

    function toggleRawData(button) {
        const cardBody = $(button).closest('.card-header').next('.card-body');
        const isVisible = cardBody.is(':visible');
        
        if (isVisible) {
            cardBody.slideUp();
            $(button).html('<i class="ri-arrow-down-s-line"></i> Show');
        } else {
            cardBody.slideDown();
            $(button).html('<i class="ri-arrow-up-s-line"></i> Hide');
        }
    }
    
    function printStoreDetails() {
        const printContent = $('#storeDetailsContent').html();
        const originalContent = $('body').html();
        
        $('body').html(printContent);
        window.print();
        $('body').html(originalContent);
        $('#storeDetailsModal').modal('show');
    }

});
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
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.rounded-circle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.store-details-content {
    max-height: 70vh;
    overflow-y: auto;
    padding-right: 10px;
}

.icon-box {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    transition: all 0.3s ease;
}
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.05);
    font-weight: 600;
}
.display-1 {
    font-size: 3.5rem;
    font-weight: 300;
    line-height: 1.2;
}
.store-details-content {
    max-height: 70vh;
    overflow-y: auto;
    padding-right: 10px;
}
/* Custom scrollbar */
.store-details-content::-webkit-scrollbar {
    width: 8px;
}
.store-details-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
.store-details-content::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}
.store-details-content::-webkit-scrollbar-thumb:hover {
    background: #555;
}
/* Progress bar styling */
.progress {
    border-radius: 10px;
    overflow: hidden;
}
.progress-bar {
    border-radius: 10px;
}
/* Badge styling */
.badge {
    padding: 8px 12px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 8px;
}
/* Alert styling */
.alert {
    border-radius: 10px;
    border: none;
}
/* Text styling */
.text-muted {
    opacity: 0.8;
}
.opacity-75 {
    opacity: 0.75;
}
</style>
@endsection