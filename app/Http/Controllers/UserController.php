<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Sms;
use Mail;
use Auth;
use GlobalHelpers;


class UserController extends Controller
{
    // // public function register(Request $request)
    // // {
    // //    if(User::where('email', $request->email)->exists()){
    // //        return response()->json(['message' => 'Email already exists'], 409);
    // //    }else{
    // //     $user = User::create([
    // //         'name' => $request->name,
    // //         'email' => $request->email,
    // //         'password' => Hash::make($request->password),
    // //     ]);
    // //     $request->validate([
    // //         'email' => 'required|email',
    // //     ]);
    // //     $checkUser = User::where('email', $request->email)->first();
    // //     if(is_null($checkUser)){
    // //         return response()->json(['status' => 0, 'message' => 'User not found'], 200);
    // //     }else{
    // //         $otp = rand(100000, 999999);
    // //         $userUpdate = User::where('email', $request->email)->update(['otp' => $otp]);

    // //         Mail::send('email.otp', ['otp' => $otp], function ($message) use ($request) {
    // //             $message->to($request->email);
    // //             $message->subject('Registration OTP');

    // //         });

    // //     }
    // //     // return response()->json($user);
    // //     }
    // // }


    // public function register(Request $request)
    // {
    //     try {
    //         // Validate the incoming request data
    //         $request->validate([
    //             'name' => 'required|string',
    //             'email' => 'required|email|unique:users,email',
    //             'password' => 'required|string|min:6',
    //         ]);

    //         // Create the user
    //         $user = User::create([
    //             'name' => $request->name,
    //             'email' => $request->email,
    //             'password' => Hash::make($request->password),
    //         ]);

    //         // Generate OTP
    //         $otp = rand(100000, 999999);

    //         // Update the user with OTP
    //         $user->update(['otp' => $otp]);

    //         // Send OTP via email
    //         Mail::send('email.otp', ['otp' => $otp], function ($message) use ($request) {
    //             $message->to($request->email)
    //                     ->subject('Registration OTP');
    //         });

    //         // Return a success response with status code 201 (Created)
    //         return response()->json(['message' => 'User registered successfully'], 201);

    //     } catch (\Exception $e) {
    //         // Handle any exceptions
    //         return response()->json([
    //             'message' => 'Registration failed',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }



    // // public function login(Request $request){
    // //      $request->validate([
    // //         'email' => 'required|email',
    // //         'password' => 'required',
    // //     ]);
    // //     if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
    // //         $user = Auth::user();
    // //         $token = Str::random(32);
    // //         $otp = rand(100000, 999999);
    // //         $userUpdate = User::where('email', $request->email)->update(['otp' => $otp]);
    // //         Mail::send('email.loginOTP', ['otp' => $otp], function ($message) use ($request) {
    // //             $message->to($request->email);
    // //             $message->subject('Login OTP');

    // //         });
    // //         return response()->json(['status' => 1, 'message' => 'Login successful', 'user' => $user, 'token' => $token], 200);
    // //     }else{
    // //         return response()->json(['status' => 0, 'message' => 'Email/Password is incorrect'], 200);
    // //     }


    //     public function login(Request $request)
    //     {
    //         // Validate the incoming request data
    //         $request->validate([
    //             'email' => 'required|email',
    //             'password' => 'required',
    //         ]);

    //         // Attempt to authenticate the user
    //         if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
    //             $user = Auth::user();

    //             // Generate OTP
    //             $otp = rand(100000, 999999);

    //             // Update user with OTP
    //             $user->update(['otp' => $otp]);

    //             // Send OTP via email
    //             Mail::send('email.loginOTP', ['otp' => $otp], function ($message) use ($request) {
    //                 $message->to($request->email);
    //                 $message->subject('Login OTP');
    //             });

    //             // Generate a random token for the session
    //             $token = Str::random(32);

    //             // Return a successful response with user data, token, and status code 200
    //             return response()->json([
    //                 'status' => 200,
    //                 'message' => 'Login successful',
    //                 'user' => $user,
    //                 'token' => $token
    //             ], 200);
    //         } else {
    //             // Authentication failed
    //             return response()->json([
    //                 'status' => 0,
    //                 'message' => 'Email/Password is incorrect'
    //             ], 401); // Unauthorized status code
    //         }
    //     }


