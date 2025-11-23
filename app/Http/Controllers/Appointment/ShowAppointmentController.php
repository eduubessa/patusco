<?php

namespace App\Http\Controllers\Appointment;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class ShowAppointmentController extends Controller
{
    //
    public function __invoke(Request $request, string $id)
    {
        $appointment = Appointment::with('animal.owners')->findOrFail($id);

        return response()->json($appointment);
    }
}
