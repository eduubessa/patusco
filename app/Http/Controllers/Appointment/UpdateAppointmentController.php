<?php

namespace App\Http\Controllers\Appointment;

use App\Actions\Appointment\UpdateAppointment;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UpdateAppointmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateAppointmentRequest $request, Appointment $appointment, UpdateAppointment $action)
    {
        //
        $this->authorize('update', $appointment);

        $action->update($appointment, $request->validated());

        return redirect()->route('appointments.show', $appointment->slug)
            ->with('success', "O agendamento foi atualizado com sucesso.");
    }
}
