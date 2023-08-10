<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DevDemo extends Seeder
{
    // prepare the demo database for development and demosite

    public function run()
    {

        Cache::flush();
        Schema::disableForeignKeyConstraints();

        // truncate database tables
        DB::table('users')->truncate();
        DB::table('categories')->truncate();
        DB::table('categorizables')->truncate();
        DB::table('incomes')->truncate();
        DB::table('expenses')->truncate();

        // seed data
        $this->call([
            DemoUserSeeder::class,
            CategorySeeder::class,
            IncomeSeeder::class,
            ExpenseSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
