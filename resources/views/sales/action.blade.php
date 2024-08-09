<center>
   @if(Helper::checkACL('sales', 'r'))
   <a class="btn btn-outline-info btn-xs" href="{{ $edit_url }}" role="button">
      <i class="fas fa-edit"></i>
   </a>
   <a class="btn btn-outline-info btn-xs" target="_blank" href="{{ $print_url }}" role="button">
      <i class="fas fa-print"></i>
   </a>
   @endif
</center>