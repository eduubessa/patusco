<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Policies\AppointmentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

class AppServiceProvider extends AuthServiceProvider
{
    protected $policies = [
        Appointment::class => AppointmentPolicy::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        $this->registerPolicies();
    }
}
