@extends('backend.admin-master')
@section('site-title')
    {{__('Add New Child Category')}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/jodit.fat.min.css')}}">
    <x-media.css/>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-msg.success/>
                <x-msg.error/>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrap d-flex justify-content-between">
                            <div class="left-content">
                                <h4 class="header-title">{{__('Add New Child Category')}}   </h4>
                            </div>
                            <div class="right-content">
                                <a class="btn btn-info btn-sm" href="{{route('admin.child.category')}}">{{__('All Child Categories')}}</a>
                            </div>
                        </div>
                        <form action="{{route('admin.child.category.new')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-40">
                                
                                <div class="form-group">
                                    <label for="category" class="info-title"> {{__('Select Parent Category*')}} </label>
                                    <select name="category_id" id="category" class="form-control">
                                        <option value="">{{__('Select Category')}}</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="subcategory" class="info-title"> {{__('Select Sub Category*')}} </label>
                                    <select  name="sub_category_id" id="subcategory" class="form-control subcategory"></select>
                                </div>

                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#child-name-it" role="tab" style="color: blue">{{__('Italian (Default)')}}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#child-name-en" role="tab" style="color: blue">{{__('English')}}</a>
                                    </li>
                                </ul>
                                <div class="tab-content mb-3">
                                    <div class="tab-pane fade show active" id="child-name-it" role="tabpanel">
                                        <div class="form-group">
                                            <label for="name">{{__('Child Category (Italian)')}}</label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="{{__('Child Category Name')}}">
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="child-name-en" role="tabpanel">
                                        <div class="form-group">
                                            <label for="name_en">{{__('Child Category (English)')}}</label>
                                            <input type="text" class="form-control" name="name_en" id="name_en" placeholder="{{__('Child Category Name')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group permalink_label">
                                    <label class="text-dark">{{__('Permalink * :')}}
                                        <span id="slug_show" class="display-inline"></span>
                                        <span id="slug_edit" class="display-inline">
                                             <button class="btn btn-warning btn-sm slug_edit_button"> <i class="fas fa-edit"></i> </button>
                                            <input type="text" name="slug" class="form-control child_category_slug mt-2" style="display: none">
                                              <button class="btn btn-info btn-sm slug_update_button mt-2" style="display: none">{{__('Update')}}</button>
                                        </span>
                                    </label>
                                </div>


                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#child-desc-it" role="tab" style="color: blue">{{__('Italian (Default)')}}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#child-desc-en" role="tab" style="color: blue">{{__('English')}}</a>
                                    </li>
                                </ul>
                                <div class="tab-content mb-3">
                                    <div class="tab-pane fade show active" id="child-desc-it" role="tabpanel">
                                        <div class="form-group">
                                            <label>{{__('Description (Italian)')}}</label>
                                            <textarea id="jodit-editor" style="height: 400px;"></textarea>
                                            <textarea name="description" id="description" class="d-none">{{ old('description')}}</textarea>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="child-desc-en" role="tabpanel">
                                        <div class="form-group">
                                            <label>{{__('Description (English)')}}</label>
                                            <textarea id="jodit-editor-en" style="height: 400px;"></textarea>
                                            <textarea name="description_en" id="description_en" class="d-none">{{ old('description_en')}}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="image">{{__('Upload Child Category Image')}}</label>
                                    <div class="media-upload-btn-wrapper">
                                        <div class="img-wrap"></div>
                                        <input type="hidden" name="image">
                                        <button type="button" class="btn btn-info media_upload_form_btn"
                                                data-btntitle="{{__('Select Image')}}"
                                                data-modaltitle="{{__('Upload Image')}}" data-toggle="modal"
                                                data-target="#media_upload_modal">
                                            {{__('Upload Image')}}
                                        </button>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <div class="card">
                                            <div class="card-body meta">
                                                <h5 class="header-title">{{__('Meta Section')}}</h5>
                                                <div class="row">
                                                    <div class="col-xl-4 col-lg-3">
                                                        <div class="nav flex-column nav-pills" id="v-pills-tab"
                                                             role="tablist" aria-orientation="vertical">
                                                            <a class="nav-link active" id="v-pills-home-tab"
                                                               data-toggle="pill" href="#v-pills-home" role="tab"
                                                               aria-controls="v-pills-home"
                                                               aria-selected="true">{{__('Child Category Meta')}}</a>
                                                            <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill"
                                                               href="#v-pills-profile" role="tab"
                                                               aria-controls="v-pills-profile"
                                                               aria-selected="false">{{__('Facebook Meta')}}</a>
                                                            <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill"
                                                               href="#v-pills-messages" role="tab"
                                                               aria-controls="v-pills-messages"
                                                               aria-selected="false">{{__('Twitter Meta')}}</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-8 col-lg-9">
                                                        <div class="tab-content meta-content left-side-meta" id="v-pills-tabContent">

                                                            <!-- child category meta section start -->
                                                            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                                                <ul class="nav nav-tabs mb-3" role="tablist">
                                                                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#child-meta-it" role="tab" style="color:blue">{{__('Italian (Default)')}}</a></li>
                                                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#child-meta-en" role="tab" style="color:blue">{{__('English')}}</a></li>
                                                                </ul>
                                                                <div class="tab-content">
                                                                    <div class="tab-pane fade show active" id="child-meta-it" role="tabpanel">
                                                                        <div class="form-group"><label>{{__('Meta Title (Italian)')}}</label><input type="text" class="form-control" name="meta_title" value="{{ old('meta_title') }}" placeholder="{{__('Title')}}"></div>
                                                                        <div class="form-group"><label>{{__('Meta Tags (Italian)')}}</label><input type="text" class="form-control" name="meta_tags" value="{{ old('meta_tags') }}" placeholder="Slug" data-role="tagsinput"></div>
                                                                        <div class="form-group"><label>{{__('Meta Description (Italian)')}}</label><textarea name="meta_description" class="form-control max-height-140" cols="20" rows="4">{{ old('meta_description') }}</textarea></div>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="child-meta-en" role="tabpanel">
                                                                        <div class="form-group"><label>{{__('Meta Title (English)')}}</label><input type="text" class="form-control" name="meta_title_en" value="{{ old('meta_title_en') }}" placeholder="{{__('Title')}}"></div>
                                                                        <div class="form-group"><label>{{__('Meta Tags (English)')}}</label><input type="text" class="form-control" name="meta_tags_en" value="{{ old('meta_tags_en') }}" placeholder="Slug" data-role="tagsinput"></div>
                                                                        <div class="form-group"><label>{{__('Meta Description (English)')}}</label><textarea name="meta_description_en" class="form-control max-height-140" cols="20" rows="4">{{ old('meta_description_en') }}</textarea></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- child category meta section end -->

                                                            <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                                                <ul class="nav nav-tabs mb-3" role="tablist">
                                                                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#child-fb-it" role="tab" style="color:blue">{{__('Italian (Default)')}}</a></li>
                                                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#child-fb-en" role="tab" style="color:blue">{{__('English')}}</a></li>
                                                                </ul>
                                                                <div class="tab-content">
                                                                    <div class="tab-pane fade show active" id="child-fb-it" role="tabpanel">
                                                                        <div class="form-group"><label>{{__('Facebook Meta Title (Italian)')}}</label><input type="text" class="form-control" data-role="tagsinput" value="{{ old('facebook_meta_tags') }}" name="facebook_meta_tags"></div>
                                                                        <div class="form-group"><label>{{__('Facebook Meta Description (Italian)')}}</label><textarea name="facebook_meta_description" class="form-control max-height-140" cols="20" rows="4">{{ old('facebook_meta_description') }}</textarea></div>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="child-fb-en" role="tabpanel">
                                                                        <div class="form-group"><label>{{__('Facebook Meta Title (English)')}}</label><input type="text" class="form-control" data-role="tagsinput" name="facebook_meta_tags_en"></div>
                                                                        <div class="form-group"><label>{{__('Facebook Meta Description (English)')}}</label><textarea name="facebook_meta_description_en" class="form-control max-height-140" cols="20" rows="4"></textarea></div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="image">{{__('Facebook Meta Image (Shared)')}}</label>
                                                                    <div class="media-upload-btn-wrapper">
                                                                        <div class="img-wrap"></div>
                                                                        <input type="hidden" name="facebook_meta_image">
                                                                        <button type="button" class="btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">{{__('Upload Image')}}</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                                                <ul class="nav nav-tabs mb-3" role="tablist">
                                                                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#child-tw-it" role="tab" style="color:blue">{{__('Italian (Default)')}}</a></li>
                                                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#child-tw-en" role="tab" style="color:blue">{{__('English')}}</a></li>
                                                                </ul>
                                                                <div class="tab-content">
                                                                    <div class="tab-pane fade show active" id="child-tw-it" role="tabpanel">
                                                                        <div class="form-group"><label>{{__('Twitter Meta Tag (Italian)')}}</label><input type="text" class="form-control" data-role="tagsinput" name="twitter_meta_tags"></div>
                                                                        <div class="form-group"><label>{{__('Twitter Meta Description (Italian)')}}</label><textarea name="twitter_meta_description" class="form-control max-height-140" cols="20" rows="4"></textarea></div>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="child-tw-en" role="tabpanel">
                                                                        <div class="form-group"><label>{{__('Twitter Meta Tag (English)')}}</label><input type="text" class="form-control" data-role="tagsinput" name="twitter_meta_tags_en"></div>
                                                                        <div class="form-group"><label>{{__('Twitter Meta Description (English)')}}</label><textarea name="twitter_meta_description_en" class="form-control max-height-140" cols="20" rows="4"></textarea></div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="image">{{__('Twitter Meta Image (Shared)')}}</label>
                                                                    <div class="media-upload-btn-wrapper">
                                                                        <div class="img-wrap"></div>
                                                                        <input type="hidden" name="twitter_meta_image">
                                                                        <button type="button" class="btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">{{__('Upload Image')}}</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

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
    <x-media.markup/>
@endsection
@section('script')
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <script src="{{asset('assets/backend/js/jodit.fat.min.js')}}"></script>
 <x-media.js />
 <script>
    (function ($) {
        "use strict";

        $(document).ready(function () {
            //Permalink Code
            $('.permalink_label').hide();
            $(document).on('keyup', '#name', function (e) {
                var slug = converToSlug($(this).val());
                var url = "{{url('/child-category/')}}/" + slug;
                $('.permalink_label').show();
                var data = $('#slug_show').text(url).css('color', 'blue');
                $('.child_category_slug').val(slug);

            });

             function converToSlug(slug){
               let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
                //remove multiple space to single
                finalSlug = slug.replace(/  +/g, ' ');
                // remove all white spaces single or multiple spaces
                finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
                return finalSlug;
            }

            //Slug Edit Code
            $(document).on('click', '.slug_edit_button', function (e) {
                e.preventDefault();
                $('.child_category_slug').show();
                $(this).hide();
                $('.slug_update_button').show();
            });

            //Slug Update Code
            $(document).on('click', '.slug_update_button', function (e) {
                e.preventDefault();
                $(this).hide();
                $('.slug_edit_button').show();
                var update_input = $('.child_category_slug').val();
                var slug = converToSlug(update_input);
                var url = `{{url('/child-category/')}}/` + slug;
                $('#slug_show').text(url);
                $('.child_category_slug').val(slug);
                $('.child_category_slug').hide();
            });

            let jodit = null;
            if ($('#jodit-editor').length && !$('#jodit-editor').hasClass('jodit-initialized')) {
                $('#jodit-editor').addClass('jodit-initialized');
                jodit = Jodit.make('#jodit-editor', {
                    height: 400,
                    placeholder: '{{ __("Type Content") }}',
                    buttons: [
                        'bold', 'italic', 'underline', '|',
                        'ul', 'ol', '|',
                        'outdent', 'indent', '|',
                        'font', 'fontsize', 'brush', 'paragraph', '|',
                        'align', 'undo', 'redo', '|',
                        'link', 'image', 'video', 'table', '|',
                        'hr', 'eraser', 'fullsize'
                    ],
                    uploader: {
                        insertImageAsBase64URI: true
                    }
                });

                // Sync Jodit content with hidden textarea
                jodit.events.on('change', () => {
                    $('#description').val(jodit.getEditorValue());
                });

                // Set initial content if exists
                const initialContent = $('#description').val();
                if (initialContent && initialContent.trim() !== '') {
                    jodit.setEditorValue(initialContent);
                }
            }

            // select category, sub category and Child Category
            $('#category').on('change',function(){
                var category_id = $(this).val();
                $.ajax({
                    method:'post',
                    url:"{{route('admin.select.subcategory')}}",
                    data:{category_id:category_id},
                    success:function(res){
                        if(res.status=='success'){
                            var alloptions = '';
                            var allSubCategory = res.sub_categories;
                            $.each(allSubCategory,function(index,value){
                                alloptions +="<option value='" + value.id + "'>" + value.name + "</option>";
                            });
                            $(".subcategory").html(alloptions);
                            $('#subcategory').niceSelect('update');
                        }
                    }
                })
            })
        });
    })(jQuery)
</script>
@endsection 

