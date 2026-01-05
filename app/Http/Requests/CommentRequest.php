<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Handle authorization in controller
    }

    public function rules()
    {
        return [
            'content' => 'required|string|max:1000|min:1',
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'Comment content is required',
            'content.max' => 'Comment cannot exceed 1000 characters',
            'content.min' => 'Comment must be at least 1 character',
        ];
    }
}
