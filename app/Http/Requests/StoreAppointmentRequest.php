<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

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
            'customer' => 'required|exists:users,id',
            'doctor' => 'required|exists:users,id',
            'animal' => 'required|string|exists:animals,id',
            'situation' => 'required|string|min:5|max:200',
            'scheduled_at' => 'required|date',
            'status' => 'required|string',
        ];
    }
}
