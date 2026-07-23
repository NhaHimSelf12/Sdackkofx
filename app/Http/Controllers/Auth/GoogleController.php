<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Find user by Google ID
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // If user exists, log them in
                Auth::login($user);
                return redirect()->route('dashboard');
            }

            // Otherwise, check if user exists by email
            $existingUser = User::where('email', $googleUser->email)->first();

            if ($existingUser) {
                // Update their google_id and avatar if missing
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $existingUser->avatar ?? $googleUser->avatar
                ]);
                Auth::login($existingUser);
                return redirect()->route('dashboard');
            }

            // Create a new user
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'password' => bcrypt(Str::random(16)), // Set random secure password
                'role' => 'user',
                'account_balance' => 0,
            ]);

            Auth::login($newUser);
            return redirect()->route('dashboard');

        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Unable to login with Google. Please try again.');
        }
    }
}
