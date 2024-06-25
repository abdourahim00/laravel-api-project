<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Mail;
use Auth;

class OTPController extends Controller
{
    // public function loginwithotppost(Request $request){
    //     $request->validate([
    //         'email' => 'required|email',
    //         // 'otp' => 'required',
    //     ]);
    //     $checkUser = User::where('email', $request->email)->first();
    //     if(is_null($checkUser)){
    //         return response()->json(['status' => 0, 'message' => 'User not found'], 200);
    //     }else{
    //         $otp = rand(100000, 999999);
    //         $userUpdate = User::where('email', $request->email)->update(['otp' => $otp]);

    //         Mail::send('email.otp', ['otp' => $otp], function ($message) use ($request) {
    //             $message->to($request->email);
    //             $message->subject('Login OTP');

    //         });
    //     }
    // }
}
