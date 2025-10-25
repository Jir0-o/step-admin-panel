{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.master')

@section('title', 'Dashboard || Step Shoe Pos')
@section('body-class', '')

@section('content')
<!-- sidebar + nav are included by master -->

<div class="row m-0">
    <div class="col-md-6" data-aos="fade-up-right" data-aos-duration="2000">
        <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
                <div class="d-flex gap-3 align-items-center">
                    <div class="icon-box">
                        <i class="ri-store-line"></i>
                    </div>
                    <div class="info-text">
                        <p class="m-0 text-muted">
                            Total Store
                        </p>
                        <h6 class="m-0 fw-semibold">
                            5
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6" data-aos="fade-up-left" data-aos-duration="2000">
        <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
                <div class="d-flex gap-3 align-items-center">
                    <div class="icon-box">
                        <i class="ri-box-3-line"></i>
                    </div>
                    <div class="info-text">
                        <p class="m-0 text-muted">
                            Total Product
                        </p>
                        <h6 class="m-0 fw-semibold">
                            836
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="p-4">
    <h5>Store Information</h5>
    <div class="card theme-shadow" data-aos="fade-up" data-aos-delay="2000" data-aos-duration="2000">
        <div class="">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-center">
                            <th class="bg-dark text-white inria-serif">SR</th>
                            <th class="bg-dark text-white inria-serif">Store Name</th>
                            <th class="bg-dark text-white inria-serif">Total Sale</th>
                            <th class="bg-dark text-white inria-serif">Total Profit</th>
                            <th class="bg-dark text-white inria-serif">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">01</td>
                            <td class="text-center">Store 1</td>
                            <td class="text-center">67</td>
                            <td class="text-center">৳ 1, 34, 344</td>
                            <td class="text-center">
                                <button class="btn btn-success px-2 py-1">
                                    <i class="ri-eye-fill"></i> View
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">02</td>
                            <td class="text-center">Store 1</td>
                            <td class="text-center">67</td>
                            <td class="text-center">৳ 1, 34, 344</td>
                            <td class="text-center">
                                <button class="btn btn-success px-2 py-1">
                                    <i class="ri-eye-fill"></i> View
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">03</td>
                            <td class="text-center">Store 1</td>
                            <td class="text-center">67</td>
                            <td class="text-center">৳ 1, 34, 344</td>
                            <td class="text-center">
                                <button class="btn btn-success px-2 py-1">
                                    <i class="ri-eye-fill"></i> View
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">04</td>
                            <td class="text-center">Store 1</td>
                            <td class="text-center">67</td>
                            <td class="text-center">৳ 1, 34, 344</td>
                            <td class="text-center">
                                <button class="btn btn-success px-2 py-1">
                                    <i class="ri-eye-fill"></i> View
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">05</td>
                            <td class="text-center">Store 1</td>
                            <td class="text-center">67</td>
                            <td class="text-center">৳ 1, 34, 344</td>
                            <td class="text-center">
                                <button class="btn btn-success px-2 py-1">
                                    <i class="ri-eye-fill"></i> View
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection