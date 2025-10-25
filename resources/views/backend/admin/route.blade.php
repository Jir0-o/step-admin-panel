@extends('layouts.master')

@section('title', 'Dashboard || Step Shoe Pos')
@section('body-class', '')

@section('content')

<h5 class="mb-3">Create Route</h5>
<div class="card theme-shadow">
    <div class="card-body">
        <form action="#" method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="row m-0 align-items-center">
                <div class="col-sm-6 col-md-20">
                    <div class="form-group mb-3" data-aos="fade-up" data-aos-duration="2000">
                        <label for="" class="form-label">Base URL</label>
                        <input type="text" class="form-control" name="" id="">
                    </div>
                </div>
                <div class="col-sm-6 col-md-20">
                    <div class="form-group mb-3" data-aos="fade-down" data-aos-duration="2000">
                        <label for="" class="form-label">API End Point</label>
                        <input type="text" class="form-control" name="" id="">
                    </div>
                </div>
                <div class="col-sm-6 col-md-20">
                    <div class="form-group mb-3" data-aos="fade-up" data-aos-duration="2000">
                        <label for="" class="form-label">API Auth Email</label>
                        <input type="email" class="form-control" name="" id="">
                    </div>
                </div>
                <div class="col-sm-6 col-md-20">
                    <div class="form-group mb-3" data-aos="fade-down" data-aos-duration="2000">
                        <label for="" class="form-label">API Auth Password</label>
                        <input type="password" class="form-control" name="" id="">
                    </div>
                </div>
                <div class="col-sm-6 col-md-20">
                    <button class="btn btn-secondary w-100">Data Link</button>
                </div>
                <div class="col-sm-6 col-md-20">
                    <div class="form-group">
                        <input type="checkbox" class="form-check-input" name="is_active" id="is_active">
                        <label for="is_active" class="form-check-label user-select-none">Is Active</label>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="mb-3">
    <h5 class="my-3 my-md-4">Manage Route</h5>
    <div class="card theme-shadow" data-aos="fade-up" data-aos-delay="2000" data-aos-duration="2000">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-center">
                        <th class="bg-dark text-white inria-serif">SR</th>
                        <th class="bg-dark text-white inria-serif">Store Name</th>
                        <th class="bg-dark text-white inria-serif">Base URL</th>
                        <th class="bg-dark text-white inria-serif">API End Point</th>
                        <th class="bg-dark text-white inria-serif">API Auth Email</th>
                        <th class="bg-dark text-white inria-serif">Is Active</th>
                        <th class="bg-dark text-white inria-serif">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">01</td>
                        <td class="text-center fw-semibold">Store 1</td>
                        <td class="text-center">
                            <a class="text-danger fw-semibold" href="#"
                                target="_blank">shop1.step.com/public/backoffice</a>
                        </td>
                        <td class="text-center">
                            <a class="text-danger fw-semibold" href="shop1.step.com/public/backoffice/cart"
                                target="_blank">cart</a>
                        </td>
                        <td class="text-center">admin@gmail.com</td>
                        <td class="text-center">Active</td>
                        <td class="text-center">
                            <div class="d-flex gap-2 align-items-center justify-content-center">
                                <button class="btn btn-secondary px-2 py-1">
                                    <i class="ri-pencil-line"></i> Edit
                                </button>
                                <button class="btn btn-danger px-2 py-1">
                                    <i class="ri-delete-bin-line"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">02</td>
                        <td class="text-center fw-semibold">Store 1</td>
                        <td class="text-center">
                            <a class="text-danger fw-semibold" href="#"
                                target="_blank">shop1.step.com/public/backoffice</a>
                        </td>
                        <td class="text-center">
                            <a class="text-danger fw-semibold" href="shop1.step.com/public/backoffice/checkout"
                                target="_blank">checkout</a>
                        </td>
                        <td class="text-center">admin@gmail.com</td>
                        <td class="text-center">Active</td>
                        <td class="text-center">
                            <div class="d-flex gap-2 align-items-center justify-content-center">
                                <button class="btn btn-secondary px-2 py-1">
                                    <i class="ri-pencil-line"></i> Edit
                                </button>
                                <button class="btn btn-danger px-2 py-1">
                                    <i class="ri-delete-bin-line"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">03</td>
                        <td class="text-center fw-semibold">Store 2</td>
                        <td class="text-center">
                            <a class="text-danger fw-semibold" href="#"
                                target="_blank">shop2.step.com/public/backoffice</a>
                        </td>
                        <td class="text-center">
                            <a class="text-danger fw-semibold" href="shop2.step.com/public/backoffice"
                                target="_blank">cart</a>
                        </td>
                        <td class="text-center">admin@gmail.com</td>
                        <td class="text-center">Active</td>
                        <td class="text-center">
                            <div class="d-flex gap-2 align-items-center justify-content-center">
                                <button class="btn btn-secondary px-2 py-1">
                                    <i class="ri-pencil-line"></i> Edit
                                </button>
                                <button class="btn btn-danger px-2 py-1">
                                    <i class="ri-delete-bin-line"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection