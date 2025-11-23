<?php

namespace App\Http\Requests;

use App\Helpers\Enums\UserRoles;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
      return auth()->check() && $this->user()->hasAnyRole(['customer', 'receptionist', 'doctor']);
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
            'animal_id' => 'required|string|exists:animals,id',
            'situation' => 'required|string|min:5|max:200',
            'schedule_at' => 'required|date'
        ];
    }
}
