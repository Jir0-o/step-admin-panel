@extends('layouts.master')

@section('title', 'Dashboard || Step Shoe Pos')
@section('body-class', '')

@section('content')

<div class="p-4">
    <h5>Discount Request</h5>
    <div class="card theme-shadow" data-aos="fade-up" data-aos-duration="2000">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-center">
                        <th class="bg-dark text-white inria-serif">SR</th>
                        <th class="bg-dark text-white inria-serif">Store Name</th>
                        <th class="bg-dark text-white inria-serif">Customer</th>
                        <th class="bg-dark text-white inria-serif">Total Items</th>
                        <th class="bg-dark text-white inria-serif">Total Amount</th>
                        <th class="bg-dark text-white inria-serif">Requested Discount</th>
                        <th class="bg-dark text-white inria-serif">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center inria-serif">01</td>
                        <td class="text-center inria-serif">Store 1</td>
                        <td class="text-center inria-serif">Customer 1</td>
                        <td class="text-center inria-serif">2</td>
                        <td class="text-center inria-serif">3000 /=</td>
                        <td class="text-center inria-serif">300 /=</td>
                        <td class="text-center inria-serif">
                            <div class="d-flex gap-2 align-items-center justify-content-center">
                                <button class="btn btn-success px-2 py-1" type="button" data-bs-toggle="modal"
                                    data-bs-target="#viewDiscountDetails">
                                    <i class="ri-eye-fill"></i> View
                                </button>
                                <button class="btn btn-secondary px-2 py-1">
                                    <i class="ri-delete-bin-line"></i> Approve
                                </button>
                                <button class="btn btn-danger px-2 py-1">
                                    <i class="ri-delete-bin-line"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr> 
                        <td class="text-center inria-serif">02</td>
                        <td class="text-center inria-serif">Store 1</td>
                        <td class="text-center inria-serif">Customer 2</td>
                        <td class="text-center inria-serif">3</td>
                        <td class="text-center inria-serif">5100 /=</td>
                        <td class="text-center inria-serif">100 /=</td>
                        <td class="text-center inria-serif">
                            <div class="d-flex gap-2 align-items-center justify-content-center">
                                <button class="btn btn-success px-2 py-1" type="button" data-bs-toggle="modal"
                                    data-bs-target="#viewDiscountDetails">
                                    <i class="ri-eye-fill"></i> View
                                </button>
                                <button class="btn btn-secondary px-2 py-1">
                                    <i class="ri-delete-bin-line"></i> Approve
                                </button>
                                <button class="btn btn-danger px-2 py-1">
                                    <i class="ri-delete-bin-line"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
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
                <h1 class="modal-title fs-5 fw-semibold" id="viewDiscountDetailsLabel"><i
                        class="ri-price-tag-3-line"></i> Discount Details</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row m-0">
                    <div class="col-md-12">
                        <!-- request-from is populated by JS -->
                        <h6 class="fw-semibold mb-3 request-from">Request From: Step Store 1</h6>
                    </div>

                    <div class="col-md-6">
                        <table class="mb-3">
                            <tr>
                                <td>Customer Name</td>
                                <td>: &nbsp; &nbsp; </td>
                                <!-- customer-name populated by JS -->
                                <td class="fw-semibold customer-name">Customer 1</td>
                            </tr>
                            <tr>
                                <td>Requested Amount</td>
                                <td>: &nbsp; &nbsp; </td>
                                <!-- requested-amount populated by JS -->
                                <td class="fw-semibold py-2 requested-amount">500/=</td>
                            </tr>
                            <tr>
                                <td>Requested By</td>
                                <td>: &nbsp; &nbsp; </td>
                                <!-- requested-by populated by JS -->
                                <td class="fw-semibold requested-by">MD Hridoy Sheikh</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table class="mb-3 w-100">
                            <tr>
                                <td>Current Price</td>
                                <td> : &nbsp; &nbsp; </td>
                                <!-- current-price populated by JS -->
                                <td class="fw-semibold text-end current-price">3500/=</td>
                            </tr>
                            <tr>
                                <td>Discount Price</td>
                                <td> : &nbsp; &nbsp; </td>
                                <td class="fw-semibold">
                                    <!-- discount-input used to live-edit and update new-price -->
                                    <input disabled class="form-control text-end py-1 discount-input" type="text" value="300">
                                </td>
                            </tr>
                            <tr>
                                <td>New Price</td>
                                <td> : &nbsp; &nbsp; </td>
                                <!-- new-price populated by JS -->
                                <td class="fw-semibold text-end new-price">-</td>
                            </tr>
                        </table>
                    </div>

                    <!-- action buttons (modal footer also contains approve/reject) -->
                    <div class="col-md-6">
                        <button class="btn btn-danger w-100 mb-3 modal-reject">Cancel</button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-secondary w-100 mb-3 modal-approve">Confirm</button>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-semibold">Product List</h6>
                                <p class="text-muted">
                                    <span class="me-3 total-products">Total Product 0</span>
                                    <span class="total-items">Total Items 0</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- product list container: JS will inject product cards here -->
                    <div class="col-12 product-list-container row">
                        <!-- placeholder when no products: JS will replace -->
                        <div class="col-12 text-muted no-products">No product details available.</div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <!-- keep modal footer buttons but add classes the script expects -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger modal-reject">Reject</button>
                <button type="button" class="btn btn-success modal-approve">Approve</button>
            </div>
        </div>
    </div>
