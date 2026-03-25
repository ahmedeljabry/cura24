<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\FlashMsg;

class CaptchaSettingController extends Controller
{
    public function captchaPageSettings(Request $request)
    {
        if ($request->isMethod('post')) {

            $this->validate($request, [
                'site_google_captcha_v2_site_key' => 'nullable|string',
                'site_google_captcha_v2_secret_key' => 'nullable|string',
                'site_google_captcha_v3_site_key' => 'nullable|string',
                'site_google_captcha_v3_secret_key' => 'nullable|string',
            ]);

            $fields = [
                'site_google_captcha_v2_site_key',
                'site_google_captcha_v2_secret_key',
                'site_google_captcha_v3_site_key',
                'site_google_captcha_v3_secret_key',
            ];

            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            
            return redirect()->back()->with(FlashMsg::settings_update());
        }
        return view('backend.pages.appearance-settings.captcha-settings');
    }
}
