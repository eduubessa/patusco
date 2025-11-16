<?php

namespace App\Http\Controllers\Appointments;

use App\Actions\Appointments\CreateNewAppointment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appointments\StoreAppointmentRequest;

class AppointmentController extends Controller
{
    //
    public function store(StoreAppointmentRequest $request, CreateNewAppointment $createNewAppointment)
    {
        $appointment = $createNewAppointment($request->validated());

        return response()->json([
            'message' => 'Appointment created.',
            'appointment' => $appointment
        ], 201);
    }
}
