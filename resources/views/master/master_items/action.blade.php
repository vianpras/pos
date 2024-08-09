<center>
    @if(Helper::checkACL('master_item', 'e'))
        <button type="button" id="editButton" class="btn btn-outline-info btn-xs" data-id="{{ $id }}" data-attr="{{ route('master.item.edit', $id) }}">
            <i class="fas fa-edit"></i>
        </button>
    @endif

    @if(Helper::checkACL('master_item', 'd'))
    <a href="javascript:void(0);" id="_bDelete" data-toggle="tooltip" data-original-title="Delete" data-id="{{ $id }}"
        class="disable btn  @if($status==0) btn-outline-danger @else btn-outline-success @endif  btn-xs disabling"
        data-toggle="tooltip" data-placement="bottom" title=" @if($status==1) Non-Aktifkan @else Aktifkan @endif "> <i
        class="fas @if($status==0) fa-times-circle @else fa-check-circle @endif"></i> </a>
    @endif
 </center>