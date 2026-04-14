@php
    $all_languages = \App\Helpers\LanguageHelper::all_languages();
    $current_language_slug = get_user_lang();
    $current_language = $all_languages->firstWhere('slug', $current_language_slug);
    $current_language_name = trim(explode('(', $current_language->name ?? strtoupper($current_language_slug))[0]);
@endphp

@if($all_languages->count() > 1)
    <div class="login-account">
        <a class="accounts" href="javascript:void(0)">
            <span class="account">{{ $current_language_name }}</span>
            <i class="las la-globe"></i>
        </a>
        <ul class="account-list-item mt-2">
            @foreach($all_languages as $lang)
                @php
                    $lang_name = trim(explode('(', $lang->name)[0]);
                @endphp
                <li class="list">
                    <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($lang->slug) }}">
                        {{ $lang_name }}
                        @if($lang->slug === $current_language_slug)
                            <i class="las la-check"></i>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
