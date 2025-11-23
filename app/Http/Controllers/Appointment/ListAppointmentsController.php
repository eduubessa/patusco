<?php

namespace App\Http\Controllers\Appointment;

use App\Actions\Appointment\ListAppointments;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ListAppointmentsController extends Controller
{
    //
    public function __invoke(Request $request, ListAppointments $listAppointments)
    {

        $appointments = $listAppointments->handle();

        return Inertia::render('Appointment/List');
    }
}
