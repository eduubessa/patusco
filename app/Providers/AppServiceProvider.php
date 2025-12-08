<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Appointment;
use App\Policies\AppointmentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

final class AppServiceProvider extends AuthServiceProvider
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
