@extends('layouts.master')

@section('title', 'Dashboard || Step Shoe Pos')
@section('body-class', '')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <style>
        .filter-bar .form-control, .filter-bar .form-select { height: 38px; }
        table.dataTable td, table.dataTable th { vertical-align: middle; }
        .status-badge { text-transform: capitalize; }
        .disc-modal-card { border: 1px solid rgba(0,0,0,.06); }
    </style>
@endpush

@section('content')

<div class="p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0">Discount Request</h5>
        <button class="btn btn-outline-secondary btn-sm" id="btnRefresh">
            <i class="ri-refresh-line"></i> Refresh
        </button>
    </div>

    <!-- Filters -->
    <div class="card theme-shadow mb-3 filter-bar" data-aos="fade-up" data-aos-duration="800">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label mb-1">Status</label>
                    <select class="form-select" id="f_status">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label mb-1">Store / Shop</label>
                    <input class="form-control" id="f_store" placeholder="Store name / login id">
                </div>

                <div class="col-md-3">
                    <label class="form-label mb-1">Customer (mobile)</label>
                    <input class="form-control" id="f_customer" placeholder="01XXXXXXXXX">
                </div>

                <div class="col-md-2">
                    <label class="form-label mb-1">Date From</label>
                    <input type="date" class="form-control" id="f_date_from">
                </div>

                <div class="col-md-2">
                    <label class="form-label mb-1">Date To</label>
                    <input type="date" class="form-control" id="f_date_to">
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-primary btn-sm" id="btnApplyFilters">
                        <i class="ri-search-line"></i> Apply
                    </button>
                    <button class="btn btn-light btn-sm" id="btnResetFilters">
                        <i class="ri-restart-line"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card theme-shadow" data-aos="fade-up" data-aos-duration="1200">
        <div class="table-responsive p-2">
            <table id="discountRequestsTable" class="table table-striped table-hover align-middle w-100">
                <thead>
                    <tr class="text-center">
                        <th class="bg-dark text-white inria-serif">SR</th>
                        <th class="bg-dark text-white inria-serif">Date & Time (BD)</th>
                        <th class="bg-dark text-white inria-serif">Store Name</th>
                        <th class="bg-dark text-white inria-serif">Customer</th>
                        <th class="bg-dark text-white inria-serif">Total Items</th>
                        <th class="bg-dark text-white inria-serif">Total Amount</th>
                        <th class="bg-dark text-white inria-serif">Requested Discount</th>
                        <th class="bg-dark text-white inria-serif">Status</th>
                        <th class="bg-dark text-white inria-serif">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- modal for discount details start -->
<div class="modal fade" id="viewDiscountDetails" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="viewDiscountDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold" id="viewDiscountDetailsLabel">
                    <i class="ri-price-tag-3-line"></i> Discount Details
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row m-0">
                    <div class="col-md-12">
                        <h6 class="fw-semibold mb-2 request-from">Request From: -</h6>
                        <small class="text-muted d-block mb-3 requested-at">Requested At: -</small>
                    </div>

                    <div class="col-md-6">
                        <table class="mb-3">
                            <tr>
                                <td>Customer Name</td><td>: &nbsp; &nbsp; </td>
                                <td class="fw-semibold customer-name">-</td>
                            </tr>
                            <tr>
                                <td>Requested Amount</td><td>: &nbsp; &nbsp; </td>
                                <td class="fw-semibold py-2 requested-amount">-</td>
                            </tr>
                            <tr>
                                <td>Requested By</td><td>: &nbsp; &nbsp; </td>
                                <td class="fw-semibold requested-by">-</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table class="mb-3 w-100">
                            <tr>
                                <td>Current Price</td><td> : &nbsp; &nbsp; </td>
                                <td class="fw-semibold text-end current-price">-</td>
                            </tr>
                            <tr>
                                <td>Discount Price</td><td> : &nbsp; &nbsp; </td>
                                <td class="fw-semibold">
                                    <input disabled class="form-control text-end py-1 discount-input" type="text" value="0">
                                </td>
                            </tr>
                            <tr>
                                <td>New Price</td><td> : &nbsp; &nbsp; </td>
                                <td class="fw-semibold text-end new-price">-</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-semibold">Product List</h6>
                                <p class="text-muted mb-0">
                                    <span class="me-3 total-products">Total Product 0</span>
                                    <span class="total-items">Total Items 0</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 product-list-container row">
                        <div class="col-12 text-muted no-products">No product details available.</div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>

                <button type="button" class="btn btn-danger modal-delete">
                    <i class="ri-delete-bin-line"></i> Delete
                </button>

                <button type="button" class="btn btn-danger modal-reject">Reject</button>
                <button type="button" class="btn btn-success modal-approve">Approve</button>
            </div>
        </div>
    </div>
