<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
         'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        // 토큰 유효기간
        Passport::tokensExpireIn(now()->addHours(12));

        // refresh token
        Passport::refreshTokensExpireIn(now()->addDays(60));

        // personalAccessToken
        Passport::personalAccessTokensExpireIn(now()->addHours(6));
        //
    }
}
