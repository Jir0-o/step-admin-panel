<nav class="d-flex justify-content-between align-items-center">
  <h4 class="title fst-italic m-0">@yield('nav-title', 'Welcome')</h4>
  <div class="d-flex gap-2 align-items-center">
    <button class="theme-mode btn p-1"><i class="ri-moon-line"></i></button>
    <button class="notification btn p-1"><i class="ri-notification-3-line"></i></button>
    <a href="{{ route('admin.roles-permissions.index') }}" ><button class="btn p-1"><i class="ri-settings-5-line"></i></button></a>
    <div class="profile-box d-flex align-items-center">
      <img src="{{ asset('assets/img/profile.jpg') }}" alt="Profile Image" loading="lazy" />
      <div class="profile-info">
        <h6 class="m-0">{{ Auth::user()->name ?? 'User' }}</h6>
        <p class="m-0 text-muted">{{ Auth::user()->roles->pluck('name')->first() ?? 'Role' }}</p>
      </div>
    </div>
  </div>
</nav>
