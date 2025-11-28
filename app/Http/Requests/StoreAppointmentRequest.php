<?php

namespace App\Http\Requests;

use App\Helpers\Enums\AppointmentStatus;
use App\Helpers\Enums\UserRoles;
use App\Models\Appointment;
use App\Models\User;
use App\Rules\MaxAppointmentsPerDoctorPerSlot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
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
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', UserRoles::Customer->value)
                        ->whereNotNull('email_verified_at')
                        ->whereNull('deleted_at');
                }),
            ],
            'doctor' => [
                'required',
                'string',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', UserRoles::Doctor->value)
                        ->whereNotNull('email_verified_at')
                        ->whereNull('deleted_at');
                }),
                new MaxAppointmentsPerDoctorPerSlot(5, $this->scheduled_at)
            ],
            'animal' => [
                'required',
                'string',
                Rule::exists('animals', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            'situation' => [
                'required',
                'string',
                'min:10',
                'max:150'
            ],
            'scheduled_at' => [
                'required',
                'date',
                'date_format:Y-m-d H:i:s',
                'after:today'
            ],
            'status' => [
                'required',
                'string',
                Rule::in(array_column(AppointmentStatus::cases(), 'value'))
            ]
        ];
    }
}
