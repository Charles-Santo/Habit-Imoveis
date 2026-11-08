<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use RuntimeException;

class SocialLoginController extends Controller
{
    private array $providers = ['github', 'google'];

    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();

            $providerId = (string) $socialUser->getId();
            $email = $socialUser->getEmail();

            if (!$email) {
                return redirect()->route('login')
                    ->with('login_error', 'Não foi possível obter seu e-mail do provedor.');
            }

            $user = DB::transaction(function () use ($provider, $providerId, $socialUser, $email) {

                $socialAccount = SocialAccount::where('provider', $provider)
                    ->where('provider_id', $providerId)
                    ->first();

                if ($socialAccount) {
                    $user = User::withTrashed()->find($socialAccount->user_id);

                    if (!$user || $user->trashed()) {
                        throw new RuntimeException('Esta conta está desativada.');
                    }

                    return $user;
                }

                $user = User::withTrashed()
                    ->where('email', $email)
                    ->first();

                if ($user && $user->trashed()) {
                    throw new RuntimeException('Já existe uma conta desativada com este e-mail.');
                }

                if (!$user) {
                    $user = User::create([
                        'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Usuário',
                        'email' => $email,
                        'password' => Hash::make(Str::random(40)),
                        'role' => User::ROLE_USER,
                    ]);
                }

                SocialAccount::create([
                    'user_id' => $user->id,
                    'provider' => $provider,
                    'provider_id' => $providerId,
                ]);

                return $user;
            });

            Auth::login($user);

            session()->regenerate();

            return redirect('/');

        } catch (RuntimeException $e) {
            return redirect()->route('login')
                ->with('login_error', $e->getMessage());
        }
    }
}
