<?php

declare(strict_types=1);

namespace App\Http\Requests\Appointment;

use App\Helpers\Enums\AppointmentStatus;
use App\Helpers\Enums\UserRoles;
use App\Rules\MaxAppointmentsPerDoctorPerSlot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && $this->user()->hasAnyRole(['admin', 'customer', 'receptionist']);
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
            'customer' => [
                'required',
                'string',
                Rule::exists('users', 'username')->where(function ($query) {
                    $query->where('role', UserRoles::Customer->value)
                        ->whereNotNull('email_verified_at')
                        ->whereNull('deleted_at');
                }),
            ],
            'animal' => [
                'required',
                'string',
                Rule::exists('animals', 'slug')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            'doctor' => [
                'required',
                'string',
                Rule::exists('users', 'username')->where(function ($query) {
                    $query->where('role', UserRoles::Doctor->value)
                        ->whereNotNull('email_verified_at')
                        ->whereNull('deleted_at');
                }),
                new MaxAppointmentsPerDoctorPerSlot(3, $this->scheduled_at),
            ],
            'situation' => [
                'required',
                'string',
                'min:50',
                'max:250',
            ],
            'scheduled_at' => [
                'required',
                'date',
                'date_format:Y-m-d H:i:s',
                'after:today',
            ],
            'status' => [
                'required',
                'string',
                Rule::in(array_column(AppointmentStatus::cases(), 'value')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // Required field messages
            'customer.required' => 'The customer field is required.',
            'doctor.required' => 'The doctor field is required.',
            'animal.required' => 'The animal field is required.',
            'situation.required' => 'The situation field is required.',
            'scheduled_at.required' => 'The scheduled at field is required.',

            // Existence validation messages
            'customer.exists' => 'The selected customer is invalid or does not exist.',
            'doctor.exists' => 'The selected doctor is invalid or does not exist.',
            'animal.exists' => 'The selected animal is invalid or does not exist.',

            // Format and constraint messages
            'scheduled_at.date_format' => 'A data de agendamento não é válida.',

            'situation.string.min' => "A situação deve ter no mínimo' :min caracteres.",
            'situation.string.max' => "A situação deve ter no máximo' :max caracteres.",
        ];
    }
}
