@extends('backend.admin-master')
@section('site-title')
    {{__('Add New Order')}}
@endsection

@section('style')
    <x-datatable.css/>
    <x-media.css/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" />
    <style>
        .select2-container .select2-search__field {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .services-container {
            margin-top: 20px;
        }
        .includes-list {
            list-style-type: disc;
            margin-left: 20px;
        }
        .no-data-message {
            color: #6c757d;
            font-style: italic;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity-btn {
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
            user-select: none;
        }
        .quantity-display {
            width: 50px;
            text-align: center;
        }
        .subtotal {
            margin-left: 20px;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-msg.success/>
                <x-msg.error/>
            </div>
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('service.create.order') }}" method="post">
                            @csrf
                            <input type="hidden" name="selected_payment_gateway" value="cash_on_delivery">
                            <input type="hidden" name="time" value="on">
                            <input type="hidden" name="paymentRadio" value="on">
                            <input type="hidden" name="kineticpay_bank" value="">
                            <input type="hidden" name="coupon_code" value="">
                            <input type="hidden" name="is_service_online" id="is_service_online">
                            <input type="hidden" name="seller_id" id="seller_id">
                            <input type="hidden" name="package_fee_input_hiddend_field_for_js_calculation" id="package_fee">
                            <input type="hidden" name="service_subtotal_input_hidden_field_for_js_calculation" id="service_subtotal">
                            <input type="hidden" name="request_source" value="admin">
                            <div class="col-lg-3 col-sm-6 mb-3">
                                <div class="single-category-service flex-category-service">
                                    <div class="single-select">
                                        <label for="buyer_id">{{ __('Select Registered Buyer') }}</label>
                                        <select name="buyer_id" id="buyer_id" class="form-control select2" required>
                                            <option value="">{{ __('Select a Buyer') }}</option>
                                            @foreach ($buyers as $user)
                                                <option value="{{ $user->id }}" 
                                                        data-name="{{ $user->name }}" 
                                                        data-email="{{ $user->email }}" 
                                                        data-phone="{{ $user->phone }}" 
                                                        data-address="{{ $user->address }}">{{ $user->name }} ({{ $user->email }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 mb-3">
                                <div class="single-category-service flex-category-service">
                                    <div class="single-select">
                                        <label for="service_id">{{ __('Select Service') }}</label>
                                        <select name="service_id" id="service_id" class="form-control select2" required>
                                            <option value="">{{ __('Select a Service') }}</option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}" 
                                                        data-seller-id="{{ $service->seller_id }}"
                                                        data-is-service-online="{{ $service->is_service_online }}">{{ $service->title }} ({{ $service->seller->name ?? 'N/A' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group services-container" id="service-includes-container" style="display: none;">
                                <label>{{ __('Included Services') }}</label>
                                <ul id="service-includes" class="includes-list"></ul>
                            </div>
                            <div class="form-group services-container" id="service-additionals-container" style="display: none;">
                                <label>{{ __('Additional Services') }}</label>
                                <div id="service-additionals"></div>
                            </div>
                            <div class="col-lg-3 col-sm-6 mb-3">
                                <div class="single-category-service flex-category-service">
                                    <div class="single-select">
                                        <label for="order_date">{{ __('Select Date') }}</label>
                                        <input type="text" name="date" id="order_date" class="form-control" autocomplete="off" required>
                                        <input type="hidden" name="service_available_dates" id="service_available_dates">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 mb-3">
                                <div class="single-category-service flex-category-service">
                                    <div class="single-select">
                                        <label for="schedule_id">{{ __('Select Schedule') }}</label>
                                        <select name="schedule" id="schedule_id" class="form-control select2" required>
                                            <option value="">{{ __('Select a Schedule') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 mb-3">
                                <div class="single-category-service flex-category-service">
                                    <div class="single-select">
                                        <label for="name">{{ __('Name') }}</label>
                                        <input type="text" name="name" id="name" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 mb-3">
                                <div class="single-category-service flex-category-service">
                                    <div class="single-select">
                                        <label for="email">{{ __('Email') }}</label>
                                        <input type="email" name="email" id="email" class="form-control" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 mb-3">
                                <div class="single-category-service flex-category-service">
                                    <div class="single-select">
                                        <label for="phone">{{ __('Phone') }}</label>
                                        <input type="text" name="phone" id="phone" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 mb-3">
                                <div class="single-category-service flex-category-service">
                                    <div class="single-select">
                                        <label for="address">{{ __('Address') }}</label>
                                        <input type="text" name="address" id="address" class="form-control" required>
                                        <input type="hidden" name="user_address" id="user_address">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="order_note">{{ __('Order Note') }}</label>
                                <textarea name="order_note" id="order_note" class="form-control" placeholder="{{ __('Enter order notes') }}"></textarea>
                            </div>
                            
                             <div class="form-group">
                                <label for="total_price">{{ __('Total Price') }}</label>
                                <input type="text" id="total_price" class="form-control" readonly value="0.00">
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('Create Order') }}</button>
                            <a href="{{ route('admin.orders') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <!-- Toastr CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <x-datatable.js/>
    <x-media.js/>
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                // Initialize Toastr options
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000
                };

                // Check for flashed success message
                @if (session('success'))
                    toastr.success("{{ session('success') }}");
                @endif

                $('.select2').select2({
                    placeholder: function() {
                        return $(this).data('placeholder') || "{{ __('Select an option') }}";
                    },
                    minimumInputLength: 0,
                    allowClear: true,
                    width: '100%',
                    templateResult: formatOption,
                    templateSelection: formatOptionSelection
                });

                // Initialize datepicker
                $('#order_date').datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: 0,
                    onSelect: function() {
                        $('#service_available_dates').val($(this).val());
                        fetchSchedules();
                    }
                });

                // Format search results
                function formatOption(option) {
                    if (!option.id) {
                        return option.text;
                    }
                    return $('<span>' + option.text + '</span>');
                }

                // Format selected option
                function formatOptionSelection(option) {
                    return option.text || "{{ __('Select an option') }}";
                }

                // Function to update fees and add service/additional inputs
                function updateFees() {
                    let packageFee = 0;
                    let serviceSubtotal = 0;
                    let hasAdditionals = false;

                    // Clear existing service/additional inputs
                    $('input[name^="services"]').remove();
                    $('input[name^="additionals"]').remove();

                    // Handle includes
                    $('#service-includes li').each(function(index) {
                        const $li = $(this);
                        const quantity = parseInt($li.find('.quantity-input').val()) || 0;
                        const price = parseFloat($li.data('price')) || 0;
                        const id = $li.data('id');
                        const subtotal = quantity * price;
                        $li.find('.subtotal').text(`Subtotal: $${subtotal.toFixed(2)}`);
                        packageFee += subtotal;
                        serviceSubtotal += subtotal;

                        if (quantity > 0) {
                            $('<input>').attr({
                                type: 'hidden',
                                name: `services[${index}][id]`,
                                value: id
                            }).appendTo('form');
                            $('<input>').attr({
                                type: 'hidden',
                                name: `services[${index}][quantity]`,
                                value: quantity
                            }).appendTo('form');
                        }
                    });

                    // Handle additionals
                    $('#service-additionals .form-check').each(function(index) {
                        const $div = $(this);
                        const $checkbox = $div.find('.form-check-input');
                        const isChecked = $checkbox.is(':checked');
                        const quantity = parseInt($div.find('.quantity-input').val()) || 0;
                        const price = parseFloat($div.data('price')) || 0;
                        const id = $checkbox.val();
                        const subtotal = quantity * price;
                        $div.find('.subtotal').text(`Subtotal: $${subtotal.toFixed(2)}`);

                        if (isChecked && quantity > 0) {
                            hasAdditionals = true;
                            serviceSubtotal += subtotal;
                            $('<input>').attr({
                                type: 'hidden',
                                name: `additionals[${index}][id]`,
                                value: id
                            }).appendTo('form');
                            $('<input>').attr({
                                type: 'hidden',
                                name: `additionals[${index}][quantity]`,
                                value: quantity
                            }).appendTo('form');
                        }
                    });
                    
                    if (!hasAdditionals) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'additionals[0]',
                            value: ''
                        }).appendTo('form');
                    }

                    $('#package_fee').val(packageFee.toFixed(2));
                    $('#service_subtotal').val(serviceSubtotal.toFixed(2));
                    $('#total_price').val(serviceSubtotal.toFixed(2));
                }

                // Function to fetch schedules
                function fetchSchedules() {
                    const serviceId = $('#service_id').val();
                    const dateString = $('#order_date').val();
                    const $scheduleSelect = $('#schedule_id');
                    const sellerId = $('#service_id option:selected').data('seller-id');

                    if (serviceId && dateString && sellerId) {
                        $.ajax({
                            url: "{{ route('service.schedule.by.day') }}",
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                date_string: dateString,
                                seller_id: sellerId
                            },
                            success: function(response) {

                                $scheduleSelect.empty().append('<option value="">{{ __("Select a Schedule") }}</option>');
                                if (response.status === 'success' && response.schedules && response.schedules.length > 0) {
                                    const dayName = response.day.day || '';
                                    $.each(response.schedules, function(index, schedule) {
                                        $scheduleSelect.append(`
                                            <option value="${schedule.schedule}">${dayName}: ${schedule.schedule}</option>
                                        `);
                                    });
                                } else {
                                    $scheduleSelect.append('<option value="" disabled>{{ __("No schedules available") }}</option>');
                                }
                                $scheduleSelect.trigger('change.select2');
                            },
                            error: function(xhr, status, error) {
                                console.error('Schedule AJAX Error:', status, error, xhr.responseText);
                                $scheduleSelect.empty().append('<option value="" disabled>{{ __("Error loading schedules") }}</option>').trigger('change.select2');
                            }
                        });
                    } else {
                        $scheduleSelect.empty().append('<option value="">{{ __("Select a Schedule") }}</option>').trigger('change.select2');
                    }
                }

                // Update buyer info
                $('#buyer_id').on('change', function() {
                    const $selected = $(this).find('option:selected');
                    $('#name').val($selected.data('name') || '');
                    $('#email').val($selected.data('email') || '');
                    $('#phone').val($selected.data('phone') || '');
                    $('#address').val($selected.data('address') || '');
                    $('#user_address').val($selected.data('address') || '');
                });

                // Fetch service includes and additionals
                $('#service_id').on('change', function() {
                    const serviceId = $(this).val();
                    const $includesContainer = $('#service-includes-container');
                    const $includes = $('#service-includes');
                    const $additionalsContainer = $('#service-additionals-container');
                    const $additionals = $('#service-additionals');
                    const $selected = $(this).find('option:selected');
                    const sellerId = $selected.data('seller-id');
                    const isServiceOnline = $selected.data('is-service-online');

                    // Reset schedules and date
                    $('#order_date').val('');
                    $('#service_available_dates').val('');
                    $('#schedule_id').empty().append('<option value="">{{ __("Select a Schedule") }}</option>').trigger('change.select2');
                    $('#seller_id').val(sellerId || '');
                    $('#is_service_online').val(isServiceOnline || '');

                    if (serviceId) {
                        $.ajax({
                            url: "{{ route('admin.get.service.details') }}",
                            method: 'GET',
                            data: { service_id: serviceId },
                            success: function(response) {


                                // Handle includes
                                $includes.empty();
                                if (response.includes && response.includes.length > 0) {
                                    $.each(response.includes, function(index, include) {
                                        const price = include.include_service_price || 0;
                                        $includes.append(`
                                            <li data-price="${price}" data-id="${include.id}">
                                                ${include.include_service_title} ($${price.toFixed(2)})
                                                <div class="quantity-control">
                                                    <span class="quantity-btn decrease">-</span>
                                                    <input type="number" class="quantity-input" data-id="${include.id}" value="0" min="0" readonly>
                                                    <span class="quantity-btn increase">+</span>
                                                </div>
                                                <span class="subtotal">Subtotal: $0.00</span>
                                            </li>
                                        `);
                                    });
                                    $includesContainer.show();
                                } else {
                                    $includes.append('<li class="no-data-message">{{ __('No included services available') }}</li>');
                                    $includesContainer.show();
                                }

                                // Handle additionals
                                $additionals.empty();
                                if (response.additionals && response.additionals.length > 0) {
                                    $.each(response.additionals, function(index, additional) {
                                        const price = additional.additional_service_price || 0;
                                        $additionals.append(`
                                            <div class="form-check" data-price="${price}">
                                                <input type="checkbox" name="service_additional_ids[]" value="${additional.id}" class="form-check-input" id="additional_${additional.id}">
                                                <label class="form-check-label" for="additional_${additional.id}">
                                                    ${additional.additional_service_title} ($${price.toFixed(2)})
                                                </label>
                                                <div class="quantity-control">
                                                    <span class="quantity-btn decrease">-</span>
                                                    <input type="number" class="quantity-input" data-id="${additional.id}" value="0" min="0" readonly>
                                                    <span class="quantity-btn increase">+</span>
                                                </div>
                                                <span class="subtotal">Subtotal: $0.00</span>
                                            </div>
                                        `);
                                    });
                                    $additionalsContainer.show();
                                } else {
                                    $additionals.append('<div class="no-data-message">{{ __('No additional services available') }}</div>');
                                    $additionalsContainer.show();
                                }

                                // Attach event listeners for quantity buttons and checkboxes
                                $('.quantity-btn.increase').on('click', function() {
                                    const $input = $(this).siblings('.quantity-input');
                                    let quantity = parseInt($input.val()) || 0;
                                    $input.val(++quantity);
                                    updateFees();
                                });

                                $('.quantity-btn.decrease').on('click', function() {
                                    const $input = $(this).siblings('.quantity-input');
                                    let quantity = parseInt($input.val()) || 0;
                                    if (quantity > 0) {
                                        $input.val(--quantity);
                                        updateFees();
                                    }
                                });

                                $('.form-check-input').on('change', function() {
                                    updateFees();
                                });

                                // Initial fee calculation
                                updateFees();
                            },
                            error: function(xhr, status, error) {
                                console.error('Service Details AJAX Error:', status, error, xhr.responseText);
                                $includes.empty().append('<li class="no-data-message">{{ __('Error loading included services') }}</li>');
                                $additionals.empty().append('<div class="no-data-message">{{ __('Error loading additional services') }}</div>');
                                $includesContainer.show();
                                $additionalsContainer.show();
                            }
                        });
                    } else {
                        $includesContainer.hide();
                        $additionalsContainer.hide();
                        $('#package_fee').val('0.00');
                        $('#service_subtotal').val('0.00');
                        $('#total_price').val('0.00');
                    }
                });

                // Form submission handler
                $('form').on('submit', function(e) {
                    updateFees();
                });
            });
        })(jQuery.noConflict());
    </script>
@endsection