<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #0A6EBD;">
  <!-- Brand Logo -->
  <a href="/" class="brand-link">
    <img src="/dist/img/sasoicon.png" alt="Glo.POS" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-bold text-light">Sales Source</span>

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
        @if (Helper::checkACL('sales', 'c'))
          <li class="nav-header">Penjualan</li>
        @endif
        @if (Helper::checkACL('sales_order', 'r'))
        <li class="nav-item">
          <a href="#" class="nav-link @if($nav == 'salesCreate') active text-bold @endif">
            <i class="nav-icon fas fa-cash-register"></i>
            <p>Cashier<i class="right fas fa-angle-left"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="/sales/create" class="nav-link @if($subNav == 'company') active text-bold @endif">
                <p class="text-capitalize">View 1</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="/sales/create2" class="nav-link @if($subNav == 'company') active text-bold @endif">
                <p class="text-capitalize">View 2</p>
              </a>
            </li>
          </ul>
        </li>
        @endif
        <li class="nav-item">
          <a href="/sales/cart" class="nav-link @if($nav == 'cart') active text-bold @endif">
            <i class="nav-icon fas fa-cart-arrow-down"></i>
            <p>Cart</p>
          </a>
        </li>
        {{-- data induk --}}
        @if (Helper::checkACL('application', 'r'))
        <li class="nav-header">Pengaturan</li>
        <li class="nav-item @if($nav == 'data-induk') menu-open @endif">
          <a href="#" class="nav-link @if($nav == 'data-induk') active text-bold @endif">
            <i class="nav-icon fas fa-database"></i>
            <p class="text-capitalize">
              Data Master
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

            <li class="nav-item">
              <a href="/dataInduk/docPrefix" class="nav-link @if($subNav == 'docPrefix') active text-bold @endif">
                <i class="fab fa-autoprefixer nav-icon"></i>
                <p class="text-capitalize">docPrefix</p>
              </a>
            </li>

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