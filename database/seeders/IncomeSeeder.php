<?php

namespace Database\Seeders;

use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class IncomeSeeder extends Seeder
{
    public $incomes = [

        [
            'title' => 'Get salary from office',
            'date' => '2023-08-03',
            'description' => 'Salary collected from account section',
            'category' => 1,
            'amount' => 10000,
        ],
        [
            'title' => 'Payment for freelance design project',
            'date' => '2023-08-08',
            'description' => '',
            'category' => 2,
            'amount' => 6000,
        ],
        [
            'title' => 'Commission for referring new clients',
            'date' => '2023-08-15',
            'description' => '',
            'category' => 3,
            'amount' => 2000,
        ],
        [
            'title' => 'Get Bonus from Boss',
            'date' => '2023-08-15',
            'description' => '',
            'category' => 23,
            'amount' => 1000,
        ],
        [
            'title' => 'Sale of e-book on programming',
            'date' => '2023-08-20',
            'description' => '',
            'category' => 4,
            'amount' => 3500,
        ],
        [
            'title' => 'Mentoring Junior Coders',
            'date' => '2023-08-10',
            'description' => '',
            'category' => 4,
            'amount' => 5350,
        ],
        [
            'title' => 'Dividend payment from investment',
            'date' => '2023-08-25',
            'description' => '',
            'category' => 5,
            'amount' => 2080,
        ],
        [
            'title' => 'Article writing for tech blog',
            'date' => '2023-08-28',
            'description' => '',
            'category' => 9,
            'amount' => 5060,
        ],

    ];

    public function run(): void
    {
        $today = Carbon::now();
        $firstDayOfMonth = Carbon::now()->firstOfMonth();
        $count = 0;
        foreach ($this->incomes as $item) {
            $item['date'] = $firstDayOfMonth->copy()->addDays($count += 1);
            $income = Income::create($item);
            $income->categories()->attach($item['category']);

        }

        $income = Income::create([
            'title' => 'Web development  fee',
            'date' => $today,
            'description' => '',
            'category' => 7,
            'amount' => 7351,
        ]);
        $income->categories()->attach(7);
    }
}
