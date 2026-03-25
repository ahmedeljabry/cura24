@extends('backend.admin-master')
@section('site-title')
    WhatsApp Button Text Settings
@endsection
@section('style')
    <style>
      textarea{
          width: 300px;
          height:150px;
      }
      img.button_text_setting{
          width: 300px;
          height:460px;
      }
      .card-body{
            overflow-x: unset !important;
      }
      .img-preview{
            top: 0;
      }

    </style>
@section('content')
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4>Set WhatsApp Button Text</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('admin.whatsapp.button-text.setting.update') }}" method="POST">
                    @csrf
                    @php
                        $textEvents = [
                            'service_search' => 'Search Service',
                            'view_recent_orders' => 'View Recent Orders',
                            'talk_to_support' => 'Talk to Support',
                            'select_service' => 'Select Service',
                            'included_excluded' => 'Included/Excluded',
                            'show_faqs' => 'Show FAQs',
                             'show_faqs-benefits' => 'Show FAQs-Benefits',
                            'order_now' => 'Order Now',
                            'select_addons' => 'Select Add-ons',
                            'select_addons_quantity' => 'Select Add-ons Quantity',
                            'select_includes' => 'Select Include',
                            'select_include_quantity' => 'Select Include Quantity',
                            'select_location' => 'Select Location',
                            'select_slot' => 'Select Slot',
                            'order_addon_details' => 'Order Addon Details',
                            'order_other_details' => 'Order Other Details',
                            'confirm_order' => 'Confirm Order',
                            'cancel_order' => 'Cancel Order',
                            'agree_to_cancel_order' => 'Agree to Cancel Order',
                            'disagree_to_cancel_order' => 'Disagree to Cancel Order',

                        ];
                    @endphp
                    <div class="row">
                        <div class="col-9">
                            <div class="nav nav-tabs flex-column" role="tablist">
                                @foreach($textEvents as $key => $label)
                                    <div class="nav-item {{ $loop->first ? 'active' : '' }}" data-toggle="tab" data-target="#a{{ $key }}" aria-controls="a{{ $key }}" role="tab">
                                        <div class="form-group mt-4">
                                            <label for="message_{{ $key }}">{{__("WhatsApp Button Text for {$label}")}}</label>
                                            <textarea name="messages[{{ $key }}]" id="message_{{ $key }}" class="form-control mt-3" rows="10" placeholder="Write the message for {{ $label }}">{{ old("messages.$key", $messages[$key] ?? '') }}</textarea>
                                        </div>
                                        @error("messages.$key")
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="position-sticky img-preview">
                                <div class="tab-content">
                                    <h5>{{__('Preview Image')}}</h5>
                                    @foreach($textEvents as $key => $label)
                                        <div class="tab-pane fade form-group mt-2 {{ $loop->first ? 'active show' : '' }} text-center"  id="a{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}id">
                                            <label class="d-block font-weight-bold">{{__('Example Preview')}}</label>
                                            <img src="{{ asset("assets/backend/img/whatsapp-preview/{$key}.png") }}" alt="Preview for {{ $label }}" class="img-fluid border rounded shadow-sm mb-2 button_text_setting">
                                            <p class="text-muted small">{{__('This is how it will appear in WhatsApp')}}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-2">Save All Texts</button>
                </form>
            </div>
        </div>
    </div>
@endsection

