<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard || Step Shoe Pos')</title>

  {{-- Core styles from /public/assets --}}
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-5.3.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/aos-master/dist/aos.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/font/RemixIcon_v4.7.0/remixicon.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />

  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  {{-- jQuery (your template expects it early) --}}
  <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>

  @stack('page-styles')
</head>
<body class="@yield('body-class')">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  {{-- Sidebar --}}
  @include('backend.includes.side-nav')

  {{-- Body + Nav + Content --}}
  <div class="layout-body">
    @include('backend.includes.nav')

    <main>
      <div class="content-layout">
        <div class="page-content">
          @yield('content')
          @include('backend.includes.footer')
        </div>
      </div>
    </main>
  </div>

  {{-- Core scripts from /public/assets --}}
  <script src="{{ asset('assets/js/chart.js') }}"></script>
  <script src="{{ asset('assets/js/index.js') }}"></script>
  <script src="{{ asset('assets/aos-master/dist/aos.js') }}"></script>
  <script src="{{ asset('assets/js/bootstrap-5.3.bundle.min.js') }}"></script>
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
  <script> if (window.AOS) AOS.init(); </script>

</body>
</html>
