<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'content' => 'required_without:file|string|max:5000',
            'type' => 'required|in:text,image,file,audio,video',
            'file' => 'nullable|file|max:10240', // 10MB max
            'file_url' => 'nullable|url',
            'file_name' => 'nullable|string|max:255',
            'file_size' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'content.required_without' => 'Message content is required when no file is uploaded.',
            'type.in' => 'Message type must be one of: text, image, file, audio, video.',
            'file.max' => 'File size must not exceed 10MB.',
            'file_url.url' => 'File URL must be a valid URL.',
        ];
    }
}
