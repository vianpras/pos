    {{-- button floating --}}
    <div class="position-sticky sticky-top text-right floating-action-menu pr-4 col-1 float-right" style="bottom: 30px; right: 50px;">
        <div class="action-menu">

            <div class="floating-action mb-2">
                <div class="badge badge-pill badge-info">Membership</div>
                <a href="/keanggotaan" class="shadow btn btn-floating btn-info rounded-circle ml-2"
                    style="width: 45px; height: 45px" role="button" aria-pressed="true">
                    <i class="fas fa-id-card-alt mt-2"></i></a>
            </div>
            <div class="floating-action mb-2">
                <div class="badge badge-pill bg-warning">Pembelian</div>
                <a href="/purchase/create" class="shadow btn btn-floating bg-orange rounded-circle ml-2"
                    style="width: 45px; height: 45px" role="button" aria-pressed="true">
                    <i class="fas fa-shopping-basket mt-2"></i></a>
            </div>
            <div class="floating-action mb-2">
                <div class="badge badge-pill bg-teal">Kasir</div>
                <a href="/sales/create" class="shadow btn btn-floating bg-teal rounded-circle ml-2"
                    style="width: 45px; height: 45px" role="button" aria-pressed="true">
                    <i class="fas fa-cash-register mt-2"></i></a>
            </div>
        </div>
        <div class="d-block action-button mr-2">
            <a class="shadow btn btn-floating btn-success rounded-circle" style="width: 45px; height: 45px"
                role="button" aria-pressed="true"
                onclick="$(this).closest('div.floating-action-menu').toggleClass('active')">
                <i class="fas fa-bars mt-2"></i>
            </a>
        </div>
    </div>
    <!-- ./wrapper -->