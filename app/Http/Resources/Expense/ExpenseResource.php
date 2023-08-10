<?php

namespace App\Http\Resources\Expense;

use App\Http\Resources\Category\CategoryDropdownResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'date' => $this->date,
            'amount' => $this->amount,
            'description' => $this->description,
            'categories' => CategoryDropdownResource::collection($this->categories),
        ];
    }
}
