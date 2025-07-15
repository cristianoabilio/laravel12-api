<?php

namespace App\Http\Requests;

use App\Http\Requests\Api\FormRequest;

class StoreCommentValidator extends FormRequest
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
            'post_id' => 'required|integer|exists:blog_posts,id',
            'content' => 'required'
        ];
    }
}
