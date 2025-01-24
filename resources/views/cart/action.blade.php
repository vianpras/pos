<center>
    @if($stage == '0')
        <a href="{{ url('sales/cart/edit').'/'.$docnum }}" class="btn btn-sm btn-info">Edit</a>
    @elseif($stage == '1')
        <a href="{{ url('sales/create?cartCode=').$docnum }}" class="btn btn-sm btn-info">Checkout Sales</a>
    @else
        <span class="badge badge-secondary">Checked Out</span>
    @endif
</center>