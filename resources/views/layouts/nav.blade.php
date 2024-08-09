<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #0A6EBD;">
  <!-- Brand Logo -->
  <a href="/" class="brand-link">
    <img src="/dist/img/logo_circle.png" alt="Glo.POS" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-bold text-light">Glo.</span>
    <span class="brand-text font-weight-bold text-light">P</span>
    <span class="brand-text font-weight-bold text-light">O</span>
    <span class="brand-text font-weight-bold text-light">S</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image" data-toggle="modal" data-target="#modalUser">
        <img src="/gambar/user/{{ Auth::id() }}" class="img-circle elevation-2" alt="{{ Auth::user()->name }}">
      </div>
      <div class="info">
        <a href="#" class="d-block text-capitalize text-light">{{ Auth::user()->name }}</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="/" class="nav-link @if($nav == 'dashboard') active text-bold @endif">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        {{-- Pembelian --}}
        @if (Helper::checkACL('purchase', 'r'))
        <li class="nav-header">Pembelian</li>
        @endif
        @if (Helper::checkACL('purchase_order', 'r'))
        <li class="nav-item">
          <a href="/purchase" class="nav-link @if($nav == 'purchasesIndex') active text-bold @endif">
            <i class="nav-icon fas fa-shopping-basket"></i>
            <p>Order Pembelian</p>
          </a>
        </li>
        @endif
        {{-- @if (Helper::checkACL('transaction_purchase', 'r'))
        <li class="nav-item">
          <a href="/purchase" class="nav-link @if($nav == 'purchases') active text-bold @endif">
            <i class="nav-icon fas fa-file-medical"></i>
            <p>Transaksi Pembelian</p>
            {!!Helper::counterData('purchases','pending')!!}

          </a>
        </li>
        @endif --}}
        {{-- Penjualan --}}
        @if (Helper::checkACL('sales', 'c'))
        <li class="nav-header">Penjualan</li>
        @endif
        @if (Helper::checkACL('sales_order', 'r'))
        <li class="nav-item">
          <a href="/sales/create" class="nav-link @if($nav == 'salesCreate') active text-bold @endif">
            <i class="nav-icon fas fa-cash-register"></i>
            <p>Kasir</p>
          </a>
        </li>
        @endif
        {{-- @if (Helper::checkACL('transaction_sales', 'r'))
        <li class="nav-item">
          <a href="/sales" class="nav-link @if($nav == 'sales') active text-bold @endif">
            <i class="nav-icon fas fa-file-invoice-dollar"></i>
            <p>Transaksi Penjualan</p>
            {!!Helper::counterData('sales','pending')!!}
          </a>
        </li>
        @endif --}}

        {{-- Kas Masuk / Keluar --}}
        @if (Helper::checkACL('sales', 'c'))
        <li class="nav-header">Kas</li>
        @endif
        @if (Helper::checkACL('cash_in', 'r'))
        <li class="nav-item">
          <a href="/cash/cash_bank" class="nav-link @if($nav == 'cashBank') active text-bold @endif">
            <i class="nav-icon fas fa-university"></i>
            <p>Kas & Bank </p>
          </a>
        </li>
        @endif
        @if (Helper::checkACL('cash_in', 'r'))
        <li class="nav-item">
          <a href="/cash/in" class="nav-link @if($nav == 'cashIn') active text-bold @endif">
            <i class="nav-icon fas fa-money-bill-alt"></i>
            <p>Kas Masuk </p>
          </a>
        </li>
        @endif

        @if (Helper::checkACL('cash_out', 'r'))
        <li class="nav-item">
          <a href="/cash/out" class="nav-link @if($nav == 'cashOut') active text-bold @endif">
            <i class="nav-icon  fas fa-money-bill-wave"></i>
            <p>Kas Keluar </p>
          </a>
        </li>
        @endif
        
        @if (Helper::checkACL('cash_recap', 'r'))
        <li class="nav-item">
          <a href="/cash/recap" class="nav-link @if($nav == 'cashRecap') active text-bold @endif">
            <i class="nav-icon fas fa-file-invoice-dollar"></i>
            <p>Rekap Kas </p>
          </a>
        </li>
        @endif

        @if ((Helper::checkACL('membership', 'r')) || (Helper::checkACL('booking', 'r')) )
          <li class="nav-header">Fitur</li>
          @if (Helper::checkACL('membership', 'r'))
          <li class="nav-item">
            <a href="/keanggotaan" class="nav-link @if($nav == 'membership') active text-bold @endif">
              <i class="fas fa-id-card-alt nav-icon"></i>
              <p>Keanggotaan</p>
            </a>
          </li>
          @endif
          {{-- @if (Helper::checkACL('booking', 'r'))
          <li class="nav-item">
            <a href="/booking" class="nav-link @if($nav == 'booking') active text-bold @endif">
              <i class="nav-icon fas fa-book-open"></i>
              <p>Pemesanan</p>
              {!!Helper::counterData('bookings','pending')!!}
            </a>
          </li>
          @endif --}}
          @if (Helper::checkACL('jurnal_umum', 'r'))
          <li class="nav-item">
            <a href="/jurnal" class="nav-link @if($nav == 'jurnal_umum') active text-bold @endif">
              <i class="nav-icon fas fa-book"></i>
              <p>Jurnal Umum</p>
            </a>
          </li>
          @endif
        @endif

        {{-- Report --}}
        @if ((Helper::checkACL('purchase_report', 'r')) || (Helper::checkACL('sales_report', 'r')) || (Helper::checkACL('overall_report', 'r')))
        <li class="nav-header">Laporan</li>
        <li class="nav-item @if($nav == 'report') menu-open @endif">
          <a href="#" class="nav-link @if($nav == 'report') active text-bold @endif">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p class="text-capitalize">
              Laporan
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            @if (Helper::checkACL('purchase_report', 'r'))
            <li class="nav-item">
              <a href="/purchaseReport" class="nav-link @if($subNav == 'purchase_report') active text-bold @endif">
                <i class="fas fa-file-import nav-icon"></i>
                <p class="text-capitalize">Laporan Pembelian</p>
              </a>
            </li>
            @endif

            @if (Helper::checkACL('sales_report', 'r'))
            <li class="nav-item">
              <a href="/salesReport" class="nav-link @if($subNav == 'sales_report') active text-bold @endif">
                <i class="fas fa-file-export nav-icon"></i>
                <p class="text-capitalize">Laporan Penjualan</p>
              </a>
            </li>
            @endif

            @if (Helper::checkACL('sales_report', 'r'))
            <li class="nav-item">
              <a href="/kasirReport" class="nav-link @if($subNav == 'kasir_report') active text-bold @endif">
                <i class="fas fa-file-export nav-icon"></i>
                <p class="text-capitalize">Laporan Penjualan Kasir</p>
              </a>
            </li>
            @endif

            @if (Helper::checkACL('sales_report', 'r'))
            <li class="nav-item">
              <a href="/salesReport/pendapatan" class="nav-link @if($subNav == 'pendapatan_sales_report') active text-bold @endif">
                <i class="fas fa-file-export nav-icon"></i>
                <p class="text-capitalize">Laporan Pendapatan Penjualan</p>
              </a>
            </li>
            @endif

            @if (Helper::checkACL('overall_report', 'r'))
            <li class="nav-item">
              <a href="/proyek" class="nav-link @if($subNav == 'proyek') active text-bold @endif">
                <i class="fas fa-file-contract nav-icon"></i>
                <p class="text-capitalize">Laporan Keseluruhan</p>
              </a>
            </li>
            @endif
          </ul>
        </li>
        @endif

        {{-- data induk --}}
        @if (Helper::checkACL('application', 'r'))
        <li class="nav-header">Pengaturan</li>
        <li class="nav-item @if($nav == 'data-induk') menu-open @endif">
          <a href="#" class="nav-link @if($nav == 'data-induk') active text-bold @endif">
            <i class="nav-icon fas fa-database"></i>
            <p class="text-capitalize">
              Data Induk
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">

            @if (Helper::checkACL('company', 'r'))
            <li class="nav-item">
              <a href="/dataInduk/perusahaan" class="nav-link @if($subNav == 'company') active text-bold @endif">
                <i class="fas fa-building nav-icon"></i>
                <p class="text-capitalize">Pengaturan Aplikasi</p>
              </a>
            </li>
            @endif
            {{-- @if (Helper::checkACL('master_docPrefix', 'xx'))
            <li class="nav-item">
              <a href="/dataInduk/docPrefix" class="nav-link @if($subNav == 'docPrefix') active text-bold @endif">
                <i class="fab fa-autoprefixer nav-icon"></i>
                <p class="text-capitalize">docPrefix</p>
              </a>
            </li>
            @endif --}}
            @if (Helper::checkACL('master_acl', 'r'))
            <li class="nav-item">
              <a href="/dataInduk/acl" class="nav-link @if($subNav == 'hakakses') active text-bold @endif">
                <i class="fas fa-key nav-icon"></i>
                <p class="text-capitalize">Hak Akses</p>
              </a>
            </li>
            @endif
            @if (Helper::checkACL('master_user', 'r'))
            <li class="nav-item">
              <a href="/dataInduk/pengguna" class="nav-link @if($subNav == 'user') active text-bold @endif">
                <i class="fas fa-users nav-icon"></i>
                <p class="text-capitalize">Pengguna</p>
              </a>
            </li>
            @endif
            @if (Helper::checkACL('master_unit', 'r'))
            <li class="nav-item">
              <a href="/dataInduk/satuan" class="nav-link @if($subNav == 'unit') active text-bold @endif">
                <i class="fas fa-weight-hanging nav-icon"></i>
                <p class="text-capitalize">Satuan</p>
              </a>
            </li>
            @endif
            @if (Helper::checkACL('master_category', 'r'))
            <li class="nav-item">
              <a href="/dataInduk/kategori" class="nav-link @if($subNav == 'category') active text-bold @endif">
                <i class="fas fa-tags nav-icon"></i>
                <p class="text-capitalize">Kategori Item</p>
              </a>
            </li>
            @endif
            @if (Helper::checkACL('master_sales_category', 'r'))
            <li class="nav-item">
              <a href="/dataInduk/kategoriPenjualan" class="nav-link @if($subNav == 'master_sales_category') active text-bold @endif">
                <i class="fas fa-hand-holding-usd nav-icon"></i>
                <p class="text-capitalize">Kategori Penjualan</p>
              </a>
            </li>
            @endif
            @if (Helper::checkACL('master_item', 'r'))
            <li class="nav-item">
              <a href="/dataInduk/item" class="nav-link @if($subNav == 'barang') active text-bold @endif">
                <i class="fas fa-boxes nav-icon"></i>
                <p class="text-capitalize">Item</p>
              </a>
            </li>
            @endif
            @if (Helper::checkACL('master_item', 'r'))
            <li class="nav-item">
              <a href="/dataInduk/profitsetting" class="nav-link @if($subNav == 'profit-setting') active text-bold @endif">
                <i class="fas fa-boxes nav-icon"></i>
                <p class="text-capitalize">Profit</p>
              </a>
            </li>
            @endif
            @if (Helper::checkACL('master_coa', 'r'))
            <li class="nav-item">
              <a href="/dataInduk/coa" class="nav-link @if($subNav == 'coa') active text-bold @endif">
                <i class="fas fa-list-alt nav-icon"></i>
                <p class="text-capitalize">Chart Of Account</p>
              </a>
            </li>
            @endif
          </ul>
        </li>
        @endif

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>