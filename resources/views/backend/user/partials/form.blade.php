<div class="mb-3">
    <label>Name</label>
    <input name="name" class="form-control"
           value="{{ old('name', $user->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Email</label>
    <input name="email" type="email" class="form-control"
           value="{{ old('email', $user->email ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Password</label>
    <input name="password" type="password" class="form-control">
</div>

<div class="mb-3">
    <label>Confirm Password</label>
    <input name="password_confirmation" type="password" class="form-control">
</div>

<div class="mb-3">
    <label>Roles</label>
    <select name="roles[]" class="form-control" multiple required>
        @foreach($roles as $role)
            <option value="{{ $role->name }}"
                {{ isset($user) && $user->roles->pluck('name')->contains($role->name) ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Profile Picture</label>
    <input type="file" name="profile_picture" class="form-control">
</div>

@if(isset($user) && $user->profile_photo_path)
    <img src="{{ asset('storage/'.$user->profile_photo_path) }}"
         width="80" class="mb-3">
@endif
