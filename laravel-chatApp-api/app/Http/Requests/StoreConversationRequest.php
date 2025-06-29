<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'type' => 'required|in:private,group',
            'description' => 'nullable|string|max:1000',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.in' => 'Conversation type must be either private or group.',
            'user_ids.required' => 'At least one user must be selected.',
            'user_ids.min' => 'At least one user must be selected.',
            'user_ids.*.exists' => 'One or more selected users do not exist.',
        ];
    }
}
