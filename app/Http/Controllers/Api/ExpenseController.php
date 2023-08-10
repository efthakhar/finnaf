<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\CreateExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Http\Resources\Expense\ExpenseResource;
use App\Models\Expense;
use Exception;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->query('limit') && $request->query('limit') < 100 ? $request->query('limit') : 10;

        $sort_column = $request->query('sort_column', 'id');
        $sort_order = $request->query('sort_order', 'desc');

        $title = $request->query('title');

        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');

        $start_amount = $request->query('start_amount');
        $end_amount = $request->query('end_amount');

        $category_id = $request->query('category');

        $expenses = Expense::query();

        $expenses->when($title, function ($query, $title) {
            $query->where('title', 'LIKE', '%'.$title.'%');
        })
            ->when($start_date, function ($query, $start_date) {
                $query->whereDate('date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                $query->whereDate('date', '<=', $end_date);
            })
            ->when($start_amount, function ($query, $start_amount) {
                $query->where('amount', '>=', $start_amount);
            })
            ->when($end_amount, function ($query, $end_amount) {
                $query->where('amount', '<=', $end_amount);
            })
            ->when($category_id, function ($query, $category_id) {
                $query->whereHas('categories', function ($query) use ($category_id) {
                    $query->where('category_id', $category_id)
                        ->where('category_type', 'expense');
                });
            });

        $expenses = $expenses->orderBy($sort_column, $sort_order)->with('categories')->paginate($limit);

        return ExpenseResource::collection($expenses);
    }

    public function show($expense_id)
    {
        $expense = Expense::where('id', $expense_id)->with('categories')->first();

        return new ExpenseResource($expense);
    }

    public function store(CreateExpenseRequest $request)
    {
        try {

            $expense = new Expense();
            $expense->title = $request->validated('title');
            $expense->date = $request->validated('date');
            $expense->amount = $request->validated('amount');
            $expense->description = $request->validated('description');

            $expense->save();
            $expense->categories()->attach($request->validated('categories'));

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'failed to create expense item',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'expense record created succesfully',
        ], 201);
    }

    public function update(UpdateExpenseRequest $request, $expense_id)
    {

        try {

            $expense = Expense::find($expense_id);

            $expense->title = $request->validated('title');
            $expense->date = $request->validated('date');
            $expense->amount = $request->validated('amount');
            $expense->description = $request->validated('description');

            $expense->save();

            $expense->categories()->detach();
            $expense->categories()->attach($request->validated('categories'));

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'failed to update expense',
                'error' => $e->getMessage(),
            ], 500);

        }

        return response()->json([
            'status' => 'success',
            'message' => 'expense updated succesfully',
        ], 200);
    }

    public function delete($ids)
    {

        $ids = explode(',', $ids);

        try {
            foreach ($ids as $id) {
                $expense = Expense::where('id', $id)->first();
                $expense->categories()->detach();
                $expense->delete();
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'failed to delete expense item',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'expense item deleted succesfully',
        ], 204);
    }
}
