<aside>
  <div class="sidebar">
    <div class="d-flex flex-column gap-2">
      <div class="side-header d-flex justify-content-between align-items-center p-3">
        <img src="{{ asset('assets/img/logo light.png') }}" alt="Main Logo" loading="lazy" />
        <button class="btn btn-dark" onclick="sidebar()">
          <i class="ri-menu-unfold-4-line"></i>
        </button>
      </div>

      <div class="menu-list">
        <p class="m-0 text-muted ps-3 pb-2"><span class="side-text">General</span></p>
        <ul class="m-0 p-0">
          <li>
            <a href="{{ url('/dashboard') }}" class="ps-3">
              <i class="ri-gallery-view-2"></i>
              <span class="side-text">Dashboard</span>
            </a>
          </li>
          <li>
            <a href="{{ url('/store') }}" class="ps-3">
              <i class="ri-store-2-line"></i>
              <span class="side-text">Store</span>
            </a>
          </li>
          <li>
            <a href="{{ url('/discount') }}" class="ps-3">
              <i class="ri-price-tag-3-line"></i>
              <span class="side-text">Discount</span>
            </a>
          </li>
        </ul>

        <p class="m-0 text-muted ps-3 pt-3 pb-2"><span class="side-text">Advance</span></p>
        <ul class="m-0 p-0">
          <li>
            <a href="{{ route('stores.index') }}" class="ps-3">
              <i class="ri-database-line"></i>
              <span class="side-text">Create Store</span>
            </a>
          </li>
          <li>
            <a href="{{ url('/route') }}" class="ps-3">
              <i class="ri-links-fill"></i>
              <span class="side-text">Route Management</span>
            </a>
          </li>
          <li>
            <a href="{{ url('/user') }}" class="ps-3">
              <i class="ri-user-line"></i>
              <span class="side-text">User Management</span>
            </a>
          </li>
          <li>
            <a href="{{ route('admin.roles-permissions.index') }}" class="ps-3">
              <i class="ri-shield-keyhole-line"></i>
              <span class="side-text">Role & Permission</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="p-3">
        <span class="side-text w-100 d-block">
          {{-- Wire to your auth route if needed --}}
          <a href="{{ route('logout') }}" class="btn btn-danger w-100"
             onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Log Out
          </a>
        </span>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      </div>
    </div>
  </div>
</aside>
