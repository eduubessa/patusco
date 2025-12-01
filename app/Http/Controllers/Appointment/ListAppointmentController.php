<?php

namespace App\Http\Controllers\Appointment;

use App\Actions\Appointment\ListAppointments;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListAppointmentController extends Controller
{
    //
    use AuthorizesRequests;

    public function __invoke(
        Request $request,
        ListAppointments $action
    ) {
        $this->authorize('viewAny', Appointment::class);

        $sortBy = $request->query('sort') ?? 'updated_at';
        $sortDirection = $request->query('direction') ?? 'desc';

        $appointments = $action->handle(auth()->user(), $sortBy, $sortDirection);

        return Inertia::render('Appointment/Calendar', [
            'events' => $appointments,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
        ]);
    }
}
