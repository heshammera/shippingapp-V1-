<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Add authorization logic here later (e.g., check 'users.create' permission)
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|exists:roles,name', // Validate against existing roles in DB
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'expires_days' => 'nullable|integer|min:1',
            'expires_lifetime' => 'nullable',
            'shipping_company_id' => 'nullable|exists:shipping_companies,id',
        ];
    }

    public function messages()
    {
        return [
            'role.exists' => 'الدور المختار غير صالح.',
            // Add more custom messages if needed
        ];
    }
}
