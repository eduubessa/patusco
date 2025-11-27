<?php

namespace App\Http\Controllers\Appointment;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CreateAppointmentController extends Controller
{
    use AuthorizesRequests;

    //
    public function __invoke()
    {
        $this->authorize('create', Appointment::class);

        return Inertia::render('Appointment/Create');
    }

    public function create(Request $request)
    {
        return Inertia::render('Appointment/Create');
    }
}
