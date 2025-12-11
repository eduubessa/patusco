<?php

declare(strict_types=1);

namespace App\Http\Controllers\Appointment;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class EditAppointmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Appointment $appointment)
    {
        //
        $this->authorize('update', $appointment);

        return Inertia::render('Appointment/Edit', [
            'appointment' => $appointment,
        ]);
    }
}
