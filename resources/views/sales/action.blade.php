<center>
   @if(Helper::checkACL('sales', 'r'))
      @if($status == 'pending')
         <a class="btn btn-outline-info btn-xs" href="{{ $edit_url }}" role="button">
            <i class="fas fa-edit"></i>
         </a>
      @elseif($status == 'close')
         <a class="btn btn-outline-warning btn-xs" href="#" type="button" onclick="refundProcess('{{ $id }}')">
            Refund
         </a>
         <a class="btn btn-outline-info btn-xs" target="_blank" href="{{ $print_url }}" role="button">
            <i class="fas fa-print"></i>
         </a>
      @endif
   @endif
</center>