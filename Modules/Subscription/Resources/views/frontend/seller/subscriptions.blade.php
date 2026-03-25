@extends('frontend.user.buyer.buyer-master')
@section('site-title')
    {{__('Subscription')}}
@endsection
@section('style')
    <style>
        .btn-wrapper {
            display: block;
        }
        .btn-wrapper .cmn-btn {
            line-height: 20px;
        }
        .cmn-btn {
            color: var(--paragraph-color);
            font-size: 16px;
            font-weight: 500;
            font-family: var(--new-body-font);
            display: inline-block;
            text-transform: capitalize;
            text-align: center;
            cursor: pointer;
            line-height: 20px;
            padding: 13px 25px;
            -webkit-transition: all 0.3s;
            transition: all 0.3s;
            -webkit-transition: all 0.3s ease-in;
            transition: all 0.3s ease-in;
            border-color: inherit;
        }
        @media only screen and (max-width: 575.98px) {
            .cmn-btn {
                padding: 10px 20px;
                font-size: 15px;
            }
        }
        @media only screen and (max-width: 375px) {
            .cmn-btn {
                padding: 10px 15px;
                font-size: 14px;
            }
        }
        .cmn-btn.btn-bg-1 {
            background: var(--main-color-one);
            color: #fff;
            border: 1px solid var(--main-color-one);
            border-color: inherit;
        }
        .cmn-btn.btn-bg-1:hover {
            background: none;
            color: var(--main-color-one);
            border-color: inherit;
        }
        .cmn-btn.btn-bg-2 {
            background: var(--main-color-two);
            color: #fff;
            border: 1px solid var(--main-color-two);
            border-color: inherit;
        }
        .cmn-btn.btn-bg-2:hover {
            background: none;
            color: var(--main-color-two);
            border-color: inherit;
        }
        .cmn-btn.btn-bg-3 {
            background: var(--main-color-three);
            color: #fff;
            border: 1px solid var(--main-color-three);
            border-color: inherit;
        }
        .cmn-btn.btn-bg-3:hover {
            background: none;
            color: var(--main-color-three);
            border-color: inherit;
        }
        .cmn-btn.btn-outline-1 {
            border: 1px solid var(--main-color-one);
            color: var(--main-color-one);
            border-color: inherit;
        }
        .cmn-btn.btn-outline-1:hover {
            background: var(--main-color-one);
            color: #fff;
            border-color: inherit;
        }
        .cmn-btn.btn-outline-2 {
            border: 1px solid var(--main-color-two);
            color: var(--main-color-two);
            border-color: inherit;
        }
        .cmn-btn.btn-outline-2:hover {
            background: var(--main-color-two);
            color: #fff;
            border-color: inherit;
        }
        .cmn-btn.btn-outline-3 {
            border: 1px solid var(--main-color-three);
            color: var(--main-color-three);
            border-color: inherit;
        }
        .cmn-btn.btn-outline-3:hover {
            color: #fff;
            background: var(--main-color-three);
            border-color: inherit;
        }
        .cmn-btn.btn-outline-border {
            border: 1px solid var(--new-body-color);
            color: var(--new-paragraph-color);
        }
        .cmn-btn.btn-outline-border:hover {
            color: var(--white);
            background: var(--main-color-three);
            border-color: var(--main-color-one);
            border-color: inherit;
        }
        .cmn-btn.btn-hover-danger:hover {
            background-color: var(--delete-color);
            border-color: var(--delete-color);
            color: var(--white);
            border-color: inherit;
        }
        .cmn-btn.btn-small {
            padding: 9px 12px;
            font-size: 15px;
        }
        .cmn-btn.btn-medium {
            padding: 11px 25px;
            font-size: 15px;
        }
        .cmn-btn.btn-small-height {
            padding: 3px 35px;
        }
        .cmn-btn.pending {
            background: rgba(255, 179, 7, 0.1);
            color: #FFB307;
            text-align: center;
            border-radius: 5px;
        }
        .cmn-btn.pending:hover {
            background: #FFB307;
            color: #fff;
        }
        .cmn-btn.completed {
            background: rgba(29, 191, 115, 0.1);
            color: #1DBF73;
            text-align: center;
            border-radius: 5px;
        }
        .cmn-btn.completed:hover {
            background: #1DBF73;
            color: #fff;
        }
        .cmn-btn.canceled {
            background: rgba(255, 23, 71, 0.1);
            color: #FF1747;
            text-align: center;
            border-radius: 5px;
        }
        .cmn-btn.canceled:hover {
            background: #FF1747;
            color: #fff;
        }

        .submit-btn {
            font-size: 16px;
            outline: none;
            border: none;
            background: var(--main-color-one);
            color: #fff;
            padding: 10px 30px;
            cursor: pointer;
            -webkit-transition: all 0.3s;
            transition: all 0.3s;
        }
        .submit-btn:focus {
            outline: none;
        }

        .new_exploreBtn {
            font-size: 18px;
            font-weight: 500;
            color: var(--main-color-one);
            -webkit-transition: all 0.3s;
            transition: all 0.3s;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            gap: 7px;
        }
        .new_exploreBtn:hover {
            color: var(--main-color-two);
        }
        @media only screen and (max-width: 480px) {
            .new_exploreBtn {
                font-size: 16px;
            }
        }

        .btn_flex {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            gap: 12px;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
        }



        .new_pricing__single.active_plan .cmn-btn.btn-outline-border {
            background-color: var(--main-color-one);
            border-color: var(--main-color-one);
            color: var(--white);
        }
        .new_pricing__single.active_plan .cmn-btn.btn-outline-border:hover {
            background-color: var(--main-color-two);
            border-color: var(--main-color-two);
        }

        ul.new_pricing__single__list.list_none li{
            color: #FFFFFF;
        }
        ul.new_pricing__single__list.list_none li{
            color: #6e6e6e;
        }

        .wallet_selected_payment_gateway {
            width: 15px;
            height: 15px;
        }

        .wallet-payment-gateway-wrapper {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 6px;
        }

        .new_pricing__single.active_plan {
            background-color: var(--dashboard-bg);
            border-radius: 0 0 10px 10px;
        }


        /* Hive Pricing Css */
        .new_pricing__single {
            border: 1px solid var(--new-border-color);
            border-radius: 10px;
            padding: 20px;
            background-color: var(--white);
            position: relative;
        }
        .new_pricing__single__popular {
            position: absolute;
            top: -20px;
            background: var(--success-color);
            width: 100%;
            left: 0;
            color: var(--white);
            text-align: center;
            border-radius: 10px 10px 0 0;
            padding: 3px;
            font-size: 15px;
            font-weight: 400;
            visibility: hidden;
            opacity: 0;
            -webkit-transition: all 0.2s;
            transition: all 0.2s;
        }
        .new_pricing__single.active_plan {
            background-color: var(--heading-color);
            border-radius: 0 0 10px 10px;
        }
        @media only screen and (max-width: 767.98px) {
            .new_pricing__single.active_plan {
                margin-top: 20px;
            }
        }
        .new_pricing__single.active_plan .new_pricing__single__popular {
            visibility: visible;
            opacity: 1;
        }
        .new_pricing__single.active_plan .cmn-btn.btn-outline-border {
            background-color: var(--main-color-one);
            border-color: var(--main-color-one);
            color: var(--white);
        }
        .new_pricing__single.active_plan .cmn-btn.btn-outline-border:hover {
            background-color: var(--main-color-two);
            border-color: var(--main-color-two);
        }
        .new_pricing__single__header {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            gap: 15px 10px;
        }
        .new_pricing__single__package {
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
            color: var(--new-paragraph-color);
        }
        .new_pricing__single__price {
            font-size: 36px;
            font-weight: 600;
            line-height: 1.2;
            color: var(--main-color-two);
        }
        .new_pricing__single__duration {
            font-size: 20px;
            font-weight: 600;
            line-height: 1.2;
            color: var(--new-heading-color);
            display: block;
            margin-top: 10px;
        }
        .new_pricing__single__list li {
            color: var(--light-color);
            font-size: 16px;
            line-height: 1.3;
            position: relative;
            padding-left: 25px;
        }
        .new_pricing__single__list li:not(:first-child) {
            padding-top: 10px;
        }
        .new_pricing__single__list li:not(:last-child) {
            padding-bottom: 10px;
        }
        .new_pricing__single__list li.list_show {
            color: var(--heading-color);
        }
        .new_pricing__single__list li.list_show::before {
            color: var(--success-color);
            content: "\f00c";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            left: 0;
        }

        .new_pricing__customize {
            display: inline-block;
            font-size: 20px;
            font-weight: 600;
            line-height: 24px;
            color: var(--main-color-two);
            -webkit-transition: all 0.3s;
            transition: all 0.3s;
        }
        .new_pricing__customize:hover {
            color: var(--main-color-one);
        }

        .new_pricing__single.active_plan {
            background-color: #ffffff;
            border-radius: 0 0 10px 10px;
        }



    /*    payment*/

        .payment_getway_image ul {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .payment_getway_image ul li {
            margin-top: 10px;
        }

        .payment_getway_image ul li:not(:last-child) {
            margin-right: 10px;
        }

        .payment_getway_image ul li .img-select {
            max-width: 75px;
        }

        .payment_getway_image ul li .img-select img {
            width: auto;
        }

        .coupon_input_field {}

        .coupon_input_field .result-list {
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .coupon_input_field .result-list .form-control {
            border: 1px solid #cccccc;
            padding-right: 70px;
        }

        .coupon_input_field .result-list .apply-coupon {
            position: absolute;
            right: 0;
            bottom: 0;
            height: 100%;
            padding: 0 10px;
            border: 0;
            outline: none;
            background: var(--main-color-one);
            color: #fff;
        }

        .payment_getway_image ul li {
            position: relative;
            z-index: 1;
            border: 1px solid #fff;
        }

        .payment_getway_image ul li:before {
            content: "\f00c";
            font-family: 'Line Awesome Free';
            font-weight: 900;
            font-size: 12px;
            height: 18px;
            width: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            color: #fff;
            left: 0px;
            top: 0px;
            z-index: 9;
            background: var(--main-color-one);
            transition: all .3s;
            visibility: hidden;
            opacity: 1;
        }

        .payment_getway_image ul li.active::before {
            visibility: visible;
            opacity: 1;
        }

        .payment_getway_image ul li.active {
            border: 1px solid #ddd;
        }

        .subscription-coupon-btn-group {
            display: flex;
        }

        li{
            list-style-type: none;
        }
        button.close {
            border: none;
        }
        select#kineticpay_bank {
            border: 1px solid #dbdbdb;
            height: 38px;
        }

        label {
            display: inline-block;
            color: #000;
        }

        .new_pricing__body_style{
            /*height: 335px;*/
            min-height: 300px;
        }
        @media screen and (max-width: 1399px) {
            .new_pricing__single.active_plan {
                margin-top: 10px;
            }
        }

        /* Responsive styles for smaller screens */
        @media screen and (max-width: 1642px) {
            .new_pricing__single__header.mt-2 {
                display: grid;
            }
        }

    </style>
@endsection
@section('content')
    @include('frontend.user.seller.partials.sidebar-two')
    <div class="dashboard__right">
        @include('frontend.user.buyer.header.buyer-header')
        <div class="dashboard__body">
            <div class="dashboard__inner">
                <x-msg.error/>
                <x-msg.success/>

                <div class="dashboard__inner__item dashboard_border padding-20 radius-10 bg-white">
                <div class="row g-4 mt-1">
                    <div class="col-lg-12">
                        <div class="new_serviceDetails__tab mt-4">
                            <div class="tab_content_item active mt-5" id="tab_package">
                                <div class="row g-4">
                                    @foreach($all_subscription_list as $subscription_list)
                                    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6">
                                            <div class="new_pricing__single active_plan">
                                                <div class="package_image text-center">
                                                    {!! render_image_markup_by_attachment_id($subscription_list->image,' ','thumb') !!}
                                                </div>

                                                <span class="new_pricing__single__popular">{{ $subscription_list->title }}</span>
                                                <div class="new_pricing__single__header mt-2">
                                                    <div class="new_pricing__single__header__left">
                                                        <span class="new_pricing__single__duration">
                                                            @if($subscription_list->type == 'monthly')
                                                                {{ __('MONTHLY')}}
                                                            @elseif($subscription_list->type == 'yearly')
                                                                {{ __('YEARLY')}}
                                                            @elseif($subscription_list->type == 'lifetime')
                                                                {{ __('LIFETIME')}}
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <h3 class="new_pricing__single__price text-center">
                                                        {{ float_amount_with_currency_symbol($subscription_list->price) }}
                                                    </h3>
                                                </div>
                                                <div class="border_top mt-2 new_pricing__body_style">
                                                    <ul class="new_pricing__single__list list_none">
                                                        <li class="list_show">
                                                            <b>
                                                                @if($subscription_list->type == 'monthly')
                                                                    {{ __('Monthly')}}
                                                                @elseif($subscription_list->type == 'yearly')
                                                                    {{ __('Yearly')}}
                                                                @elseif($subscription_list->type == 'lifetime')
                                                                    {{ __('Lifetime')}}
                                                                @endif
                                                            </b>

                                                            @if($subscription_list->type == 'lifetime')
                                                                {{ __('package user will charge only once') }}
                                                            @else
                                                                {{ __('billing cycle, system will deduct this amount from seller account, if seller has balance, otherwise will send an invoice mail to pay the bill') }}
                                                            @endif
                                                        </li>

                                                        <li class="list_show">
                                                            <b>
                                                                @if($subscription_list->type == 'lifetime')
                                                                    {{ __('No limit')  }}
                                                                @else
                                                                    {{ $subscription_list->connect }}
                                                                @endif
                                                            </b>
                                                            @if($subscription_list->type == 'lifetime')
                                                                {{ __('this package will get unlimited number of connect.') }}
                                                            @else
                                                                {{ sprintf(__('Connect to get order from buyer, each order will deduct %d connect from seller account.'),get_static_option('set_number_of_connect',2)) }}
                                                            @endif
                                                        </li>

                                                        <li class="list_show">
                                                            <b>
                                                                @if($subscription_list->type == 'lifetime')
                                                                    {{ __('No limit')  }}
                                                                @endif
                                                            </b>

                                                            @if($subscription_list->type == 'lifetime')
                                                                {{ __('this package will get unlimited number of service.') }}
                                                            @else
                                                                {!! sprintf(__('Seller can create <strong>%s</strong> Services Maximum.'), $subscription_list->service) !!}
                                                            @endif
                                                        </li>

                                                        <li class="list_show">
                                                            <b>
                                                                @if($subscription_list->type == 'lifetime')
                                                                    {{ __('No limit')  }}
                                                                @endif
                                                            </b>

                                                            @if($subscription_list->type == 'lifetime')
                                                                {{ __('this package will get unlimited number of job.') }}
                                                            @else
                                                                {!! sprintf(__('Seller can apply <strong>%s</strong> Jobs Maximum.'), $subscription_list->job) !!}
                                                            @endif

                                                        </li>
                                                    </ul>
                                                </div>
                                                @if($subscription_list->price == 0)
                                                <div class="btn-wrapper mt-4">
                                                    <form action="{{ route('seller.subscription.buy') }}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="subscription_id" class="subscription_id" value="{{$subscription_list->id}}">
                                                        <input type="hidden" name="type" class="type" value="{{$subscription_list->type}}">
                                                        <input type="hidden" name="price" class="price" value="{{$subscription_list->price}}">
                                                        <input type="hidden" name="connect" class="connect" value="{{$subscription_list->connect}}">
                                                        <input type="hidden" name="service" class="service" value="{{$subscription_list->service}}">
                                                        <input type="hidden" name="job" class="job" value="{{$subscription_list->job}}">
                                                         <button type="submit" class="cmn-btn btn-outline-border w-100 radius-10">{{ __('Buy Now') }}</button>
                                                    </form>
                                                </div>
                                                @else
                                                    <div class="btn-wrapper mt-4">
                                                        <a href="#"
                                                           class="cmn-btn btn-outline-border w-100 radius-10 get_subscription_id"
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#buySubscriptionModal"
                                                           data-id="{{$subscription_list->id}}"
                                                           data-type="{{$subscription_list->type}}"
                                                           data-price="{{$subscription_list->price}}"
                                                           data-connect="{{$subscription_list->connect}}"
                                                           data-service="{{$subscription_list->service}}"
                                                           data-job="{{$subscription_list->job}}"
                                                        >{{ __('Buy Now') }}</a>
                                                    </div>
                                                @endif
                                            </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>
                </div>

                @if($subscription)
                    <div class="dashboard__inner__item dashboard_border padding-20 radius-10 bg-white">
                        <div class="dashboard__wallet">
                            <div class="dashboard__wallet__flex">
                                <div class="dashboard__wallet__balance">
                                    <div class="dashboard__wallet__balance__contents">
                                        <p class="chat_wrapper__details__inner__chat__contents__para">
                                            {{ __('Note: Pending connect will be added to available connect only the payment status is completed.') }},
                                            {{ get_static_option('set_number_of_connect') }}
                                            {{ __('Connect will reduce for each order from available connects') }}
                                        </p>

                                        <!--renew button -->
                                        <div class="btn-wrapper mt-4 w-25">
                                            @if(!empty($subscription))
                                                @php
                                                    $today = now();
                                                    $expireDate = \Carbon\Carbon::parse($subscription->expire_date);
                                                    $daysUntilExpiration = $today->diffInDays($expireDate);
                                                    $renew_expire_days = get_static_option('renew_button_before_expire_days') ?? 7;
                                                @endphp
                                                  <!-- If not expired, payment status is complete, and within 7 days, show the renewal button -->
                                                @if($subscription->payment_status == 'complete' && $expireDate >= $today &&
                                                        ($daysUntilExpiration <= $renew_expire_days ||
                                                            $subscription->connect == 0 ||
                                                            $subscription->service == 0 ||
                                                            $subscription->job == 0))
                                                    <a href="#"
                                                       class="btn btn-warning w-100 radius-10 get_renew_subscription_id"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#renewSubscriptionModal"
                                                       data-renew_id="{{$subscription->subscription_id}}"
                                                       data-renew_type="{{$subscription->type}}"
                                                       data-renew_price="{{$subscription->price}}"
                                                       data-renew_connect="{{$subscription->connect}}"
                                                       data-renew_service="{{$subscription->service}}"
                                                       data-renew_job="{{$subscription->job}}"
                                                    >{{ __('Renew Current Subscription Before Expired') }}</a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!--seller subscription basic info start-->
                @if($subscription)
                    <div class="dashboard_table__wrapper dashboard_border padding-20 radius-10 bg-white">
                        <h4 class="dashboard_table__title">{{ __('Current Subscription Details') }}</h4>
                        <div class="dashboard_table__main custom--table mt-4">
                            <table>
                                <thead>
                                <tr>
                                    <th> {{ __('Subscription Details') }} </th>
                                    <th> {{ __('Available Details') }} </th>
                                    <th> {{ __('Payment Gateway') }} </th>
                                    <th> {{ __('Payment Status') }} </th>
                                    <th> {{ __('Expire Date') }} </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <div>
                                            <span> {{ __('Title:') }}   <strong class="text-secondary"> {{ optional($subscription->subscription)->title }} </strong> </span> <br>
                                            <span> {{ __('Type:') }}  <strong class="text-secondary">  {{ optional($subscription->subscription)->type }}  </strong></span> <br>
                                            <span> {{ __('Connect:') }}   <strong class="text-secondary"> {{ optional($subscription->subscription)->connect }} </strong> </span> <br>
                                            <span> {{ __('Service:') }}  <strong class="text-secondary">   {{ optional($subscription->subscription)->service }}  </strong></span> <br>
                                            <span> {{ __('Job:') }}  <strong class="text-secondary">  {{ optional($subscription->subscription)->job }}  </strong></span> <br>
                                            <span> {{ __('Price:') }} <strong class="text-secondary">  {{ float_amount_with_currency_symbol(optional($subscription->subscription)->price) }} </strong> </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                             <span class="mt-2">
                                                 @if($subscription->payment_status == 'pending' || $subscription->payment_status == '')
                                                     @if($subscription->payment_status == 'pending' || $subscription->payment_status == '')
                                                         {{ __('Pending Connect:') }} {{ $subscription->initial_connect }} <br>
                                                         {{ __('Service Connect:') }} {{ $subscription->initial_service }} <br>
                                                         {{ __('Job Connect:') }} {{ $subscription->initial_job }} <br>
                                                     @endif
                                                 @else
                                                     @if($subscription->type == 'lifetime')
                                                         {{ __('Connect:') }}  {{ __('No Limit') }} <br>
                                                         {{ __('Service:') }}  {{ __('No Limit') }} <br>
                                                         {{ __('Job:') }}  {{ __('No Limit') }} <br>
                                                     @else
                                                         {{ __('Connect:') }}  {{$subscription->connect}} <br>
                                                         {{ __('Service:') }}  {{$subscription->service}} <br>
                                                         {{ __('Job:') }}  {{$subscription->job}} <br>
                                                     @endif
                                                 @endif

                                             </span>

                                        </div>
                                    </td>
                                    <td>
                                        <div class="dashboard_table__main__paymentGateway">
                                            {{ ucfirst($subscription->payment_gateway) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dashboard_table__main__priority">
                                            <span class="service-review">
                                            <a href="javascript:void(0)" class="priorityBtn @if($subscription->payment_status=='complete') @else pending @endif completed">
                                                {{ ucfirst($subscription->payment_status=='complete' ? 'complete' : 'pending') }}</a>

                                             </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dashboard_table__main__date">
                                            @if($subscription->type == 'lifetime')
                                                <span class="service-review">{{ __('Expire Date:') }}  {{ __('No Limit') }}</span>
                                            @else
                                                <span class="service-review"> {{ __('Expire Date:') }}  {{date('d-m-Y', strtotime($subscription->expire_date))}}  </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--seller subscription basic info end-->
                @else
                    <div class="chat_wrapper__details__inner__chat__contents">
                        <h2 class="btn btn-info">
                            {{ __('No Subscription Found') }}
                        </h2>
                    </div>
                @endif

                <!--seller subscription history  start-->
                @if($subscription_history)
                    <div class="dashboard_table__wrapper dashboard_border padding-20 radius-10 bg-white mt-4">
                        <h5 class="dashboards-title">{{ __('Subscription History') }}</h5>
                        <p class="text-info mt-2">{{ __('Your earlier subscription history list.') }}</p>
                        <div class="dashboard_table__main custom--table mt-4">
                            <table>
                                <thead>
                                <tr>
                                    <th> {{ __('#No') }} </th>
                                    <th> {{ __('Subscription Details') }} </th>
                                    <th> {{ __('Payment Details') }} </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($subscription_history as $data)
                                    <tr>
                                        <td>
                                            <div class="dashboard_table__main__paymentId">{{ $data->id }} </div>
                                        </td>
                                        <td>
                                            <div class="">
                                                {{ __('Title:') }} {{optional($data->subscription)->title}} <br>
                                                {{ __('Price:') }}
                                                @if($data->price == 0)
                                                    {{float_amount_with_currency_symbol($data->initial_price)}} <br>
                                                @else
                                                    {{float_amount_with_currency_symbol($data->price)}} <br>
                                                @endif
                                                {{ __('Type:') }} {{ucfirst($data->type)}} <br>
                                                @if($data->type != 'lifetime')
                                                    {{ __('Connect:') }}
                                                    @if($data->connect == 0)
                                                        {{$data->initial_connect}} <br>
                                                    @else
                                                        {{$data->connect}} <br>
                                                    @endif

                                                    {{ __('Service:') }}
                                                    @if($data->service == 0)
                                                        {{ $data->initial_service }} <br>
                                                    @else
                                                        {{ $data->service }} <br>
                                                    @endif

                                                    {{ __('Job:') }}
                                                    @if($data->job == 0)
                                                        {{ $data->initial_job }} <br>
                                                    @else
                                                        {{ $data->job }} <br>
                                                    @endif

                                                    {{ __('Expire Date:') }} {{date('d-m-Y', strtotime($data->expire_date))}}<br>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dashboard_table__main__priority">
                                                {{ __('Payment Gateway:') }} {{ ucfirst($data->payment_gateway) }} <br>
                                                {{ __('Payment Status:') }}
                                                <a href="javascript:void(0)"
                                                   class="priorityBtn 
                                                   @if($data->payment_status == 'complete') completed 
                                                   @elseif($data->payment_status == 'cancelled') cancelled 
                                                   @else pending 
                                                   @endif">
                                                    {{ ucfirst($data->payment_status ?: 'pending') }}
                                                </a>

                                                <br>
                                                @if($data->payment_status == 'pending' || $data->payment_status == '')
{{--                                                    @if($data->payment_gateway != 'manual_payment')--}}
                                                        @if(\Carbon\Carbon::parse($data->expire_date)->isFuture())
                                                            <a href="#"
                                                               class="mt-2 dashboard_table__title__btn btn-bg-1 radius-5 get_subscription_id"
                                                               data-bs-toggle="modal"
                                                               data-bs-target="#buySubscriptionModal"
                                                               data-history_id="{{$data->id}}"
                                                               data-id="{{$data->subscription_id}}"
                                                               data-type="{{$data->type}}"
                                                               data-price="{{$data->price}}"
                                                               data-connect="{{$data->connect}}"
                                                               data-service="{{$data->service}}"
                                                               data-job="{{$data->job}}"
                                                            >{{ __('Pay Now') }}</a>

                                                            <a href="javascript:void(0);"
                                                               class="mt-2 dashboard_table__title__btn btn-bg-1 radius-5 cancel_subscription_btn"
                                                               data-history_id="{{$data->id}}"
                                                            >{{ __('Cancel') }}</a>
                                                        @endif
{{--                                                    @endif--}}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="blog-pagination margin-top-55">
                            <div class="custom-pagination mt-4 mt-lg-5">
                                {!! $subscription_history->links() !!}
                            </div>
                        </div>
                    </div>
                @endif
                <!--seller subscription history  end-->
            </div>
        </div>


 <!-- Re-new price plan  Modal -->
<div class="modal fade" id="renewSubscriptionModal" tabindex="-1" role="dialog" aria-labelledby="couponModal" aria-hidden="true">
    <form id="msform" class="ms-order-form" action="{{route('seller.subscription.renew')}}" method="post"  enctype="multipart/form-data">
       @csrf
        <input type="hidden" name="renew_subscription_id" class="renew_subscription_id" value="">
        <input type="hidden" name="renew_type" class="renew_type" value="">
        <input type="hidden" name="renew_price" class="renew_price" value="">
        <input type="hidden" name="renew_connect" class="renew_connect" value="">
        <input type="hidden" name="renew_service" class="renew_service" value="">
        <input type="hidden" name="renew_job" class="renew_job" value="">

        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-warning" id="couponModal">{{ get_static_option('seller_renew_subscription_modal_title') ?? __('You must pay first to renew subscription')}}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="confirm-bottom-content">
                        <!-- wallet payment start -->
                        @if(moduleExists('Wallet'))
                            {!! \App\Helpers\PaymentGatewayRenderHelper::renderWalletForm() !!}
                        @endif
                        <!-- wallet payment end -->
                        <div class="confirm-payment payment-border">
                            <div class="single-checkbox">
                                <div class="checkbox-inlines">
                                    <label class="checkbox-label" for="check2">
                                        {!! \App\Helpers\PaymentGatewayRenderHelper::renderPaymentGatewayForForm(false, 'old'); !!}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="order cart-total">
                                <div class="form-group">
                                    <input type="hidden" value="" id="subscription_price">
                                    <p class="display_error_msg"></p>
                                    <p class="display_coupon_amount"></p>
                                    <div class="subscription-coupon-btn-group">
                                        <input type="text" name="apply_coupon_code" id="apply_coupon_code" class="form-control mt-2" style="line-height: 3.15" placeholder="{{__('Enter Coupon Code')}}">
                                        <button type="button" class="btn btn-success coupon_apply_btn mx-3 my-2">{{ __('Apply') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary order_create_from_jobs">{{__('Renew')}}</button>
                </div>
            </div>
        </div>
    </form>
</div>

 <!-- new buy price plan  Modal -->
<div class="modal fade" id="buySubscriptionModal" tabindex="-1" role="dialog" aria-labelledby="couponModal" aria-hidden="true">
    <form id="msform" class="ms-order-form" action="{{route('seller.subscription.buy')}}" method="post"  enctype="multipart/form-data">
       @csrf
        <input type="hidden" name="history_id" class="history_id" value="">
        <input type="hidden" name="subscription_id" class="subscription_id" value="">
        <input type="hidden" name="type" class="type" value="">
        <input type="hidden" name="price" class="price" value="">
        <input type="hidden" name="connect" class="connect" value="">
        <input type="hidden" name="service" class="service" value="">
        <input type="hidden" name="job" class="job" value="">

        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-warning" id="couponModal">{{ get_static_option('seller_buy_subscription_modal_title') ?? __('You must pay first to buy a subscription')}}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="confirm-bottom-content">
                        <!-- wallet payment start -->
                        @if(moduleExists('Wallet'))
                            {!! \App\Helpers\PaymentGatewayRenderHelper::renderWalletForm() !!}
                        @endif
                        <!-- wallet payment end -->
                        <div class="confirm-payment payment-border">
                            <div class="single-checkbox">
                                <div class="checkbox-inlines">
                                    <label class="checkbox-label" for="check2">
                                        {!! \App\Helpers\PaymentGatewayRenderHelper::renderPaymentGatewayForForm(false, 'old'); !!}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="order cart-total">
                                <div class="form-group">
                                    <input type="hidden" value="" id="subscription_price">
                                    <p class="display_error_msg"></p>
                                    <p class="display_coupon_amount"></p>
                                    <div class="subscription-coupon-btn-group">
                                        <input type="text" name="apply_coupon_code" id="apply_coupon_code" class="form-control mt-2" style="line-height: 3.15" placeholder="{{__('Enter Coupon Code')}}">
                                        <button type="button" class="btn btn-success coupon_apply_btn mx-3 my-2">{{ __('Apply') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary order_create_from_jobs">{{__('Buy Now')}}</button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
@section('scripts')
    <script src="{{asset('assets/backend/js/sweetalert2.js')}}"></script>
   <x-payment-gateway-js/>
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){

                // Re-new Price plan
                $(document).on('click', '.get_renew_subscription_id',function(){
                    let get_renew_subscription_id = $(this).data('renew_id');
                    let renew_type = $(this).data('renew_type');
                    let renew_price = $(this).data('renew_price');
                    let renew_connect = $(this).data('renew_connect');
                    let renew_service = $(this).data('renew_service');
                    let renew_job = $(this).data('renew_job');

                    $('.renew_subscription_id').val(get_renew_subscription_id)
                    $('.renew_type').val(renew_type)
                    $('.renew_price').val(renew_price)
                    $('.renew_connect').val(renew_connect)
                    $('.renew_service').val(renew_service)
                    $('.renew_job').val(renew_job)
                    $('#renew_subscription_price').val(renew_price)
                });

                // Buy now price plan
                $(document).on('click', '.get_subscription_id',function(){
                    let history_id = $(this).data('history_id');
                    let get_subscription_id = $(this).data('id');
                    let type = $(this).data('type');
                    let price = $(this).data('price');
                    let connect = $(this).data('connect');
                    let service = $(this).data('service');
                    let job = $(this).data('job');

                    $('.history_id').val(history_id)
                    $('.subscription_id').val(get_subscription_id)
                    $('.type').val(type)
                    $('.price').val(price)
                    $('.connect').val(connect)
                    $('.service').val(service)
                    $('.job').val(job)
                    $('#subscription_price').val(price)
                });

                $(document).on('click', '.cancel_subscription_btn', function () {
                    let historyId = $(this).data('history_id');

                    Swal.fire({
                        icon: 'warning',
                        title: 'Are you sure?',
                        text: 'This will cancel the subscription payment.',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, cancel it!',
                        cancelButtonText: 'No, keep it'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route("seller.subscription.history.cancel") }}',
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    history_id: historyId
                                },
                                success: function (response) {
                                    Swal.fire('Cancelled!', response.msg, 'success').then(() => {
                                        location.reload();
                                    });
                                },
                                error: function (xhr) {
                                    let msg = xhr.responseJSON?.msg || 'Something went wrong.';
                                    Swal.fire('Error!', msg, 'error');
                                }
                            });
                        }
                    });
                });

            //coupon apply
            $(document).on('click','.coupon_apply_btn',function(e){
                e.preventDefault();
                let subscription_price = $('#subscription_price').val();
                let apply_coupon_code = $('#apply_coupon_code').val();

                $.ajax({
                    url: "{{ route('seller.subscription.coupon.apply') }}",
                    method:"POST",
                    data:{subscription_price:subscription_price,apply_coupon_code:apply_coupon_code},
                    success:function(res){
                        if(res.message != ''){
                            $('.display_error_msg').html('<p class="text-danger">'+res.message+'</p>');
                            $('.display_coupon_amount').html('');
                        }
                        if(res.discount >= 1){
                            $('.display_coupon_amount').html('<p class="text-success">Discounted Amount: ' +res.discount+'</p>');
                            $('.display_error_msg').html('');
                        }
                    }
                });
            });

            // date range
            $('.flatpickr_input').flatpickr({
                altFormat: "invisible",
                altInput: false,
                mode: "range",
            });

            });
        })(jQuery);
    </script>
@endsection