<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\User;

class SocialLoginController extends Controller
{
    public function facebook_redirect(Request $request)
    {

        $type = $request->query('type'); // 1 = buyer, 0 = seller
        $origin = $request->query('origin'); // login or signup

        // Store temporarily in session
        session([
            'get_user_type' => $type,
            'social_login_origin' => $origin,
        ]);
        return Socialite::driver('facebook')->redirect();
    }

    public function facebook_callback()
    {
        try {
            $user_fb_details = Socialite::driver('facebook')->user();

            $get_user_type = session()->pull('get_user_type');
            $origin = session()->pull('social_login_origin');

            $user_type = in_array($get_user_type, [0, 1]) ? $get_user_type : null;

            $user = User::where('email', $user_fb_details->getEmail())->first();

            if ($user) {
                Auth::guard('web')->login($user);
                return $user->user_type == 1
                    ? redirect()->to('buyer/dashboard/#')
                    : redirect()->to('seller/dashboard/#');
            }

            // User not found
            if ($origin === 'signup') {
                // Create new user since request came from signup page
                $new_user = User::create([
                    'username' => 'fb_' . explode('@', $user_fb_details->getEmail())[0],
                    'name' => $user_fb_details->getName(),
                    'email' => $user_fb_details->getEmail(),
                    'facebook_id' => $user_fb_details->getId(),
                    'user_type' => $user_type ?? 1, // default to buyer
                    'email_verified' => 1,
                    'otp_verified' => 1,
                    'password' => Hash::make(\Illuminate\Support\Str::random(8))
                ]);

                Auth::guard('web')->login($new_user);
                return $new_user->user_type == 1
                    ? redirect()->to('buyer/dashboard/#')
                    : redirect()->to('seller/dashboard/#');
            }

            // Came from login but user doesn't exist → send to regular signup page
            return redirect()->route('user.register')->with([
                'msg' => 'No account found. Please sign up first.',
                'type' => 'warning'
            ]);

        } catch (\Exception $e) {
            return redirect()->to('login/#')->with([
                'msg' => $e->getMessage(),
                'type' => 'danger'
            ]);
        }
    }

    public function google_redirect(Request $request)
    {
        $type = $request->query('type'); // 1 = buyer, 0 = seller
        $origin = $request->query('origin'); // login or signup

        // Store temporarily in session
        session([
            'get_user_type' => $type,
            'social_login_origin' => $origin,
        ]);
        return Socialite::driver('google')
            ->redirectUrl(env('GOOGLE_REDIRECT'))
            ->redirect();
    }

    public function google_callback()
    {
        try {
            $user_google_details = Socialite::driver('google')->user();
            $get_user_type = session()->pull('get_user_type');
            $origin = session()->pull('social_login_origin');

            $user_type = in_array($get_user_type, [0, 1]) ? $get_user_type : 1;

            $user = User::where('email', $user_google_details->getEmail())->first();

            if ($user) {
                Auth::guard('web')->login($user);
                return $user->user_type == 1
                    ? redirect()->to('buyer/dashboard/#')
                    : redirect()->to('seller/dashboard/#');
            }

            if ($origin === 'signup') {
                // Create new user from signup origin
                $new_user = User::create([
                    'username' => 'gl_' . explode('@', $user_google_details->getEmail())[0],
                    'name' => $user_google_details->getName(),
                    'email' => $user_google_details->getEmail(),
                    'google_id' => $user_google_details->getId(),
                    'user_type' => $user_type,
                    'email_verified' => 1,
                    'otp_verified' => 1,
                    'password' => Hash::make(\Illuminate\Support\Str::random(8))
                ]);

                Auth::guard('web')->login($new_user);
                return $new_user->user_type == 1
                    ? redirect()->to('buyer/dashboard/#')
                    : redirect()->to('seller/dashboard/#');
            }

            // Came from login but user doesn't exist
            return redirect()->route('user.register')->with([
                'msg' => 'No account found. Please sign up first.',
                'type' => 'warning'
            ]);

        } catch (\Exception $e) {
            return redirect()->to('login/#')->with([
                'msg' => $e->getMessage(),
                'type' => 'danger'
            ]);
        }
    }

}
