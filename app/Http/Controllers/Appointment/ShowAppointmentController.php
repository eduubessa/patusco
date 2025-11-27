<?php

namespace App\Http\Controllers\Appointment;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShowAppointmentController extends Controller
{
    use AuthorizesRequests;

    //
    public function __invoke(
        Request $request,
        Appointment $appointment,
    ) {

        $this->authorize('view', $appointment);

        return Inertia::render('Appointment/Show', [
            'appointment' => $appointment,
        ]);
    }
}
