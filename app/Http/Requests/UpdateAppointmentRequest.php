<?php

namespace App\Http\Requests;

use App\Helpers\Enums\UserRoles;
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
            'doctor' => 'string|exists:users,id',
            'situation' => 'required|string',
            'scheduled_at' => 'required|date',
            'status' => 'required|string',
        ];
    }
}