</div>
<!-- modal end -->


<!-- jQuery required by your code -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){

  const API_BASE = '/api/discount-requests';

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
    const imgs = [
      "/assets/img/product-1.jpg",
      "/assets/img/product-2.webp",
    ];
    return imgs[Math.floor(Math.random() * imgs.length)];
  }

  // ----------------------------------------------------------
  // LOAD ALL DISCOUNT REQUESTS
  // ----------------------------------------------------------
  function loadRequests(){
    $.ajax({
      url: API_BASE,
      method: 'GET',
      dataType: 'json'
    }).done(function(res){
      if (!res || !res.ok) return;

      const rows = res.data || [];
      const $tbody = $("table.table tbody").empty();

      rows.forEach(function(r, idx){

        let items = r.items_json ?? r.items ?? [];
        if (typeof items === "string") {
          try { items = JSON.parse(items); } catch(e){ items = []; }
        }

        const totalQty = items.reduce((s,i)=> s + Number(i.quantity || i.qty || 0), 0);

        const tr = `
          <tr data-id="${r.id}">
            <td class="text-center">${String(idx+1).padStart(2,"0")}</td>
            <td class="text-center">${r.store_name ?? r.store_login_id ?? "-"}</td>
            <td class="text-center">${r.customer_mobile ?? "-"}</td>
            <td class="text-center">${totalQty}</td>
            <td class="text-center">${formatCurrency(r.total_payable)}</td>
            <td class="text-center">${formatCurrency(r.discount_requested)}</td>
            <td class="text-center">
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn btn-sm btn-info view-btn" data-id="${r.id}">View</button>
                    ${ r.status === "pending" 
                        ? `
                            <button class="btn btn-sm btn-success approve-btn" data-id="${r.id}">Approve</button>
                            <button class="btn btn-sm btn-danger reject-btn" data-id="${r.id}">Reject</button>
                        `
                        : `
                            <span class="badge bg-${r.status === 'approved' ? 'success' : 'danger'}">
                                ${r.status}
                            </span>
                        `
                    }
              </div>
            </td>
          </tr>
        `;
        $tbody.append(tr);
      });

    }).fail(function(xhr){
      console.error("Failed to load requests", xhr.responseText);
      toastError("Unable to load discount requests");
    });
  }

  loadRequests();
  setInterval(loadRequests, 10000);


  // ----------------------------------------------------------
  // RENDER PRODUCT LIST INTO MODAL
  // ----------------------------------------------------------
  function renderProductList(selector, items){
    const $c = $(selector).empty();

    if (!Array.isArray(items) || items.length === 0){
      $c.append('<p class="text-muted">No product details available.</p>');
      return;
    }

    items.forEach(function(it){

      const img = it.image ?? it.product_image ?? it.product_images?.[0] ?? randomImg();

      const name = it.product_name ?? it.product_material_name ?? `Product`;
      const qty = it.quantity ?? it.qty ?? 0;
      const price = it.unit_price ?? it.sales_price ?? it.price ?? 0;
      const discount = it.total_discount ?? it.discount ?? 0;
      const variation = `${it.color_name ?? it.colors_name ?? ''} ${it.size_name ?? ''}`.trim();
      const sku = it.barcode ?? "-";

      const card = `
        <div class="col-md-6 mb-3">
          <div class="card theme-shadow disc-modal-card">
            <div class="card-body d-flex gap-3 align-items-center">
              <div class="d-flex gap-2 align-items-center">
                <img src="${img}" alt="Product Image" style="width:72px;height:72px;object-fit:cover;border-radius:6px">
                <div class="product-info">
                  <h6 class="fw-semibold mb-0">${name}</h6>
                  <small class="text-muted">Article: ${sku}</small><br>
                </div>
              </div>
              <div style="margin-left:auto;text-align:right;">
                <p class="qty-info mb-0">
                  <span class="d-block"><b class="fs-5">${formatCurrency(price)}</b>/pc</span>
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


  // ----------------------------------------------------------
  // OPEN MODAL WITH DETAILS
  // ----------------------------------------------------------
    $(document).on("click",".view-btn", function(){
  const id = $(this).data("id");

  $.get(API_BASE + "/" + id).done(function(res){
    console.log("Detail response:", res);
    if (!res.ok) { toastError("Failed to load details"); return; }

    const r = res.data;

    let items = r.items_json ?? r.items ?? [];
    if (typeof items === "string") {
      try { items = JSON.parse(items); } catch(e){ items = []; }
    }
    if (!Array.isArray(items)) items = [];

    // Fill modal text fields
    $(".request-from").text("Request From: " + (r.store_name ?? "-"));
    $(".customer-name").text(r.customer_mobile ?? "-");
    $(".requested-amount").text(formatCurrency(r.discount_requested));
    $(".requested-by").text(r.salesman ?? "-");

    const curr = Number(r.total_payable || 0);
    $(".current-price").text(formatCurrency(curr));
    $(".discount-input").val(Number(r.discount_requested || 0).toFixed(2));
    $(".new-price").text(formatCurrency(curr - Number(r.discount_requested || 0)));

    // Render item cards
    renderProductList(".product-list-container", items);

    // --- NEW: update total product / total items ---
    const totalProducts = items.length;
    const totalItems = items.reduce((s, it) => s + Number(it.quantity ?? it.qty ?? 0), 0);
    $(".total-products").text("Total Product " + totalProducts);
    $(".total-items").text("Total Items " + totalItems);

    // hide placeholder if items present, show otherwise
    if (totalProducts > 0) {
      $(".product-list-container .no-products").remove(); // remove placeholder (if present)
    } else {
      if ($(".product-list-container .no-products").length === 0) {
        $(".product-list-container").append('<div class="col-12 text-muted no-products">No product details available.</div>');
      }
    }
    // --------------------------------------------------

    $("#viewDiscountDetails").data("request-id", r.id).modal("show");

    if (r.status !== "pending") {
      $(".modal-approve").prop("disabled", true);
      $(".modal-reject").prop("disabled", true);
    } else {
      $(".modal-approve").prop("disabled", false);
      $(".modal-reject").prop("disabled", false);
    }

  }).fail(function(){
    toastError("Error loading details");
  });
});



  // live updating new price
  $(document).on("input",".discount-input", function(){
    const discount = parseFloat($(this).val() || 0);
    const currentPrice = parseFloat($(".current-price").text().replace(/[^\d.-]/g,"") || 0);
    $(".new-price").text(formatCurrency(Math.max(currentPrice - discount, 0)));
  });


  // ----------------------------------------------------------
  // APPROVE / REJECT LOGIC
  // ----------------------------------------------------------
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
        loadRequests();
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

});
</script>

@endsection