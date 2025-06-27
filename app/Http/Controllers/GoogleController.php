<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\SendGeneratedPassword;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirect(){
        return Socialite::driver('google')->redirect();
    }

    public function callback(){
        $googleUser = Socialite::driver('google')->user();
    
        $user = User::where('email', $googleUser->email)->first();
    
        if(!$user){
            $randomPassword = Str::random(16);

            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'profile_photo_url' => $googleUser->avatar,
                'email_verified_at' => now(),
                'password' => Hash::make($randomPassword)
            ]);

        Mail::to($user->email)->send(new SendGeneratedPassword($randomPassword));
        Log::info("test");
        }

        $user->assignRole('user');
        event(new Registered($user));
        Auth::login($user);
    
        return redirect()->intended(route('welcome'));
    }
    
}