    //     public function confirmOTP(Request $request)
    //     {
    //         // Validate the incoming OTP
    //         $request->validate([
    //             'otp' => 'required',
    //         ]);

    //         // Check if the OTP exists and is not expired
    //         $user = User::where('otp', $otp)
    //         ->where('otp_expired_at', '>', now())
    //         ->first();


    //         if ($user) {
    //             // Generate a new OTP
    //             $newOTP = rand(100000, 999999);

    //             // Update user with the new OTP
    //             $user->update(['otp' => $newOTP]);

    //             // Send email notification about the updated OTP
    //             Mail::send('email.updateOTP', ['otp' => $newOTP], function ($message) use ($user) {
    //                 $message->to($user->email)
    //                         ->subject('OTP Expired and Updated');
    //             });

    //             return response()->json(['status' => 1, 'message' => 'OTP is expired and updated'], 200);
    //         } else {
    //             return response()->json(['status' => 2, 'message' => 'OTP is valid'], 404);
    //         }
    //     }


        public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'phone_number' => 'required|string',
                'role_id' => 'required',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'role_id' => $request->role_id,
            ]);

            $otp = rand(100000, 999999);
            $user->update(['otp' => $otp]);

            Mail::send('email.otp', ['otp' => $otp], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Registration OTP');
            });
            $phone = $user->phone_number;
            GlobalHelpers::sendSms($phone, 'votre code OTP: '.$otp);


            return response()->success('User registered successfully', ['user' => $user]);

        } catch (\Exception $e) {
            return response()->error('Registration failed', ['error' => $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $otp = rand(100000, 999999);
                $user->update(['otp' => $otp]);

                Mail::send('email.loginOTP', ['otp' => $otp], function ($message) use ($request) {
                    $message->to($request->email)
                            ->subject('Login OTP');
                });
                $phone = $user->phone_number;
                GlobalHelpers::sendSms($phone, 'votre code OTP: '.$otp);
                // $token = Str::random(32);

                return response()->success('Login successful', [
                    'user' => $user,
                    // 'token' => $token,
                ]);

            } else {
                return response()->error('Email/Password is incorrect');
            }

        } catch (\Exception $e) {
            return response()->error('Login failed', ['error' => $e->getMessage()]);
        }
    }

    public function regenerateOTP(Request $request)
    {
        // Vérifier si l'email est présent dans la requête
        if (!$request->has('email') || empty($request->email)) {
            return response()->error('Email not provided');
        }

        // Rechercher l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        // Vérifier si l'utilisateur existe
        if (!$user) {
            return response()->error('User not found');
        }

        // Générer un nouveau OTP
        $otp = rand(100000, 999999);

        // Mettre à jour l'OTP de l'utilisateur
        $user->update(['otp' => $otp]);

        // Envoyer l'OTP par email
        Mail::send('email.otp', ['otp' => $otp], function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('OTP');
        });

        // Retourner une réponse de succès
        return response()->success('OTP regenerated successfully');
    }

    public function verifyOTP(Request $request)
    {
        // Vérifier si l'email et le code OTP sont presents dans la requête
        if (empty($request->otp)) {
            return response()->error('Veuillez entrer le code OTP');
        }

        // Rechercher l'utilisateur par email
        $user = User::where('otp', $request->otp)->first();
        // Vérifier si l'utilisateur existe
        if (!$user) {
            return response()->error("OTP n'\existe pas");
        }

        // $credentials = $request->only('email', 'password');
        $token = Auth::login($user);

        //recuperer l'utilisateur
        $us = Auth::user();

        return response()->success('OTP verified successfully', [
            'user' => $us,
            'token' => $token
        ]);
            // return response()->success('OTP verified successfully');

    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }
    public function updateUser(Request $request, $id){

        $user = User::find($id);
        $user->update($request->all());

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function deleteUser($id){
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }
        $user->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
        ]);
    }
}
