<center>
   @if(Helper::checkACL('master_sales_category', 'e'))
   <button type="button" id="editButton" class="btn btn-outline-info btn-xs" data-toggle="modal" data-id="{{ $id }}" data-attr="{{ route('kategoriPenjualan.edit', $id) }}" data-target="#modalBlade">
      <i class="fas fa-edit"></i>
   </button>
   @endif
   @if(Helper::checkACL('master_sales_category', 'd'))
   <a href="javascript:void(0);" id="_bDelete" data-toggle="tooltip" data-original-title="Delete" data-id="{{ $id }}"
      class="disable btn  @if($status==1) btn-outline-danger @else btn-outline-success @endif  btn-xs disabling"
      data-toggle="tooltip" data-placement="bottom" title=" @if($status==1) Non-Aktifkan @else Aktifkan @endif "> <i
         class="fas @if($status==1) fa-times-circle @else fa-check-circle @endif"></i> </a>
   @endif
</center>