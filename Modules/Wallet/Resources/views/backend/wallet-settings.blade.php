@extends('backend.admin-master')
@section('site-title')
    {{__('Wallet Settings')}}
@endsection
@section('content')
    <div class="col-lg-6 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-msg.success/>
                <x-msg.error/>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrap d-flex justify-content-between">
                            <div class="left-content">
                                <h4 class="header-title">{{__('Wallet Settings')}} </h4>
                            </div>
                        </div>
                        <form action="{{route('admin.wallet.settings.update')}}" method="post">
                            @csrf
                            <div class="tab-content margin-top-10">
                                <div class="mt-5">
                                    <div class="form-group">
                                        <label for="wallet_deposit_max_value">{{__('Wallet Deposit Max Amount')}}</label>
                                        <input type="number" class="form-control" name="wallet_deposit_max_value" value="{{ get_static_option('wallet_deposit_max_value') }}" placeholder="{{ __('Wallet Deposit Max Amount') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="wallet_deposit_min_value">{{__('Wallet Deposit Min Amount')}}</label>
                                        <input type="number" class="form-control" name="wallet_deposit_min_value" value="{{ get_static_option('wallet_deposit_min_value') }}" placeholder="{{ __('Wallet Deposit Min Amount') }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3 submit_btn">{{__('Submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
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
        })(jQuery)
    </script>
@endsection

