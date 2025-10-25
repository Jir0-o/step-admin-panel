@extends('layouts.master')

@section('title', 'Dashboard || Step Shoe Pos')
@section('body-class', '')

@section('content')

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center pb-3">
        <h5>Store Information</h5>
        <button class="btn btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#createStore">+ Create
            Store</button>
    </div>
    <div class="card theme-shadow">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-center">
                        <th class="bg-dark text-white inria-serif">SR</th>
                        <th class="bg-dark text-white inria-serif">Store Name</th>
                        <th class="bg-dark text-white inria-serif">Total Income</th>
                        <th class="bg-dark text-white inria-serif">Base URL</th>
                        <th class="bg-dark text-white inria-serif">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">01</td>
                        <td class="text-center">Store 1</td>
                        <td class="text-center">৳ 1, 34, 344</td>
                        <td class="text-center">
                            <a href="#" target="_blank">shop1.step.com/public/backoffice</a>
                        </td>
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
                        <td class="text-center">Store 1</td>
                        <td class="text-center">৳ 1, 34, 344</td>
                        <td class="text-center">
                            <a href="#" target="_blank">shop1.step.com/public/backoffice</a>
                        </td>
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
                        <td class="text-center">Store 1</td>
                        <td class="text-center">৳ 1, 34, 344</td>
                        <td class="text-center">
                            <a href="#" target="_blank">shop1.step.com/public/backoffice</a>
                        </td>
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
                        <td class="text-center">04</td>
                        <td class="text-center">Store 1</td>
                        <td class="text-center">৳ 1, 34, 344</td>
                        <td class="text-center">
                            <a href="#" target="_blank">shop1.step.com/public/backoffice</a>
                        </td>
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
                        <td class="text-center">05</td>
                        <td class="text-center">Store 1</td>
                        <td class="text-center">৳ 1, 34, 344</td>
                        <td class="text-center">
                            <a href="#" target="_blank">shop1.step.com/public/backoffice</a>
                        </td>
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
<div class="modal fade" id="createStore" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="createStoreLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold" id="createStoreLabel"><i class="ri-store-2-fill"></i> Create
                    Store</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row m-0">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="shop">Shop Name</label>
                            <input class="form-control" type="text" name="" id="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="url">Base URL</label>
                            <input class="form-control" type="text" name="" id="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email">User Email</label>
                            <input class="form-control" type="text" name="" id="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="password">User Password</label>
                            <input class="form-control" type="text" name="" id="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Create</button>
            </div>
        </div>
    </div>
</div>

@endsection