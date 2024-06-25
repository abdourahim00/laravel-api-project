<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;

class UserMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        User::macro('generateOTP', function () {
            return rand(100000, 999999);
        });

        // Macro to check if OTP is valid and update it
        User::macro('checkAndUpdateOTP', function ($otp) {
            return $this->where('otp', $otp)
                        ->where('otp_expired_at', '>', now())
                        ->first();
        });
    }
}
