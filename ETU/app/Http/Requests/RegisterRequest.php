<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'string|max:13',
            'login' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|integer|exists:roles,id',
            'password' => [
                'required',
                //https://medium.com/@muhammad-sanan/validating-complex-passwords-with-laravel-955e128545f1
                Password::min(10)
                    ->numbers()
                    ->letters()
            ]
        ];
    }
}
