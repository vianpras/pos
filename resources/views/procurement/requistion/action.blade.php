<center>
   @if(Helper::checkACL('procurement_requistion', 'e'))
   <a class="btn btn-outline-info btn-xs" href="{{ $edit_url }}" role="button">
      <i class="fas fa-edit"></i>
   </a>
   @endif

   {{-- @if(Helper::checkACL('procurement_requistion', 'u'))
   <a href="javascript:void(0);" id="_bDelete" data-toggle="tooltip" data-original-title="Delete" data-id="{{ $id }}"
      class="disable btn  @if($status==1) btn-outline-danger @else btn-outline-success @endif  btn-xs disabling"
      data-toggle="tooltip" data-placement="bottom" title=" @if($status==1) Non-Aktifkan @else Aktifkan @endif "> <i
         class="fas @if($status==1) fa-times-circle @else fa-check-circle @endif"></i> </a>
   @endif --}}

</center>