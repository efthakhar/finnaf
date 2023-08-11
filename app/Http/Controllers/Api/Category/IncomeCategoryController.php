<?php

namespace App\Http\Controllers\Api\Category;

use App\Exceptions\CategoryHasChildException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateIncomeCategoryRequest;
use App\Http\Requests\Category\UpdateIncomeCategoryRequest;
use App\Http\Resources\Category\CategoryDropdownResource;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class IncomeCategoryController extends Controller
{
    public function getIncomeCategoryList()
    {
        return CategoryDropdownResource::collection(Category::where('category_type', 'income')->get());
    }

    public function index(Request $request)
    {
        $limit = $request->query('limit') && $request->query('limit') < 100 ? $request->query('limit') : 10;

        $name = $request->query('name');

        $income_cats = Category::query();

        $income_cats->when($name, function ($query, $name) {
            $query->where('name', 'LIKE', '%'.$name.'%');
        });

        $income_cats = $income_cats->where('category_type', 'income')->paginate($limit);

        return CategoryResource::collection($income_cats);
    }

    public function show($category_id)
    {
        $category = Category::where('id', $category_id)->where('category_type', 'income')->first();

        return new CategoryResource($category);
    }

    public function store(CreateIncomeCategoryRequest $request)
    {
        try {

            $income_cat = new Category();
            $income_cat->name = $request->validated('name');
            $income_cat->category_type = 'income';
            $income_cat->save();

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'failed to create income category',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'income category record created succesfully',
        ], 201);
    }

    public function update(UpdateIncomeCategoryRequest $request, $category_id)
    {

        try {

            $income_cat = Category::where('category_type', 'income')->where('id', $category_id)->first();
            $income_cat->name = $request->validated('name');
            $income_cat->save();

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'failed to update income category',
                'error' => $e->getMessage(),
            ], 500);

        }

        return response()->json([
            'status' => 'success',
            'message' => 'income category updated succesfully',
        ], 200);
    }

    public function delete($ids)
    {
        $ids = explode(',', $ids);

        try {
            foreach ($ids as $id) {

                $category = Category::where('category_type', 'income')->where('id', $id)->first();

                if ($category->incomes()->count() > 0) {
                    throw new CategoryHasChildException();
                } else {
                    $category->delete();
                }
            }
        } catch (CategoryHasChildException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'category has non zero income records.',
                'error_type' => 'HAS_CHILD_ERROR',
            ], 500);

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'failed to delete income category',
                'error' => $e->getMessage(),
            ], 500);

        }

        return response()->json([
            'status' => 'success',
            'message' => 'income category deleted succesfully',
        ], 204);
    }
}
