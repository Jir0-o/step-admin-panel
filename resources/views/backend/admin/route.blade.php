@extends('layouts.master')

@section('title', 'Dashboard || Step Shoe Pos')
@section('body-class', '')

@section('content')

<h5 class="mb-3">Create Route</h5>
<div class="card theme-shadow">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('store-routes.store') }}" method="post" autocomplete="off">
            @csrf
            <div class="row m-0 align-items-center">

                {{-- Store dropdown --}}
                <div class="col-sm-6 col-md-3">
                    <div class="form-group mb-3" data-aos="fade-up" data-aos-duration="2000">
                        <label class="form-label">Store</label>
                        <select name="store_id" class="form-control">
                            <option value="">Select Store</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name ?? ('Store #'.$store->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Base URL --}}
                <div class="col-sm-6 col-md-3">
                    <div class="form-group mb-3" data-aos="fade-up" data-aos-duration="2000">
                        <label class="form-label">Base URL</label>
                        <input type="text" class="form-control" name="base_url"
                               value="{{ old('base_url') }}" placeholder="https://shop1.step.com/public/backoffice">
                    </div>
                </div>

                {{-- API End Point --}}
                <div class="col-sm-6 col-md-3">
                    <div class="form-group mb-3" data-aos="fade-down" data-aos-duration="2000">
                        <label class="form-label">API End Point</label>
                        <input type="text" class="form-control" name="endpoint"
                               value="{{ old('endpoint') }}" placeholder="cart / checkout / etc">
                    </div>
                </div>

                {{-- Is Active --}}
                <div class="col-sm-6 col-md-2">
                    <div class="form-group mb-3">
                        <div class="form-check mt-4">
                            <input type="checkbox"
                                class="form-check-input"
                                name="is_active"
                                id="is_active"
                                value="1"   {{-- add this --}}
                                {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label user-select-none">Is Active</label>
                        </div>
                    </div>
                </div>

                {{-- Submit button --}}
                <div class="col-sm-6 col-md-1">
                    <button type="submit" class="btn btn-secondary w-100 mt-3 mt-md-0">
                        Save
                    </button>
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
                        <th class="bg-dark text-white inria-serif">Is Active</th>
                        <th class="bg-dark text-white inria-serif">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routes as $index => $route)
                        <tr data-row-id="{{ $route->id }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center fw-semibold">
                                {{ $route->store->name ?? 'N/A' }}
                            </td>
                            <td class="text-center">
                                <a class="text-danger fw-semibold" href="{{ $route->base_url }}" target="_blank">
                                    {{ $route->base_url }}
                                </a>
                            </td>
                            <td class="text-center">
                                <a class="text-danger fw-semibold"
                                   href="{{ rtrim($route->base_url, '/') . '/' . ltrim($route->endpoint, '/') }}"
                                   target="_blank">
                                    {{ $route->endpoint }}
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="js-status-text">
                                    {{ $route->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 align-items-center justify-content-center">
                                    {{-- Edit button (opens modal) --}}
                                    <button
                                        type="button"
                                        class="btn btn-secondary px-2 py-1 js-edit"
                                        data-id="{{ $route->id }}"
                                        data-store-id="{{ $route->store_id }}"
                                        data-base-url="{{ $route->base_url }}"
                                        data-endpoint="{{ $route->endpoint }}"
                                        data-is-active="{{ $route->is_active ? 1 : 0 }}"
                                    >
                                        <i class="ri-pencil-line"></i> Edit
                                    </button>

                                    {{-- Delete with AJAX --}}
                                    <button
                                        type="button"
                                        class="btn btn-danger px-2 py-1 js-delete"
                                        data-id="{{ $route->id }}"
                                    >
                                        <i class="ri-delete-bin-line"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No routes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Edit Route Modal --}}
<div class="modal fade" id="editRouteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editRouteForm">
                @csrf
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Route</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="edit-errors" class="alert alert-danger d-none mb-3"></div>

                    <div class="row">

                        {{-- Store dropdown --}}
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Store</label>
                                <select name="store_id" id="edit_store_id" class="form-control">
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">
                                            {{ $store->name ?? ('Store #'.$store->id) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Base URL --}}
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Base URL</label>
                                <input type="text" class="form-control" name="base_url" id="edit_base_url">
                            </div>
                        </div>

                        {{-- API End Point --}}
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">API End Point</label>
                                <input type="text" class="form-control" name="endpoint" id="edit_endpoint">
                            </div>
                        </div>

                        {{-- Is Active --}}
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox"
                                        class="form-check-input"
                                        name="is_active"
                                        id="edit_is_active"
                                        value="1">   {{-- add this --}}
                                    <label for="edit_is_active" class="form-check-label user-select-none">
                                        Is Active
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">Close</button>
                    <button type="submit"
                            class="btn btn-primary">Update Route</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // CSRF token
        var token = '{{ csrf_token() }}';

        // Open edit modal and fill data
        $('.js-edit').on('click', function () {
            var btn = $(this);

            $('#edit_id').val(btn.data('id'));
            $('#edit_store_id').val(btn.data('store-id'));
            $('#edit_base_url').val(btn.data('base-url'));
            $('#edit_endpoint').val(btn.data('endpoint'));
            $('#edit_is_active').prop('checked', btn.data('is-active') == 1);

            $('#edit-errors').addClass('d-none').empty();

            $('#editRouteModal').modal('show');
        });

        // Submit edit via AJAX
        $('#editRouteForm').on('submit', function (e) {
            e.preventDefault();

            var id = $('#edit_id').val();

            var url = '{{ route("store-routes.update", "__ID__") }}';
            url = url.replace('__ID__', id);

            var formData = {
                _token: token,
                _method: 'PUT',
                store_id: $('#edit_store_id').val(),
                base_url: $('#edit_base_url').val(),
                endpoint: $('#edit_endpoint').val(),
                is_active: $('#edit_is_active').is(':checked') ? 1 : 0
            };

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function () {
                    // Simplest: reload page to refresh table
                    location.reload();
                },
                error: function (xhr) {
                    // Basic validation error display
                    var errorsBox = $('#edit-errors');
                    errorsBox.removeClass('d-none').empty();

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function (key, messages) {
                            errorsBox.append('<div>' + messages[0] + '</div>');
                        });
                    } else {
                        errorsBox.text('Something went wrong. Please try again.');
                    }
                }
            });
        });

        // Delete with AJAX
        $('.js-delete').on('click', function () {
            if (!confirm('Are you sure to delete this route?')) {
                return;
            }

            var btn = $(this);
            var id = btn.data('id');

            var url = '{{ route("store-routes.destroy", "__ID__") }}';
            url = url.replace('__ID__', id);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: token,
                    _method: 'DELETE'
                },
                success: function () {
                    // Remove row from table
                    btn.closest('tr').remove();
                },
                error: function () {
                    alert('Delete failed. Try again.');
                }
            });
        });
    });
</script>
@endsection