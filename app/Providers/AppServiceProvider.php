<?php

namespace App\Providers;

use App\Models\Anuncio;
use App\Models\Proposta;
use App\Models\User;
use App\Policies\AnuncioPolicy;
use App\Policies\PropostaPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('anunciar', function (User $user) {
            return $user->role === User::ROLE_ANUNCIANTE;
        });

        Gate::policy(Anuncio::class, AnuncioPolicy::class);
        Gate::policy(Proposta::class, PropostaPolicy::class);
    }
}
