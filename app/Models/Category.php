<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_type'];

    public function incomes()
    {
        return $this->morphedByMany(Income::class, 'categorizable');
    }

    public function expenses()
    {
        return $this->morphedByMany(Expense::class, 'categorizable');
    }
}
