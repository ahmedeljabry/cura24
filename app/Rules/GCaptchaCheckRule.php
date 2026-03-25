<?php
//
//namespace App\Rules;
//
//use Closure;
//use Illuminate\Contracts\Validation\ValidationRule;
//
//class GCaptchaCheckRule implements ValidationRule
//{
//    /**
//     * Run the validation rule.
//     *
//     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
//     */
//    public function validate(string $attribute, mixed $value, Closure $fail): void
//    {
//        $secretKey = get_static_option('site_google_captcha_v3_secret_key'); // Replace with your actual Secret Key
//        $recaptchaResponse = $value;
//
//// Verify with Google reCAPTCHA API
//        $url = "https://www.google.com/recaptcha/api/siteverify";
//        $data = [
//            "secret" => $secretKey,
//            "response" => $recaptchaResponse
//        ];
//
//// Use cURL for better error handling
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $verify = curl_exec($ch);
//        curl_close($ch);
//
//        $captchaSuccess = json_decode($verify);
////        dd($captchaSuccess);
//        if (!$captchaSuccess || !$captchaSuccess->success || $captchaSuccess->score < 0.7) {
//            $fail("reCAPTCHA verification failed. You might be a bot.");
//        }
//    }
//}


namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class GCaptchaCheckRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $captchaVersion = get_static_option('site_google_captcha_version') ?: 'v3';
        $secretKey = $captchaVersion === 'v2' ? get_static_option('site_google_captcha_v2_secret_key') : get_static_option('site_google_captcha_v3_secret_key');
        $recaptchaResponse = $value;

        // Check if secret key is configured
        if (empty($secretKey)) {
            $fail('reCAPTCHA configuration error. Please contact the administrator.');
            return;
        }

        // Verify with Google reCAPTCHA API
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => request()->ip(), // Optional: include client IP for analytics
        ]);

        $captchaSuccess = $response->json();

        // Validate response
        if (!$captchaSuccess || !isset($captchaSuccess['success']) || !$captchaSuccess['success']) {
            $fail('reCAPTCHA verification failed. Please complete the CAPTCHA.');
            return;
        }

        // Additional v3 score check
        if ($captchaVersion === 'v3' && (!isset($captchaSuccess['score']) || $captchaSuccess['score'] < 0.7)) {
            $fail('reCAPTCHA verification failed. You might be a bot.');
        }
    }
}
