<?php

namespace App\Http\Requests\Appointment;

use App\Helpers\Enums\AppointmentStatus;
use App\Helpers\Enums\UserRoles;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() &&
            (
                auth()->user()->role === UserRoles::Admin->value ||
                auth()->user()->role === UserRoles::Receptionist->value ||
                auth()->user()->role === UserRoles::Doctor->value
            );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'doctor' => $this->user()->role === UserRoles::Doctor->value
                ? ['prohibited']
                : 'required',
            'uuid',
            Rule::exists('users', 'id')->where(fn ($q) => $q
                ->where('role', UserRoles::Doctor->value)
                ->whereNotNUll('email_verified_at')
                ->whereNull('deleted_at')
            ),
            'situation' => [
                'required',
                'string',
                'min:10',
                'max:150',
            ],
            'scheduled_at' => [
                'required',
                'date',
                'after:today',
            ],
            'status' => [
                'string',
                Rule::in(array_column(AppointmentStatus::cases(), 'value')),
            ],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->user()->role === UserRoles::Doctor->value) {
            $this->request->remove('doctor');
        }
    }
}
