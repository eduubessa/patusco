<?php

declare(strict_types=1);

namespace App\Http\Controllers\Appointment;

use App\Actions\Appointment\CreateNewAppointment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Log;
use Throwable;

final class StoreAppointmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     */
    public function __invoke(
        StoreAppointmentRequest $request,
        CreateNewAppointment $action
    ): RedirectResponse {
        //
        $this->authorize('create', Appointment::class);

        try {
            $appointment = $action->handle(
                array_merge(
                    $request->validated(),
                    ['author' => auth()->user()->id]
                )
            );

            return redirect()
                ->route('appointments.show', $appointment)
                ->with('success', 'Consulta agendada com sucesso.');

        } catch (Throwable $e) {
            Log::error("Appointment store failed. Message: {$e->getMessage()}");

            return redirect()
                ->back()
                ->withInput()
                ->with('err', 'Appointment store failed.');
        }
    }
}
