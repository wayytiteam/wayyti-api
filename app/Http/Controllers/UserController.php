<?php

namespace App\Http\Controllers;

use App\Mail\AccountDeleteSent;
use App\Mail\WelcomeEmailSent;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Mail;
use Exception;
use App\Mail\OTPSent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('is_admin', false)->get();

        return response()->json($users, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            $has_password = false;
            if($user && $user->password){
                $has_password = true;
            }
            $new_user = User::updateOrCreate(
                [
                    'email' => $request->email,
                ],
                [
                    'email' => $request->email,
                    'password' => $request->password
                ]
            );
            $user = User::where('email', $new_user->email)->first();
            $user->toArray();
            $user['has_password'] = $has_password;
            // $token = $new_user->createToken('Email Sign-up')->accessToken;
            return response()->json([
                'user' => $user,
                // 'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, Request $request)
    {
        $now = Carbon::now();
        $user->load('personas', 'recent_searches');
        if($request->query('password')) {
            try {
                if (!(Hash::check($request->query('password'), $user->password))) {
                    throw new Exception('Wrong passwrd');
                }
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 404);
            }
        }
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            foreach ($request->all() as $key => $value) {
                if (array_key_exists($key, $user->getAttributes())) {
                    $user->$key = $value;
                }
            }
            if($request->new_password) {
                $user->password = Hash::make($request->new_password);
            }
            $user->save();
            $user->load('personas');

            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try{
            Mail::to($user->email)->send(new AccountDeleteSent());
            $user->delete();

            return response()->json([
                'message' => 'User account deactivated'
            ], 200);
        } catch(\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function email_sign_in(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Something went wrong',
                'errors' => $validator->errors()
            ], 400);
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();
            $user->load('personas');
            // $token = $user->createToken('Email Sign-in')->accessToken;
            if($user->is_admin) {
                $token = $user->createToken('Admin Sign-in', ['admin'])->accessToken;
            } else {
                $token = $user->createToken('Email Sign-in', ['user'])->accessToken;
            }
            return response()->json([
                "message" => "Signed-in successfully",
                "token" => $token,
                "user" => $user
            ], 200);
        } else {
            return response()->json([
                "message" => "User not found",
            ], 401);
        }
    }

    // public function facebook_mobile_sign_in(Request $request)
    // {
    //     $token = '';
    //     $facebook_credential = Socialite::driver('facebook')
    //         ->fields(['name', 'email', 'id'])
    //         ->userFromToken($request->access_token);

    //     $user = User::where('facebook_id', $facebook_credential->id)->first();
    //     if (!$user) {
    //         try {
    //             $user = User::create([
    //                 'facebook_id' => $facebook_credential->id,
    //                 'email' => $facebook_credential->email,
    //                 'email_verified_at' => Carbon::parse(now())
    //             ]);
    //             $token = $user->createToken('Facebook Authentication')->accessToken;
    //             return response()->json([
    //                 'token' => $token,
    //                 'user' => $user
    //             ], 200);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'message' => 'Something went wrong',
    //                 'error' => $e->getMessage()
    //             ], 400);
    //         }
    //     }
    // }
    public function facebook_mobile_sign_in(Request $request)
    {
       $facebook_id = $request->uid;
       $email = $request->email;
       $user = User::where('facebook_id', $facebook_id)->first();
       if(!$user) {
        $user = User::create([
            'facebook_id' => $facebook_id,
            'email' => $email
        ]);
       }
       $token = $user->createToken('Facebook Authentication')->accessToken;

       return response()->json([
        'token' => $token,
        'user' => $user
       ], 200);
    }

    public function google_mobile_sign_in(Request $request)
    {
        try{
            $google_credential = Socialite::driver('google')->userFromToken($request->access_token);

            $user = User::where('google_id', $google_credential->id)->first();
            if (!$user) {
                $user = User::create([
                    'google_id' => $google_credential->id,
                    'email' => $google_credential->email,
                    'email_verified_at' => Carbon::parse(now())
                ]);
            }
            $token = $user->createToken('Google Authentication')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    // public function apple_sign_in(Request $request)
    // {
    //     $authorizationCode = $request->input('authorization_code');

    //     $clientSecret = JWT::encode([
    //         'iss' => config('services.apple.team_id'),
    //         'iat' => time(),
    //         'exp' => time() + (86400 * 180), // Valid for 6 months
    //         'aud' => 'https://appleid.apple.com',
    //         'sub' => config('services.apple.client_id'),
    //     ], file_get_contents(config('services.apple.private_key')), 'ES256', config('services.apple.key_id'));

    //     try {
    //         $response = Http::post('https://appleid.apple.com/auth/token', [
    //             'client_id' => config('services.apple.client_id'),
    //             'client_secret' => $clientSecret,
    //             'code' => $authorizationCode,
    //             'grant_type' => 'authorization_code',
    //         ]);

    //         $responseBody = json_decode($response->getBody(), true);

    //         $idToken = $responseBody['id_token'];
    //         $decodedToken = json_decode(base64_decode($idToken));

    //         $appleUserId = $decodedToken->sub;
    //         $email = $decodedToken->email ?? null;

    //         $user = User::where('ios_id', $appleUserId)->first();
    //         if (!$user) {
    //             $user = User::create([
    //                 'ios_id' => $appleUserId,
    //                 'email' => $email,
    //                 'email_verified_at' => Carbon::parse(now())
    //             ]);
    //         }

    //         $token = $user->createToken('Apple Authentication')->accessToken;
    //         return response()->json([
    //             'token' => $token,
    //             'user' => $user
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Authentication failed'], 401);
    //     }
    // }
    public function apple_sign_in(Request $request)
    {
       $ios_id = $request->uid;
       $email = $request->email;
       $user = User::where('ios_id', $ios_id)->first();
       if(!$user) {
        $user = User::create([
            'ios_id' => $ios_id,
            'email' => $email
        ]);
       }
       $token = $user->createToken('IOS Authentication')->accessToken;

       return response()->json([
        'token' => $token,
        'user' => $user
       ], 200);
    }

    // public function apple_sign_in(Request $request)
    // {
    //     $provider = 'apple';
    //     $token = $request->jwt_token ?? null;

    //     if (!$token) {
    //         return response()->json(['error' => 'Missing token'], 400);
    //     }

    //     $social_user = Socialite::driver($provider)->userFromToken($token);

    //     if (!$social_user) {
    //         return response()->json(['error' => 'Failed to authenticate'], 401);
    //     }

    //     return $this->get_local_user($social_user);
    // }

    public function get_local_user(Socialite $socialUser)
    {
        $user = User::where('ios_id', $socialUser->id)
            ->first();
        if (!$user) {
            $user = $this->register_apple_user($socialUser);
            return response()->json($user, 200);
        } else {
            $token = $user->createToken('Apple Login')->accessToken;
            return response()->json(['user' => $user, 'token' => $token]);
        }
    }

    public function register_apple_user(Socialite $social_user) {
        $new_user = User::create([
                'email' => $social_user->email,
                'email_verified_at' => now(),
                'ios_id' => $social_user->id,
            ]);
        $token = $new_user->createToken('Apple Login')->accessToken;

        return response()->json(['user' => $new_user, 'token' => $token]);
    }

    // public function apple_sign_in_callback(Request $request)
    // {
    //     return Socialite::driver('apple')->redirect();
    // }

    public function apple_sign_in_callback(Request $request)
    {
        $redirectParams = http_build_query($request->all());
        $redirect = "intent://callback?" . $redirectParams . "#Intent;package=api.smartsales.koda.ws;scheme=signinwithapple;end";

        return Redirect::to($redirect, 307);
    }

    public function request_verification_code(Request $request)
    {
        try {
            $email = $request->email;
            $name = explode('@', $email);
            $name = $name[0];
            $greetings = null;
            $verification_code = mt_rand(10000, 99999);
            switch ($request->action) {
                case 'sign-up':
                    $subject = "Verify Your Email to Access Wayyti";
                    $header_message = "To complete your registration, verify your email by entering the code below into the app:";
                    $message = "Start tracking prices today-it's as simple as: search, track, wait, and save!";
                    $greetings = "Thanks for joining Wayyti!";
                    break;
                case 'password':
                    $subject = "Reset Your Wayyti Password";
                    $header_message = "We received a request to reset your Wayyti password. To continue, enter the code below in the
                    app:";
                    $message = "Don’t miss out—keep adding products to your list, and we’ll handle the rest. It’s as simple as: search, track, wait, and save!";
                    break;
                case 'email':
                    $subject = "Change Your Wayyti Email";
                    $header_message = "We received a request to change your Wayyti email. To continue, enter the code below in the app:";
                    $message = "Once verified, all future communications will be sent to your new email address.";
                    break;
            }
            $verification_code_timestamp = now()->addHours(24);
            Cache::put('verification_code' . $email, $verification_code, $verification_code_timestamp);
            Cache::put('verification_code_timestamp' . $email, $verification_code_timestamp);
            Mail::to($email)->send(new OTPSent(
                $verification_code,
                $header_message,
                $subject,
                $message,
                $greetings,
                $request->action));
            return response()->json([
                'message' => 'Verifcation code sent',
                'verification_code_timestamp' => $verification_code_timestamp
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function verify_otp(Request $request)
    {
        $user = $user = User::where('email', $request->email)->first();
        $cached_verification_code = Cache::get('verification_code' . $request->email);
        try {
            // if($user->hasVerifiedEmail()){
            if ($cached_verification_code !== null) {
                if ($cached_verification_code === (int)$request->verification_code) {
                    $user->markEmailAsVerified();
                    $token = $user->createToken('Token from OTP')->accessToken;
                    $message = 'Valid OTP code';
                    Cache::forget('verification_code' . $request->email);
                    Cache::forget('verification_code_timestamp' . $request->email);
                    return response()->json([
                        'user' => $user,
                        'token' => $token
                    ], 200);
                } else {
                    throw new Exception('Invalid Code');
                }
            } else {
                throw new Exception('Verification code is either expired or already used. Please do note that verification code is only available for 24 hours');
            }
            // } else {
            //     throw new Exception('User is already Verified');
            // }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function check_password(Request $request)
    {
        try {
            $user = Auth::user();
            if (Hash::check($request->password, $user->password)) {
                throw new Exception("Wrong Password");
            }
            return response()->json([
                ''
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function check_email(Request $request)
    {
        try {
            $user = User::where('email', $request->query('email'))->first();
            if (!$user) {
                throw new Exception("User not found");
            }
            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function welcome_email(Request $request) {
        try {
            $user = User::find($request->user_id);
            if($user) {
                Mail::to($user->email)->send(new WelcomeEmailSent($user->email));
                return response()->json([
                    'message' => 'Email Sent'
                ], 200);
            } else {
                throw new Exception("User not found");
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}
