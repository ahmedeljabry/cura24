<a tabindex="0"
   class="btn btn-success btn-xs btn-sm mr-1 swal_status_change_order_accept text-white">
    {{ __('Accept Order') }}
</a>

<form method='post' action='{{ $url }}' class="d-none">
    @csrf
    <button type="submit" class="swal_form_submit_btn_accept_order d-none"></button>
</form>
