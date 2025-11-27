<?php

namespace App\Http\Controllers\Appointment;

use App\Helpers\Enums\UserRoles;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class DeleteAppointmentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        string $id
    ): void {
        //
        $appointment = Appointment::with('animal')->findOrFail($id);

        $user_id = match (auth()->user()->role) {
            UserRoles::Customer->value => auth()->user()->id,
            UserRoles::Receptionist->value => ''
        };
    }
}
