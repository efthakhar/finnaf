<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getDashBoardReports()
    {
        $total_incomes = Income::sum('amount');
        $total_expenses = Expense::sum('amount');
        $net_incomes = $total_incomes - $total_expenses;

        // $currentMonthTotalincomes = Income::whereDate('date', '>=', Carbon::now()->firstOfMonth())
        // ->whereDate('date', '<=', Carbon::now()->lastOfMonth())
        // ->sum('amount');

        $today_incomes = Income::whereDate('date', '=', Carbon::now())
            ->sum('amount');

        $current_month_incomes = Income::whereDate('date', '>=', Carbon::now()->firstOfMonth())
            ->whereDate('date', '<=', Carbon::now()->lastOfMonth())
            ->select([DB::raw('SUM(amount) as amount'), 'date'])
            ->groupBy('date')->orderBy('date')
            ->get();

        $current_month_expenses = Expense::whereDate('date', '>=', Carbon::now()->firstOfMonth())
            ->whereDate('date', '<=', Carbon::now()->lastOfMonth())
            ->select([DB::raw('SUM(amount) as amount'), 'date'])
            ->groupBy('date')->orderBy('date')
            ->get();

        $incomeCatData = Category::where('category_type', 'income')->withCount('incomes')->get();
        $expenseCatData = Category::where('category_type', 'expense')->withCount('expenses')->get();

        return response()->json([
            // 'currentMonthTotalincomes' => round($currentMonthTotalincomes, 2),
            'total_incomes' => round($total_incomes, 2),
            'total_expenses' => round($total_expenses, 2),
            'net_incomes' => round($net_incomes, 2),
            'today_incomes' => round($today_incomes, 2),
            'current_month_incomes' => $current_month_incomes,
            'current_month_expenses' => $current_month_expenses,
            'incomeCatData' => $incomeCatData,
            'expenseCatData' => $expenseCatData,
        ], 200);
    }
}
