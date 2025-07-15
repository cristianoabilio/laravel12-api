<?php

namespace App\Http\Requests;

use App\Http\Requests\Api\FormRequest;

class StoreBlogPostValidator extends FormRequest
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
            'user_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'nullable|image|max:2048',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'meta_keywords' => 'required',
        ];
    }
}
