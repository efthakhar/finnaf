<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Income\CreateIncomeRequest;
use App\Http\Requests\Income\UpdateIncomeRequest;
use App\Http\Resources\Income\IncomeResource;
use App\Models\Income;
use Exception;
use Illuminate\Http\Request;

class IncomeController extends Controller
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

        $incomes = Income::query();

        $incomes->when($title, function ($query, $title) {
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
                        ->where('category_type', 'income');
                });
            });

        $incomes = $incomes->orderBy($sort_column, $sort_order)->with('categories')->paginate($limit);

        return IncomeResource::collection($incomes);
    }

    public function show($income_id)
    {
        $income = Income::where('id', $income_id)->with('categories')->first();

        return new IncomeResource($income);
    }

    public function store(CreateIncomeRequest $request)
    {
        try {

            $income = new Income();
            $income->title = $request->validated('title');
            $income->date = $request->validated('date');
            $income->amount = $request->validated('amount');
            $income->description = $request->validated('description');

            $income->save();
            $income->categories()->attach($request->validated('categories'));

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'failed to create income item',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'income record created succesfully',
        ], 201);
    }

    public function update(UpdateIncomeRequest $request, $income_id)
    {

        try {

            $income = Income::find($income_id);

            $income->title = $request->validated('title');
            $income->date = $request->validated('date');
            $income->amount = $request->validated('amount');
            $income->description = $request->validated('description');

            $income->save();

            $income->categories()->detach();
            $income->categories()->attach($request->validated('categories'));

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'failed to update income',
                'error' => $e->getMessage(),
            ], 500);

        }

        return response()->json([
            'status' => 'success',
            'message' => 'income updated succesfully',
        ], 200);
    }

    public function delete($ids)
    {

        $ids = explode(',', $ids);

        try {
            foreach ($ids as $id) {
                $income = Income::where('id', $id)->first();
                $income->categories()->detach();
                $income->delete();
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'failed to delete income item',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'income item deleted succesfully',
        ], 204);
    }
}
