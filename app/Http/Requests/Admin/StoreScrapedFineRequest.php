<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreScrapedFineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'organisation' => 'required|string|max:255|min:3',
            'regulator' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'fine_amount' => 'required|numeric|min:0|max:999999999999.99',
            'currency' => 'required|string|size:3|in:EUR,USD,GBP,AUD,CAD',
            'fine_date' => 'required|date|before_or_equal:today',
            'law' => 'nullable|string|max:255',
            'articles_breached' => 'nullable|string|max:1000',
            'violation_type' => 'nullable|string|max:255',
            'summary' => 'required|string|min:10|max:5000',
            'badges' => 'nullable|string|max:500',
            'link_to_case' => 'nullable|url|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'organisation.required' => 'Organisation name is required.',
            'organisation.min' => 'Organisation name must be at least 3 characters.',
            'fine_amount.required' => 'Fine amount is required.',
            'fine_amount.numeric' => 'Fine amount must be a valid number.',
            'fine_date.before_or_equal' => 'Fine date cannot be in the future.',
            'summary.min' => 'Summary must be at least 10 characters.',
            'link_to_case.url' => 'Link must be a valid URL.',
        ];
    }
}
