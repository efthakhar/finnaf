<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public $categories = [

        // incomes
        ['id' => 1, 'category_type' => 'income', 'name' => 'job'],
        ['id' => 2, 'category_type' => 'income', 'name' => 'freelance'],
        ['id' => 3, 'category_type' => 'income', 'name' => 'overtime'],
        ['id' => 4, 'category_type' => 'income', 'name' => 'mentorship'],
        ['id' => 5, 'category_type' => 'income', 'name' => 'invest profit'],
        ['id' => 6, 'category_type' => 'income', 'name' => 'old book sale'],
        ['id' => 7, 'category_type' => 'income', 'name' => 'consultancy'],
        ['id' => 8, 'category_type' => 'income', 'name' => 'envato item sale'],
        ['id' => 9, 'category_type' => 'income', 'name' => 'content writing'],
        ['id' => 10, 'category_type' => 'income', 'name' => 'old gadget sale'],

        // expense
        ['id' => 11, 'category_type' => 'expense', 'name' => 'gadget purchase'],
        ['id' => 12, 'category_type' => 'expense', 'name' => 'house rental'],
        ['id' => 13, 'category_type' => 'expense', 'name' => 'car rental'],
        ['id' => 14, 'category_type' => 'expense', 'name' => 'tution fee'],
        ['id' => 15, 'category_type' => 'expense', 'name' => 'exam fee'],
        ['id' => 16, 'category_type' => 'expense', 'name' => 'travel'],
        ['id' => 17, 'category_type' => 'expense', 'name' => 'food purchase'],
        ['id' => 18, 'category_type' => 'expense', 'name' => 'refreshment'],
        ['id' => 19, 'category_type' => 'expense', 'name' => 'gift'],
        ['id' => 20, 'category_type' => 'expense', 'name' => 'buying software'],
        ['id' => 21, 'category_type' => 'expense', 'name' => 'investment'],
        ['id' => 22, 'category_type' => 'expense', 'name' => 'donation'],

        // income
        ['id' => 23, 'category_type' => 'income', 'name' => 'bonus'],

    ];

    public function run(): void
    {
        foreach ($this->categories as $category) {
            Category::create($category);
        }
    }
}
