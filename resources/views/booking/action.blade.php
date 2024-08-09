<center>
   @if(Helper::checkACL('booking', 'r'))
   <a class="btn btn-outline-info btn-xs" href="{{ $edit_url }}" role="button">
      <i class="fas fa-edit"></i>
   </a>
   @endif
</center>