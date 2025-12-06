@extends('layouts.master')

@section('content')
<div class="container">
    <h4>Create User</h4>

    <form method="POST" action="{{ route('user.store') }}" enctype="multipart/form-data">
        @csrf

        @include('backend.user.partials.form', ['user' => null])

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
