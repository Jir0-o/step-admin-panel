@extends('layouts.master')

@section('title', 'Dashboard || Step Shoe Pos')
@section('body-class', '')

@section('content')
<div class="p-4">
    <div class="d-flex justify-content-between align-items-center pb-3">
        <h5>Store Information</h5>
        <button id="btnOpenCreate" class="btn btn-secondary" type="button">+ Create Store</button>
    </div>

    <div class="card theme-shadow">
        <div class="table-responsive">
            <table class="table align-middle" id="storesTable">
                <thead>
                    <tr class="text-center">
                        <th class="bg-dark text-white inria-serif">SR</th>
                        <th class="bg-dark text-white inria-serif">Store Name</th>
                        <th class="bg-dark text-white inria-serif">Total Income</th>
                        <th class="bg-dark text-white inria-serif">Base URL</th>
                        <th class="bg-dark text-white inria-serif">Login API</th>
                        <th class="bg-dark text-white inria-serif">Action</th>
                    </tr>
                </thead>
                <tbody id="storesTbody">
                    <!-- Rows injected by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create / Edit modal -->
<div class="modal fade" id="createStore" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="createStoreLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="storeForm" class="modal-content" method="POST" action="{{ route('stores.store') }}">
            @csrf
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold" id="createStoreLabel"><i class="ri-store-2-fill"></i>
                    <span id="modalTitle">Create Store</span></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="storeId" value="">
                <div class="row m-0">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="shop">Shop Name</label>
                            <input class="form-control" type="text" name="name" id="name" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="url">Base URL</label>
                            <input class="form-control" type="url" name="base_url" id="base_url"
                                   placeholder="https://example.com/public/backoffice">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="login_api_url">Login API URL</label>
                            <input class="form-control" type="url" name="login_api_url" id="login_api_url"
                                   placeholder="https://shoe.com/api/login">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email">User Email</label>
                            <input class="form-control" type="email" name="user_email" id="user_email">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3 position-relative">
                            <label for="password">User Password</label>
                            <div class="input-group">
                                <input class="form-control" type="password" name="user_password" id="user_password"
                                    placeholder="Leave empty to keep existing password when editing" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" id="togglePwdBtn" type="button" title="Show/Hide">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </div>
                            <small class="text-muted">Leave blank to keep current password. Enter a new password to replace it.</small>
                        </div>
                    </div>

                </div>

                <div id="formErrors" style="display:none;" class="alert alert-danger"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="saveStoreBtn" type="button" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- jQuery & Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    const createStoreEl = document.getElementById('createStore');
    const createStoreModal = new bootstrap.Modal(createStoreEl);

    function fetchStores() {
        $.get("{{ route('stores.index') }}")
            .done(function(res) {
                if (res && res.ok) renderRows(res.data);
                else {
                    console.error('fetchStores: unexpected response', res);
                    alert('Failed to load stores');
                }
            })
            .fail(function(xhr, status, err) {
                console.error('fetchStores error', status, err, xhr.responseText);
                alert('Failed to load stores');
            });
    }

    function renderRows(stores) {
        const tbody = $('#storesTbody').empty();
        if (!stores || stores.length === 0) {
            tbody.append('<tr><td colspan="6" class="text-center">No stores found</td></tr>');
            return;
        }

        stores.forEach(function(store, idx) {
            const baseUrl = store.base_url ? `<a href="${store.base_url}" target="_blank">${store.base_url}</a>` : '-';
            const loginApi = store.login_api_url ? `<a href="${store.login_api_url}" target="_blank">${store.login_api_url}</a>` : '-';
            tbody.append(`
                <tr data-id="${store.id}">
                    <td class="text-center">${(idx+1).toString().padStart(2,'0')}</td>
                    <td class="text-center">${escapeHtml(store.name)}</td>
                    <td class="text-center">৳ 0</td>
                    <td class="text-center">${baseUrl}</td>
                    <td class="text-center">${loginApi}</td>
                    <td class="text-center">
                        <button class="btn btn-secondary btn-edit" data-id="${store.id}">Edit</button>
                        <button class="btn btn-danger btn-delete" data-id="${store.id}">Delete</button>
                    </td>
                </tr>
            `);
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")
                           .replace(/"/g,"&quot;").replace(/'/g,"&#039;");
    }

    $('#btnOpenCreate').on('click', function() {
        $('#modalTitle').text('Create Store');
        $('#storeForm')[0].reset();
        $('#storeId').val('');
        $('#formErrors').hide().empty();
        createStoreModal.show();
    });

    $('#saveStoreBtn').on('click', function(e) {
        e.preventDefault();
        $('#formErrors').hide().empty();

        const id = $('#storeId').val();
        const payload = {
            name: $('#name').val(),
            base_url: $('#base_url').val(),
            login_api_url: $('#login_api_url').val(),
            user_email: $('#user_email').val(),
            user_password: $('#user_password').val()
        };

        if (!payload.name) {
            $('#formErrors').show().text('Name is required');
            return;
        }

        if (id) {
            const updateUrl = "{{ route('stores.update', ':id') }}".replace(':id', id);
            $.ajax({
                url: updateUrl,
                type: 'POST',
                data: Object.assign(payload, {_method: 'PUT'}),
            })
            .done(function(res) {
                if (res && res.ok) {
                    createStoreModal.hide();
                    fetchStores();
                } else {
                    $('#formErrors').show().text(res?.message || 'Update failed');
                    console.error('update response', res);
                }
            })
            .fail(function(xhr) { showAjaxErrors(xhr); });
        } else {
            $.post("{{ route('stores.store') }}", payload)
            .done(function(res) {
                if (res && res.ok) {
                    createStoreModal.hide();
                    fetchStores();
                } else {
                    $('#formErrors').show().text(res?.message || 'Create failed');
                    console.error('create response', res);
                }
            })
            .fail(function(xhr) { showAjaxErrors(xhr); });
        }
    });

    // Edit: fetch single, populate (but do NOT pre-fill password)
    $('#storesTbody').on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const showUrl = "{{ route('stores.show', ':id') }}".replace(':id', id);

        $.get(showUrl)
        .done(function(res) {
            if (res && res.ok) {
                $('#modalTitle').text('Edit Store');
                $('#storeId').val(res.data.id ?? '');
                $('#name').val(res.data.name ?? '');
                $('#base_url').val(res.data.base_url ?? '');
                $('#login_api_url').val(res.data.login_api_url ?? '');
                $('#user_email').val(res.data.user_email ?? '');
                // DO NOT prefill password — leave blank so user must enter to change
                $('#user_password').val('');
                $('#formErrors').hide().empty();
                createStoreModal.show();
            } else {
                console.error('show returned unexpected response', res);
                alert(res?.message || 'Failed to load store data');
            }
        })
        .fail(function(xhr) {
            console.error('show error', xhr);
            alert('Failed to load store data');
        });
    });

    // toggle show/hide password
    $(document).on('click', '#togglePwdBtn', function() {
        const $pwd = $('#user_password');
        const type = $pwd.attr('type') === 'password' ? 'text' : 'password';
        $pwd.attr('type', type);
        // optional: toggle icon
        $(this).find('i').toggleClass('ri-eye-line ri-eye-off-line');
    });


    // Delete
    $('#storesTbody').on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        if (!confirm('Delete this store?')) return;
        const deleteUrl = "{{ route('stores.destroy', ':id') }}".replace(':id', id);

        $.ajax({
            url: deleteUrl,
            type: 'POST',
            data: {_method: 'DELETE'}
        })
        .done(function(res) {
            if (res && res.ok) fetchStores();
            else alert(res?.message || 'Delete failed');
        })
        .fail(function(xhr) {
            console.error('delete error', xhr);
            alert('Delete failed');
        });
    });

    function showAjaxErrors(xhr) {
        console.error('ajax error', xhr);
        let msg = 'Error occurred';
        if (xhr && xhr.responseJSON) {
            if (xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join('; ');
            } else {
                msg = xhr.responseJSON.message || JSON.stringify(xhr.responseJSON);
            }
        } else if (xhr.responseText) {
            msg = xhr.responseText;
        }
        $('#formErrors').show().text(msg);
    }

    // initial load
    fetchStores();
});
</script>
@endsection

