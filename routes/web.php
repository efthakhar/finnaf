<?php

use App\Http\Controllers\Api\Category\ExpenseCategoryController;
use App\Http\Controllers\Api\Category\IncomeCategoryController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\VisitorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Database\Seeders\DevDemo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
})->name('home');

// create demo site
Route::get('/demo', function (Request $reqest) {

    $seeder = new DevDemo();
    $seeder->run();

    return redirect('/login');
});

// All Auth Related Route
Route::get('/register', [RegisterController::class, 'registrationForm'])->name('registrationForm');
Route::post('/register', [RegisterController::class, 'register'])->name('registerUser')->middleware(['viewonly']);
Route::get('/login', [LoginController::class, 'loginForm'])->name('loginForm');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Handling All Admin Routes
Route::get('/admin/{any?}', function () {
    if (Auth::check()) {
        return view('admin.app');
    }

    return redirect('login');
})->where('any', '.*')->name('admin');

// All route available only for authenticated users
Route::group(['middleware' => ['auth']], function () {

    // User
    Route::get('/api/users/authenticated-user', [UserController::class, 'getAuthenticatedUser']);

    // income category
    Route::get('/api/income-categories/list', [IncomeCategoryController::class, 'getIncomeCategoryList']);
    Route::get('/api/income-categories', [IncomeCategoryController::class, 'index']);
    Route::get('/api/income-categories/{id}', [IncomeCategoryController::class, 'show']);
    Route::post('/api/income-categories', [IncomeCategoryController::class, 'store']);
    Route::put('/api/income-categories/{id}', [IncomeCategoryController::class, 'update']);
    Route::delete('/api/income-categories/{id}', [IncomeCategoryController::class, 'delete']);

    // income
    Route::get('/api/incomes', [IncomeController::class, 'index']);
    Route::get('/api/incomes/{id}', [IncomeController::class, 'show']);
    Route::post('/api/incomes', [IncomeController::class, 'store']);
    Route::put('/api/incomes/{id}', [IncomeController::class, 'update']);
    Route::delete('/api/incomes/{id}', [IncomeController::class, 'delete']);

    // expense category
    Route::get('/api/expense-categories/list', [ExpenseCategoryController::class, 'getExpenseCategoryList']);
    Route::get('/api/expense-categories', [ExpenseCategoryController::class, 'index']);
    Route::get('/api/expense-categories/{id}', [ExpenseCategoryController::class, 'show']);
    Route::post('/api/expense-categories', [ExpenseCategoryController::class, 'store']);
    Route::put('/api/expense-categories/{id}', [ExpenseCategoryController::class, 'update']);
    Route::delete('/api/expense-categories/{id}', [ExpenseCategoryController::class, 'delete']);

    // expense
    Route::get('/api/expenses', [ExpenseController::class, 'index']);
    Route::get('/api/expenses/{id}', [ExpenseController::class, 'show']);
    Route::post('/api/expenses', [ExpenseController::class, 'store']);
    Route::put('/api/expenses/{id}', [ExpenseController::class, 'update']);
    Route::delete('/api/expenses/{id}', [ExpenseController::class, 'delete']);

    // Dashboard Reports
    Route::get('/api/dashboard-reports', [ReportController::class, 'getDashBoardReports']);

    // Demo Site Visitors Data
    Route::get('/api/visitors', [VisitorController::class, 'index']);

});
