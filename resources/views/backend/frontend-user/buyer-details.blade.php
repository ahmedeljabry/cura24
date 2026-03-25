@extends('backend.admin-master')
@section('site-title')
    {{__('Buyer Details')}}
@endsection

@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        @if(!empty($buyer_details))
            <div class="row mt-5">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="checkbox-inlines">
                                <label><strong>{{ __('Buyer ID:') }} </strong>#{{ $buyer_details->id }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5 mt-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="border-bottom mb-3">
                                <h5>{{ __('Buyer Details') }}</h5>
                            </div>
                            <div class="single-checbox">
                                <div class="checkbox-inlines">
                                    <label><strong>{{ __('Name:') }} </strong>{{ $buyer_details->name }}</label>
                                </div>
                                <div class="checkbox-inlines">
                                    <label><strong>{{ __('Email:') }} </strong>{{ $buyer_details->email }}</label>
                                </div>
                                <div class="checkbox-inlines">
                                    <label><strong>{{ __('Phone:') }} </strong>{{ $buyer_details->phone }}</label>
                                </div>
                                <div class="checkbox-inlines">
                                    <label><strong>{{ __('Address:') }} </strong>{{ $buyer_details->address }}</label>
                                </div>
                                <div class="checkbox-inlines">
                                    <label><strong>{{ __('City:') }} </strong>{{ optional($buyer_details->city)->service_city }}</label>
                                </div>
                                <div class="checkbox-inlines">
                                    <label><strong>{{ __('Area:') }} </strong>{{ optional($buyer_details->area)->service_area }}</label>
                                </div>
                                <div class="checkbox-inlines">
                                    <label><strong>{{ __('Post Code:') }} </strong>{{ $buyer_details->post_code }}</label>
                                </div>
                                <div class="checkbox-inlines">
                                    <label><strong>{{ __('Country:') }} </strong>{{ optional($buyer_details->country)->country }}</label>
                                </div>
                                <div class="checkbox-inlines">
                                    <label><strong>{{ __('User Verify:') }} </strong>
                                        @if($buyer_details->email_verified == 1)
                                            <span class="text-warning">{{ __('Verified') }}</span>
                                        @else
                                            <span class="text-info">{{ __('Not Verified') }}</span>
                                        @endif
                                        <x-status-change :url="route('admin.frontend.buyer.profile.verify', $buyer_details->id)"/>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <div class="col-lg-7 mt-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="border-bottom mb-3">
                                <h5>{{ __('Buyer Information') }}</h5>
                            </div>
                            <div class="single-checbox">
                                <div class="checkbox-inlines">
                                    <p>{{ __('No additional verification documents available for buyers.') }}</p>
                                </div>
                            </div>   
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('script')
<script>
    (function($){
    "use strict";
    $(document).ready(function() {
        
        $(document).on('click','.swal_status_change',function(e){
            e.preventDefault();
                Swal.fire({
                title: '{{__("Are you sure to change status?")}}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $(this).next().find('.swal_form_submit_btn').trigger('click');
                }
            });
        });

    });
})(jQuery);
</script>
@endsection