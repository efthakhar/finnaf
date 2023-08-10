<?php

namespace Database\Seeders;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public $expenses = [

        // August
        [
            'title' => 'Purchasing new headphone',
            'date' => '2023-08-2',
            'description' => '',
            'category' => 11,
            'amount' => 500,
        ],
        [
            'title' => 'Pay to house owner',
            'date' => '2023-08-3',
            'description' => '',
            'category' => 12,
            'amount' => 5000,
        ],
        [
            'title' => 'Paying tuition fee of university',
            'date' => '2023-08-8',
            'description' => '',
            'category' => 14,
            'amount' => 4000,
        ],
        [
            'title' => 'Paying class test exam fee',
            'date' => '2023-08-10',
            'description' => '',
            'category' => 15,
            'amount' => 2540,
        ],
        [
            'title' => 'Visiting zoo with family',
            'date' => '2023-08-12',
            'description' => '',
            'category' => 16,
            'amount' => 1600,
        ],
        [
            'title' => 'Purchasing snacks for party',
            'date' => '2023-08-17',
            'description' => '',
            'category' => 17,
            'amount' => 1500,
        ],
        [
            'title' => 'Purchasing new soccer ball',
            'date' => '2023-08-19',
            'description' => '',
            'category' => 18,
            'amount' => 750,
        ],
        [
            'title' => 'Renew software license',
            'date' => '2023-08-28',
            'description' => '',
            'category' => 20,
            'amount' => 4500,
        ],

    ];

    public function run(): void
    {
        $today = Carbon::now();
        $firstDayOfMonth = Carbon::now()->firstOfMonth();
        $count = 0;
        foreach ($this->expenses as $item) {
            $item['date'] = $firstDayOfMonth->copy()->addDays($count += 1);
            $expense = Expense::create($item);
            $expense->categories()->attach($item['category']);

        }

        $expense = Expense::create([
            'title' => 'Donate to Charity',
            'date' => $today,
            'description' => '',
            'category' => 22,
            'amount' => 2800,
        ]);
        $expense->categories()->attach(22);
    }
}
