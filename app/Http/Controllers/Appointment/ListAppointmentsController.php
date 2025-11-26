<?php

namespace App\Http\Controllers\Appointment;

use App\Actions\Appointment\ListAppointments;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ListAppointmentsController extends Controller
{
    use AuthorizesRequests;
    //
    public function __invoke(ListAppointments $action)
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = $action->handle(user: auth()->user());

        return Inertia::render('Appointment/Calendar', [
            'events' => $appointments,
        ]);
    }
}
