<center>
    @if(Helper::checkACL('pembelian', 'e'))
        <a href="{{ route('purchase.edit', $id) }}" id="editButton" class="btn btn-outline-info btn-xs">
            <i class="fas fa-edit"></i>
        </a>
        <button type="button" id="detailButton" class="btn btn-outline-info btn-xs" data-toggle="modal" data-id="{{ $id }}" data-attr="{{ route('purchase.detail', $id) }}" data-target="#modalBlade">
            <i class="fas fa-list"></i>
        </button>
    @endif
</center>