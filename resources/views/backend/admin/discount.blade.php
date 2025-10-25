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
                        <h6 class="fw-semibold mb-3">Request From: Step Store 1</h6>
                    </div>
                    <div class="col-md-6">
                        <table class="mb-3">
                            <tr>
                                <td>Customer Name</td>
                                <td>: &nbsp; &nbsp; </td>
                                <td class="fw-semibold">Customer 1</td>
                            </tr>
                            <tr>
                                <td>Requested Amount</td>
                                <td>: &nbsp; &nbsp; </td>
                                <td class="fw-semibold py-2">500/=</td>
                            </tr>
                            <tr>
                                <td>Requested By</td>
                                <td>: &nbsp; &nbsp; </td>
                                <td class="fw-semibold">MD Hridoy Sheikh</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="mb-3">
                            <tr>
                                <td>Current Price</td>
                                <td> : &nbsp; &nbsp; </td>
                                <td class="fw-semibold text-end">3500/=</td>
                            </tr>
                            <tr>
                                <td>Discount Price</td>
                                <td> : &nbsp; &nbsp; </td>
                                <td class="fw-semibold">
                                    <input class="form-control text-end py-1" type="text" value="300">
                                </td>
                            </tr>
                            <tr>
                                <td>New Price</td>
                                <td> : &nbsp; &nbsp; </td>
                                <td class="fw-semibold text-end">-</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-danger w-100 mb-3">Cancel</button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-secondary w-100 mb-3">Confirm</button>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-semibold">Product List</h6>
                                <p class="text-muted">
                                    <span class="me-3">Total Product 2</span>
                                    <span>Total Items 3</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card theme-shadow disc-modal-card">
                            <div class="card-body">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="./assets/img/product-2.webp" alt="Product Image" loading="lazy">
                                        <div class="product-info">
                                            <h6 class="fw-semibold mb-0">Twinkler Boy's Canvas</h6>
                                            <small class="text-muted">SKU: 41597A27</small>
                                            <small class="text-muted">Variation: Black-38-Cotton</small>
                                        </div>
                                    </div>
                                    <p class="qty-info mb-0">
                                        <span class="d-block">
                                            <b class="fs-5">300/</b>pc
                                        </span>
                                        <span class="d-block">
                                            <strong>QTY: 1</strong>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card theme-shadow disc-modal-card">
                            <div class="card-body">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="./assets/img/product-1.jpg" alt="Product Image" loading="lazy">
                                        <div class="product-info">
                                            <h6 class="fw-semibold mb-0">Twinkler Boy's Canvas</h6>
                                            <small class="text-muted">SKU: 41597A27</small>
                                            <small class="text-muted">Variation: Black-38-Cotton</small>
                                        </div>
                                    </div>
                                    <p class="qty-info mb-0">
                                        <span class="d-block">
                                            <b class="fs-5">300/</b>pc
                                        </span>
                                        <span class="d-block">
                                            <strong>QTY: 1</strong>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Reject</button>
                <button type="button" class="btn btn-success">Approve</button>
            </div>
        </div>
    </div>
</div>
<!-- modal for discount details end -->
@endsection