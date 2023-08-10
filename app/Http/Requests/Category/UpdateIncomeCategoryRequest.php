<?php

namespace App\Http\Requests\Category;

use App\Rules\CombineUnique;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateIncomeCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => [
                'string', 'required',
                new CombineUnique(
                    [
                        'name' => $this->name,
                        'category_type' => 'income',
                    ],
                    'categories',
                    'category must be unique',
                    $id
                ),
            ],

        ];
    }
}
