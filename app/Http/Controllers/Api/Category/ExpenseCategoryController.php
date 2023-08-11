<?php

namespace App\Http\Controllers\Api\Category;

use App\Exceptions\CategoryHasChildException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateExpenseCategoryRequest;
use App\Http\Requests\Category\UpdateExpenseCategoryRequest;
use App\Http\Resources\Category\CategoryDropdownResource;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function getExpenseCategoryList()
    {
        return CategoryDropdownResource::collection(Category::where('category_type', 'expense')->get());
    }

    public function index(Request $request)
    {
        $limit = $request->query('limit') && $request->query('limit') < 100 ? $request->query('limit') : 10;

        $name = $request->query('name');

        $expense_cats = Category::query();

        $expense_cats->when($name, function ($query, $name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        });

        $expense_cats = $expense_cats->where('category_type', 'expense')->paginate($limit);

        return CategoryResource::collection($expense_cats);
    }

    public function show($category_id)
    {
        $category = Category::where('id', $category_id)->where('category_type', 'expense')->first();

        return new CategoryResource($category);
    }

    public function store(CreateExpenseCategoryRequest $request)
    {
        try {

            $expense_cat = new Category();
            $expense_cat->name = $request->validated('name');
            $expense_cat->category_type = 'expense';
            $expense_cat->save();

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'failed to create expense category',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'expense category record created succesfully',
        ], 201);
    }

    public function update(UpdateExpenseCategoryRequest $request, $category_id)
    {

        try {

            $expense_cat = Category::where('category_type', 'expense')->where('id', $category_id)->first();
            $expense_cat->name = $request->validated('name');
            $expense_cat->save();

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'failed to update expense category',
                'error' => $e->getMessage(),
            ], 500);

        }

        return response()->json([
            'status' => 'success',
            'message' => 'expense category updated succesfully',
        ], 200);
    }

    public function delete($ids)
    {
        $ids = explode(',', $ids);

        try {
            foreach ($ids as $id) {

                $category = Category::where('category_type', 'expense')->where('id', $id)->first();

                if ($category->expenses()->count() > 0) {

                    throw new CategoryHasChildException();
                } else {
                    $category->delete();
                }
            }
        } catch (CategoryHasChildException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'category has non zero expense records.',
                'error_type' => 'HAS_CHILD_ERROR',
            ], 500);

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'failed to delete expense category',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'expense category deleted succesfully',
        ], 204);
    }
}
