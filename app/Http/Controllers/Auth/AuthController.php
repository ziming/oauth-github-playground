<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Socialite;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    
    public function redirectToProvider() {
        return Socialite::driver('github')->redirect();
    }

    public function handleProviderCallback()
    {
        $githubUser = Socialite::driver('github')->user();
        $user = $this->findOrCreateGithubUser($githubUser);

        auth()->login($user);

        return redirect('/');
    }

    private function findOrCreateGithubUser($githubUser)
    {
        
        // you probably don't want to match on email because your user
        // might have changed email address on your site
        // you probably want to match with the github ID instead
        // which is less likely to change
        $user = User::firstOrNew(
            [
                'github_id' => $githubUser->id,
            ]
        );

        if ($user->exists) return $user;
        
        // github_id is filled earlier already
        $user->fill([
            'username' => $githubUser->nickname,
            'email' => $githubUser->email,
            'avatar' => $githubUser->avatar,
        ])->save();

        return $user;
    }
}
