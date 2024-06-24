<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Mail;
use Auth;


class UserController extends Controller
{
    public function register(Request $request)
    {
       if(User::where('email', $request->email)->exists()){
           return response()->json(['message' => 'Email already exists'], 409);
       }else{
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $request->validate([
            'email' => 'required|email',
        ]);
        $checkUser = User::where('email', $request->email)->first();
        if(is_null($checkUser)){
            return response()->json(['status' => 0, 'message' => 'User not found'], 200);
        }else{
            $otp = rand(100000, 999999);
            $userUpdate = User::where('email', $request->email)->update(['otp' => $otp]);

            Mail::send('email.otp', ['otp' => $otp], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Registration OTP');

            });

        }
        // return response()->json($user);
        }
    }

    public function login(Request $request){
         $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $token = Str::random(32);
            $otp = rand(100000, 999999);
            $userUpdate = User::where('email', $request->email)->update(['otp' => $otp]);
            Mail::send('email.loginOTP', ['otp' => $otp], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Login OTP');

            });
            return response()->json(['status' => 1, 'message' => 'Login successful', 'user' => $user, 'token' => $token], 200);
        }else{
            return response()->json(['status' => 0, 'message' => 'Email/Password is incorrect'], 200);
        }


    }

    public function confirmOTP(Request $request){
        $checker = $request->validate([
            'otp' => 'required',
        ]);
        $checkOTP = User::where('otp', $request->otp)->first();
        if(is_null($checkOTP)){
            return response()->json(['status' => 1, 'message' => 'OTP is incorrect'], 200);
        }else{
            return response()->json(['status' => 2, 'message' => 'OTP is correct'], 200);
        }
    }
}
