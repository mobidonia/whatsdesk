<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirectTo()
    {
        return route('home');

    }


    private function createCompany(User $user)
    {
        $company = new Company([
            'name' => $user->name,
            'is_featured' => 0,
            'subdomain' => strtolower(preg_replace('/[^A-Za-z0-9]/', '', $user->name.Str::random(10))),
            'active' => 1,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $company->save();
    }

    public function googleRedirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function googleHandleProviderCallback(): RedirectResponse
    {
        $user_google = Socialite::driver('google')->user();
        $user = User::where('email', $user_google->email)->first();
        if (! $user) {
            $user = new User;
            $user->name = $user_google->name;
            $user->email = $user_google->email;
            $user->password = Str::random(80);
            $user->save();

            $user->assignRole('owner');
            $this->createCompany($user);
        }

        // login
        Auth::loginUsingId($user->id);

        return redirect($this->redirectTo());
    }

    public function facebookRedirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function facebookHandleProviderCallback(): RedirectResponse
    {
        $user_facebook = Socialite::driver('facebook')->user();
        $user = User::where('email', $user_facebook->email)->first();
        if (! $user) {
            $user = new User;
            $user->name = $user_facebook->name;
            $user->email = $user_facebook->email;
            $user->password = Str::random(80);
            $user->save();
            $user->assignRole('owner');
            $this->createCompany($user);
        }
        // login
        Auth::loginUsingId($user->id);

        return redirect($this->redirectTo());
    }
}
