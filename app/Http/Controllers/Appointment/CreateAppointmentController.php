<?php

declare(strict_types=1);

namespace App\Http\Controllers\Appointment;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

final class CreateAppointmentController extends Controller
{
    use AuthorizesRequests;

    public function __invoke()
    {
        $this->authorize('create', Appointment::class);

        $customers = User::Customer()->get();

        return Inertia::render('Appointment/Create', [
            'customers_data' => [],
            'breadcrumbs' => [
                ['title' => 'Agendamentos', 'href' => route('appointments.list')],
            ],
        ]);
    }
}
