<?php

namespace App\Http\Requests;

use App\Helpers\Enums\AppointmentStatus;
use App\Helpers\Enums\UserRoles;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() &&
            (
                auth()->user()->role === UserRoles::Admin->value || auth()->user()->role === UserRoles::Receptionist->value || auth()->user()->role === UserRoles::Doctor->value
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
            'doctor' => [
                'required',
                'string',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', UserRoles::Doctor->value)
                        ->whereNotNull('email_verified_at')
                        ->whereNull('deleted_at');
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
                'after:today'
            ]
        ];
    }
}
