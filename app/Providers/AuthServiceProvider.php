<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define gates

        Gate::define('admin-access', function (User $user) {
            return $user->roles()->get()->contains('name', 'admin');
        });

        Gate::define('agent-access', function (User $user) {
            return $user->roles()->get()->contains('name', 'agent');
        });

        Gate::define('report-access', function (User $user) {
            return $user->roles()->get()->contains('name', 'reporter');
        });
    }
}
