<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'title' => ['string', 'required', 'max:100'],
            'amount' => ['numeric', 'required'],
            'date' => ['date', 'required'],
            'categories' => ['required'],
            'description' => ['string', 'nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'categories.required' => 'Please select at least one category',
        ];
    }
}
