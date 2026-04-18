<div class="col-lg-3">
    <h5 class="header-title">{{__('Meta Section')}}</h5>
    <div class="nav flex-column nav-pills" id="v-pills-tab"
         role="tablist" aria-orientation="vertical">

        <a class="nav-link active" id="v-pills-home-tab"
           data-toggle="pill" href="#v-pills-home" role="tab"
           aria-controls="v-pills-home"
           aria-selected="true">{{$sidebarHeading}}</a>

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
<div class="col-lg-9">
    <div class="tab-content" id="v-pills-tabContent">

        <div class="tab-pane fade show active dynamic-page-meta" id="v-pills-home"
             role="tabpanel" aria-labelledby="v-pills-home-tab">
            <div class="form-group">
                <label for="title">{{__('Meta Title (Italian)')}}</label>
                <input type="text" class="form-control" name="meta_title"
                       placeholder="{{__('Title')}}">
            </div>
            <div class="form-group">
                <label for="meta_title_en">{{__('Meta Title (English)')}}</label>
                <input type="text" class="form-control" name="meta_title_en"
                       placeholder="{{__('Title (English)')}}">
            </div>
            <div class="form-group">
                <label for="slug">{{__('Meta Tags (Italian)')}}</label>
                <input type="text" class="form-control" name="meta_tags"
                       placeholder="Slug" data-role="tagsinput">
            </div>
            <div class="form-group">
                <label for="meta_tags_en">{{__('Meta Tags (English)')}}</label>
                <input type="text" class="form-control" name="meta_tags_en"
                       placeholder="Slug" data-role="tagsinput">
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <label for="title">{{__('Meta Description (Italian)')}}</label>
                    <textarea name="meta_description"
                              class="form-control max-height-140"
                              cols="20"
                              rows="4"></textarea>
                </div>
                <div class="form-group col-md-12">
                    <label for="meta_description_en">{{__('Meta Description (English)')}}</label>
                    <textarea name="meta_description_en"
                              class="form-control max-height-140"
                              cols="20"
                              rows="4"></textarea>
                </div>
            </div>

        </div>

        <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
             aria-labelledby="v-pills-profile-tab">
            <div class="form-group">
                <label for="title">{{__('Facebook Meta Tag (Italian)')}}</label>
                <input type="text" class="form-control" data-role="tagsinput"
                       name="facebook_meta_tags">
            </div>
            <div class="form-group">
                <label for="facebook_meta_tags_en">{{__('Facebook Meta Tag (English)')}}</label>
                <input type="text" class="form-control" data-role="tagsinput"
                       name="facebook_meta_tags_en">
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <label for="title">{{__('Facebook Meta Description (Italian)')}}</label>
                    <textarea name="facebook_meta_description"
                              class="form-control max-height-140"
                              cols="20"
                              rows="4"></textarea>
                </div>
                <div class="form-group col-md-12">
                    <label for="facebook_meta_description_en">{{__('Facebook Meta Description (English)')}}</label>
                    <textarea name="facebook_meta_description_en"
                              class="form-control max-height-140"
                              cols="20"
                              rows="4"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="image">{{__('Facebook Meta Image')}}</label>
                <div class="media-upload-btn-wrapper">
                    <div class="img-wrap"></div>
                    <input type="hidden" name="facebook_meta_image">
                    <button type="button"
                            class="btn btn-info media_upload_form_btn"
                            data-btntitle="{{__('Select Image')}}"
                            data-modaltitle="{{__('Upload Image')}}"
                            data-toggle="modal"
                            data-target="#media_upload_modal">
                        {{__('Upload Image')}}
                    </button>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
             aria-labelledby="v-pills-messages-tab">
            <div class="form-group">
                <label for="title">{{__('Twitter Meta Tag (Italian)')}}</label>
                <input type="text" class="form-control" data-role="tagsinput"
                       name="twitter_meta_tags">
            </div>
            <div class="form-group">
                <label for="twitter_meta_tags_en">{{__('Twitter Meta Tag (English)')}}</label>
                <input type="text" class="form-control" data-role="tagsinput"
                       name="twitter_meta_tags_en">
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <label for="title">{{__('Twitter Meta Description (Italian)')}}</label>
                    <textarea name="twitter_meta_description"
                              class="form-control max-height-140"
                              cols="20"
                              rows="4"></textarea>
                </div>
                <div class="form-group col-md-12">
                    <label for="twitter_meta_description_en">{{__('Twitter Meta Description (English)')}}</label>
                    <textarea name="twitter_meta_description_en"
                              class="form-control max-height-140"
                              cols="20"
                              rows="4"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="image">{{__('Twitter Meta Image')}}</label>
                <div class="media-upload-btn-wrapper">
                    <div class="img-wrap"></div>
                    <input type="hidden" name="twitter_meta_image">
                    <button type="button"
                            class="btn btn-info media_upload_form_btn"
                            data-btntitle="{{__('Select Image')}}"
                            data-modaltitle="{{__('Upload Image')}}"
                            data-toggle="modal"
                            data-target="#media_upload_modal">
                        {{__('Upload Image')}}
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>