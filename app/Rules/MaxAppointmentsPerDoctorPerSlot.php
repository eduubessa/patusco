<?php

namespace App\Rules;

use App\Helpers\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxAppointmentsPerDoctorPerSlot implements ValidationRule
{

    /**
     * @param int $maxAppointmentsPerDoctor Maximum appointments allowed per doctor in a slot
     * @param string $scheduledAt The scheduled datetime to check against
     */
    public function __construct(
        protected int $maxAppointmentsPerDoctor,
        protected string $scheduledAt
    ) {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        $appointmentCount = Appointment::scheduledForDoctor($value, Carbon::parse($this->scheduledAt))->count();

        if($appointmentCount >= $this->maxAppointmentsPerDoctor) {
            $fail("O veternário selecionado já não tem disponibilidade para essa hora!");
        }
    }
}
