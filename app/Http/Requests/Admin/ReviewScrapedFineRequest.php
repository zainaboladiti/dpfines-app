<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReviewScrapedFineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,rejected',
            'review_notes' => 'required|string|min:10|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either approved or rejected.',
            'review_notes.required' => 'Review notes are required.',
            'review_notes.min' => 'Review notes must be at least 10 characters.',
        ];
    }
}
