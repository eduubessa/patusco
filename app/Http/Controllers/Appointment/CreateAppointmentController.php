<?php

namespace App\Http\Controllers\Appointment;

use App\Actions\Appointment\CreateNewAppointment;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CreateAppointmentController extends Controller
{
    //
    public function __invoke(StoreAppointmentRequest $request, CreateNewAppointment $createNewAppointment)
    {
        $owner_id = auth()->user()->hasRole('customer') ? auth()->user()->id : $request->input('owner_id');

        $appointment = $createNewAppointment->create($request->all(), $owner_id);

        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Appointment created successfully',
                'data' => $appointment
            ], 201);
        }catch(ValidationException $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function create(Request $request)
    {
        return Inertia::render('Appointment/Create');
    }
}
