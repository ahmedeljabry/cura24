@extends('backend.admin-master')
@section('site-title')
WhatsApp Message Template Guide
@endsection

@section('content')
    <div class="container mt-4">
        <div class="card shadow rounded-4 border-0 p-2">
            <div class="card-header rounded-top-4">
                <h4 class="mb-0">{{__('WhatsApp Message Template Guide')}}</h4>
            </div>
            <div class="card-body">
                <p>{{__('If you want to message users outside the 24-hour window, you’ll need to use approved message templates by Meta.')}}</p>

                <hr>
                <h5 class="mb-2">{{__('Template Name :')}}</h5>
                <p>{{__('welcome__template')}}</p>

                <h5 class=" mt-2 mb-3">{{__('Template Preview')}}</h5>
                <div class="border p-3 bg-light rounded">
                    <p>{{__('Hi,welcome back! Would you like to book another service with us?')}}</p>
                    <p><strong>{{__('Buttons:')}}</strong></p>
                    <ul class="mb-0">
                        <li><strong>{{__('Yes')}}</strong></li>
                        <li><strong>{{__('No')}}</strong></li>
                    </ul>
                </div>
                <div class="mt-3">
                    <img src="{{ asset("assets/backend/img/whatsapp-preview/template_preview.png") }}" alt="WhatsApp Template Preview" class="img-fluid rounded shadow-sm border" style="max-width: 400px;">
                </div>

                <hr>

                <h5 class="">{{__('How to Create This Template')}}</h5>
                <ol class="p-3">
                    <li ><p>{{__('Go to ')}}<a href="https://business.facebook.com/wa/manage/message-templates" target="_blank"><strong>{{__("Meta's WhatsApp Message Template Manager")}}</strong></a>.</p></li>
                    <li class="mt-1"><p>{{__('Click ')}}<strong>{{__('Create Template')}}</strong>.</p></li>
                    <li class="mt-1"><p>{{__('Choose a category: ')}}{{__('Marketing or Utility')}}</p></li>
                    <li class="mt-1"><p>{{__('Set the template name: ')}}{{__('welcome__template')}}</p></li>
                    <li class="mt-1"><p>{{__('Write your message: ')}}</p>
                        <p class="bg-light p-2 rounded mt-1 mb-1">{{__('Hi,welcome back! Would you like to book another service with us?')}}</p>
                    </li>
                    <li class="mt-1"><p>{{__('Add interactive buttons (Quick Reply): ')}}{{__('Yes')}}, {{__('No')}}.</p></li>
                    <li class="mt-1"><p>{{__('Submit for approval.')}}</p></li>
                </ol>

                <p class="mt-4 ">{{__('Once approved, use this template from your backend to message users beyond 24 hours of last interaction.')}}</p>
            </div>
        </div>
    </div>
@endsection
