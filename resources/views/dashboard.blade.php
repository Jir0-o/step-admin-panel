{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.master')

@section('title', 'Dashboard || Step Shoe Pos')
@section('body-class', '')

@section('content')
  <!-- sidebar + nav are included by master -->

  <div class="row m-0 inria-sans">
    <div class="col-md-6" data-aos="fade-up-right" data-aos-duration="2000">
      <div class="row m-0">
        <div class="col-lg-12 pb-2">
          <div class="theme-alert card">
            <p class="m-0 p-2">
              You Have a New Notification.
              <a href="#" class="text-decoration-underline">Click For View</a>
            </p>
          </div>
        </div>

        <div class="col-lg-6 py-2">
          <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
              <div class="d-flex gap-3 align-items-center">
                <div class="icon-box">
                  <i class="ri-shopping-bag-line"></i>
                </div>
                <div class="info-text">
                  <p class="m-0 text-muted">Total Sales of 2025</p>
                  <h6 class="m-0 fw-semibold">348</h6>
                </div>
              </div>
            </div>
            <p class="card-secondary text-center p-1 m-0">Last Year: 468</p>
          </div>
        </div>

        <div class="col-lg-6 py-2">
          <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
              <div class="d-flex gap-3 align-items-center">
                <div class="icon-box">
                  <i class="ri-currency-line"></i>
                </div>
                <div class="info-text">
                  <p class="m-0 text-muted">Today Sales</p>
                  <h6 class="m-0 fw-semibold">48</h6>
                </div>
              </div>
            </div>
            <p class="card-secondary text-center p-1 m-0">Yesterday: 68</p>
          </div>
        </div>

        <div class="col-lg-6 py-2">
          <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
              <div class="d-flex gap-3 align-items-center">
                <div class="icon-box">
                  <i class="ri-wallet-line"></i>
                </div>
                <div class="info-text">
                  <p class="m-0 text-muted">Today’s Income</p>
                  <h6 class="m-0 fw-semibold">1, 48, 370 /=</h6>
                </div>
              </div>
            </div>
            <p class="card-secondary text-center p-1 m-0">Yesterday: 68, 637 /=</p>
          </div>
        </div>

        <div class="col-lg-6 py-2">
          <div class="card theme-shadow overflow-hidden">
            <div class="card-body">
              <div class="d-flex gap-3 align-items-center">
                <div class="icon-box">
                  <i class="ri-receipt-line"></i>
                </div>
                <div class="info-text">
                  <p class="m-0 text-muted">Total Expenses</p>
                  <h6 class="m-0 fw-semibold">20, 750 /=</h6>
                </div>
              </div>
            </div>
            <p class="card-secondary text-center p-1 m-0">Yesterday: 750 /=</p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6" data-aos="fade-up-left" data-aos-duration="2000">
      <div class="card theme-shadow overflow-hidden">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="fw-semibold">Store Sales Chart</h5>
            <div class="chart-filter d-flex gap-2">
              <button class="btn" data-range="7d">7D</button>
              <button class="btn" data-range="1m">1M</button>
              <button class="btn active" data-range="6m">6M</button>
              <button class="btn" data-range="1y">1Y</button>
            </div>
          </div>
          <canvas id="storeSalesChart" height="120"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6" data-aos="fade-up-right" data-aos-delay="2000" data-aos-duration="2000">
      <div class="p-2">
        <div class="card theme-shadow overflow-hidden">
          <div class="card-body">
            <div class="d-flex gap-3 align-items-center">
              <div class="icon-box"><i class="ri-store-line"></i></div>
              <div class="info-text">
                <p class="m-0 text-muted">Total Store</p>
                <h6 class="m-0 fw-semibold">5</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6" data-aos="fade-up-left" data-aos-delay="2000" data-aos-duration="2000">
      <div class="p-2">
        <div class="card theme-shadow overflow-hidden">
          <div class="card-body">
            <div class="d-flex gap-3 align-items-center">
              <div class="icon-box"><i class="ri-box-3-line"></i></div>
              <div class="info-text">
                <p class="m-0 text-muted">Total Product</p>
                <h6 class="m-0 fw-semibold">836</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="p-4">
    <h5>Store Information</h5>
    <div class="card theme-shadow" data-aos="fade-up" data-aos-delay="4000" data-aos-duration="2000">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr class="text-center">
              <th class="bg-dark text-white inria-serif">SR</th>
              <th class="bg-dark text-white inria-serif">Store Name</th>
              <th class="bg-dark text-white inria-serif">Total Sale</th>
              <th class="bg-dark text-white inria-serif">Total Profit</th>
              <th class="bg-dark text-white inria-serif">Open At</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-center">01</td>
              <td class="text-center">Store 1</td>
              <td class="text-center">67</td>
              <td class="text-center">৳ 1, 34, 344</td>
              <td class="text-center">08:30 AM</td>
            </tr>
            <tr>
              <td class="text-center">02</td>
              <td class="text-center">Store 1</td>
              <td class="text-center">67</td>
              <td class="text-center">৳ 1, 34, 344</td>
              <td class="text-center">08:30 AM</td>
            </tr>
            <tr>
              <td class="text-center">03</td>
              <td class="text-center">Store 1</td>
              <td class="text-center">67</td>
              <td class="text-center">৳ 1, 34, 344</td>
              <td class="text-center">08:30 AM</td>
            </tr>
            <tr>
              <td class="text-center">04</td>
              <td class="text-center">Store 1</td>
              <td class="text-center">67</td>
              <td class="text-center">৳ 1, 34, 344</td>
              <td class="text-center">08:30 AM</td>
            </tr>
            <tr>
              <td class="text-center">05</td>
              <td class="text-center">Store 1</td>
              <td class="text-center">67</td>
              <td class="text-center">৳ 1, 34, 344</td>
              <td class="text-center">08:30 AM</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script>
  // Example minimal chart to prove wiring
  if (window.Chart) {
    const ctx = document.getElementById('storeSalesChart');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun'],
        datasets: [{
          label: 'Sales',
          data: [12, 19, 11, 17, 14, 22],
          fill: false
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  }
</script>
@endpush