</div>
<!-- modal end -->

<!-- jQuery + DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function(){

  const API_BASE = '/api/discount-requests';
  const DT_URL   = API_BASE;

  function formatCurrency(n){
    const v = Number(n || 0);
    return '৳ ' + v.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function toastSuccess(msg){
    Swal.fire({ icon: 'success', title: msg, toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
  }
  function toastError(msg){
    Swal.fire({ icon: 'error', title: msg, toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
  }

  function randomImg(){
    const imgs = ["/assets/img/product-1.jpg", "/assets/img/product-2.webp"];
    return imgs[Math.floor(Math.random() * imgs.length)];
  }

  function debounce(fn, wait){
    let t=null;
    return function(){
      clearTimeout(t);
      t=setTimeout(()=>fn.apply(this, arguments), wait);
    };
  }

  // -----------------------------
  // DataTable init (server-side)
  // -----------------------------
  const table = $('#discountRequestsTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 25,
    lengthMenu: [10,25,50,100],
    order: [[1,'desc']], // Date & Time column
    ajax: {
      url: DT_URL,
      data: function(d){
        d.status    = $('#f_status').val();
        d.store     = $('#f_store').val();
        d.customer  = $('#f_customer').val();
        d.date_from = $('#f_date_from').val();
        d.date_to   = $('#f_date_to').val();
      }
    },
    columns: [
      { data: 'sr', orderable:false, searchable:false, className:'text-center' },
      { data: 'created_at', className:'text-center' },
      { data: 'store_name', className:'text-center' },
      { data: 'customer_mobile', className:'text-center' },
      { data: 'total_items', orderable:false, searchable:false, className:'text-center' },
      { data: 'total_payable_fmt', orderable:false, searchable:false, className:'text-center' },
      { data: 'discount_requested_fmt', orderable:false, searchable:false, className:'text-center' },
      { data: 'status_badge', orderable:false, searchable:false, className:'text-center' },
      { data: 'action', orderable:false, searchable:false, className:'text-center' },
    ]
  });

  // Auto refresh without losing page
  setInterval(function(){
    table.ajax.reload(null, false);
  }, 10000);

  $('#btnRefresh').on('click', function(){
    table.ajax.reload(null, false);
  });

  $('#btnApplyFilters').on('click', function(){
    table.ajax.reload();
  });

  $('#btnResetFilters').on('click', function(){
    $('#f_status').val('');
    $('#f_store').val('');
    $('#f_customer').val('');
    $('#f_date_from').val('');
    $('#f_date_to').val('');
    table.ajax.reload();
  });

  $('#f_store,#f_customer').on('keyup', debounce(function(){
    table.ajax.reload();
  }, 350));

  $('#f_status,#f_date_from,#f_date_to').on('change', function(){
    table.ajax.reload();
  });

  // -----------------------------
  // Render product list in modal
  // -----------------------------
  function renderProductList(selector, items){
    const $c = $(selector).empty();

    if (!Array.isArray(items) || items.length === 0){
      $c.append('<div class="col-12 text-muted no-products">No product details available.</div>');
      return;
    }

    items.forEach(function(it){
      const img = it.image ?? it.product_image ?? it.product_images?.[0] ?? randomImg();
      const name = it.product_name ?? it.product_material_name ?? 'Product';
      const qty = it.quantity ?? it.qty ?? 0;
      const price = it.unit_price ?? it.sales_price ?? it.price ?? 0;
      const discount = it.total_discount ?? it.discount ?? 0;
      const sku = it.barcode ?? it.article ?? '-';
      const variation = `${it.color_name ?? it.colors_name ?? ''} ${it.size_name ?? ''}`.trim();

      const card = `
        <div class="col-md-6 mb-3">
          <div class="card theme-shadow disc-modal-card">
            <div class="card-body d-flex gap-3 align-items-center">
              <div class="d-flex gap-2 align-items-center">
                <img src="${img}" alt="Product Image" style="width:72px;height:72px;object-fit:cover;border-radius:6px">
                <div class="product-info">
                  <h6 class="fw-semibold mb-0">${name}</h6>
                  <small class="text-muted">Article: ${sku}</small>
                  ${variation ? `<br><small class="text-muted">${variation}</small>` : ``}
                </div>
              </div>
              <div style="margin-left:auto;text-align:right;">
                <p class="mb-0">
                  <span class="d-block"><b class="fs-6">${formatCurrency(price)}</b>/pc</span>
                  <span class="d-block"><strong>QTY: ${qty}</strong></span>
                  <span class="d-block text-muted">Discount: ${formatCurrency(discount)}</span>
                </p>
              </div>
            </div>
          </div>
        </div>
      `;
      $c.append(card);
    });
  }

  // -----------------------------
  // Open modal with details
  // -----------------------------
  $(document).on("click",".view-btn", function(){
    const id = $(this).data("id");

    $.get(API_BASE + "/" + id).done(function(res){
      if (!res.ok) { toastError("Failed to load details"); return; }

      const r = res.data;

      let items = r.items_json ?? r.items ?? [];
      if (typeof items === "string") {
        try { items = JSON.parse(items); } catch(e){ items = []; }
      }
      if (!Array.isArray(items)) items = [];

      $(".request-from").text("Request From: " + (r.store_name ?? "-"));
      $(".customer-name").text(r.customer_mobile ?? "-");
      $(".requested-amount").text(formatCurrency(r.discount_requested));
      $(".requested-by").text(r.salesman ?? "-");

      // Requested time already in BD from backend if you format it there,
      // but if it's ISO, you can force BD timezone like this:
      if (r.created_at) {
        try {
          const dt = new Date(r.created_at);
          const pretty = dt.toLocaleString('en-GB', {
            timeZone: 'Asia/Dhaka',
            year:'numeric', month:'short', day:'2-digit',
            hour:'2-digit', minute:'2-digit'
          });
          $(".requested-at").text("Requested At: " + pretty + " (BD)");
        } catch(e){
          $(".requested-at").text("Requested At: " + r.created_at);
        }
      } else {
        $(".requested-at").text("Requested At: -");
      }

      const curr = Number(r.total_payable || 0);
      $(".current-price").text(formatCurrency(curr));
      $(".discount-input").val(Number(r.discount_requested || 0).toFixed(2));
      $(".new-price").text(formatCurrency(curr - Number(r.discount_requested || 0)));

      renderProductList(".product-list-container", items);

      const totalProducts = items.length;
      const totalItems = items.reduce((s, it) => s + Number(it.quantity ?? it.qty ?? 0), 0);
      $(".total-products").text("Total Product " + totalProducts);
      $(".total-items").text("Total Items " + totalItems);

      $("#viewDiscountDetails").data("request-id", r.id).modal("show");

      if (r.status !== "pending") {
        $(".modal-approve, .modal-reject").prop("disabled", true);
      } else {
        $(".modal-approve, .modal-reject").prop("disabled", false);
      }

    }).fail(function(){
      toastError("Error loading details");
    });
  });

  // -----------------------------
  // Approve / Reject
  // -----------------------------
  function performDecision(id, action){
    Swal.fire({
      title: action === "approve" ? "Approve discount?" : "Reject discount?",
      text: action === "approve" ? "This will notify POS and apply discount." : "This will reject and notify POS.",
      icon: action === "approve" ? "question" : "warning",
      showCancelButton: true,
      confirmButtonText: action === "approve" ? "Approve" : "Reject"
    }).then((result)=>{
      if (!result.isConfirmed) return;

      $.ajax({
        url: `${API_BASE}/${id}/${action}`,
        method: "PATCH",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
      }).done(function(){
        toastSuccess(action === "approve" ? "Approved" : "Rejected");
        $("#viewDiscountDetails").modal("hide");
        table.ajax.reload(null, false);
      }).fail(function(){
        toastError("Action failed");
      });
    });
  }

  $(document).on("click",".approve-btn", function(){
    performDecision($(this).data("id"), "approve");
  });
  $(document).on("click",".reject-btn", function(){
    performDecision($(this).data("id"), "reject");
  });
  $(document).on("click",".modal-approve", function(){
    performDecision($("#viewDiscountDetails").data("request-id"), "approve");
  });
  $(document).on("click",".modal-reject", function(){
    performDecision($("#viewDiscountDetails").data("request-id"), "reject");
  });

  // -----------------------------
  // Delete request
  // -----------------------------
  function performDelete(id){
    Swal.fire({
      title: "Delete this request?",
      text: "This will remove the request from the system.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Delete",
      confirmButtonColor: "#d33"
    }).then((result)=>{
      if (!result.isConfirmed) return;

      $.ajax({
        url: `${API_BASE}/${id}/delete`,
        method: "DELETE",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
      }).done(function(res){
        toastSuccess("Deleted");
        $("#viewDiscountDetails").modal("hide");
        table.ajax.reload(null, false);
      }).fail(function(xhr){
        console.error(xhr.responseText);
        toastError("Delete failed");
      });
    });
  }

  $(document).on("click",".delete-btn", function(){
    performDelete($(this).data("id"));
  });

  $(document).on("click",".modal-delete", function(){
    performDelete($("#viewDiscountDetails").data("request-id"));
  });

});
</script>

@endsection
