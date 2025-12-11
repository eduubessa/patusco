<?php

declare(strict_types=1);

namespace App\Http\Requests\Animal;

use App\Helpers\Enums\UserRoles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateAnimalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasVerifiedEmail() && (
            UserRoles::Receptionist ||
            UserRoles::Admin ||
            UserRoles::Doctor ||
            UserRoles::Customer);
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
            'name' => [
                'required',
                'string',
                'min:3',
                'max:30',
            ],
            'gender' => [
                'required',
                'string',
                'in:m,f',
            ],
            'birthday' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before:today',
            ],
            'species' => [
                'required',
                'string',
                'min:3',
                'max:50',
            ],
            'breed' => [
                'required',
                'string',
                'min:3',
                'max:50',
            ],
            'doctor' => [
                'nullable',
                'string',
                Rule::exists('users', 'username')->where(function ($query) {
                    $query->where('role', UserRoles::Doctor->value)
                        ->whereNotNull('email_verified_at')
                        ->whereNUll('deleted_at');
                }),
            ],
            'owner' => [
                'nullable',
                'string',
                Rule::exists('users', 'username')->where(function ($query) {
                    $query->where('role', UserRoles::Customer->value)
                        ->whereNotNull('email_verified_at')
                        ->whereNull('deleted_at');
                }),
            ],
        ];
    }
}
