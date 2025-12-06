@extends('layouts.master')

@section('content')
<div class="container">
    <h4>Edit User</h4>

    <form method="POST" action="{{ route('user.update',$user->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('backend.user.partials.form', ['user' => $user])

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
