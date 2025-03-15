<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Category\CategoryStoreRequest;
use App\Http\Requests\Api\Category\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Response;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::all();
        return response()->json($categories, Response::HTTP_OK);
    }

    public function store(CategoryStoreRequest $request)
    {
        $category = Category::create($request->validated());
        return response()->json($category, Response::HTTP_CREATED);
    }

    public function show(Category $category)
    {
        return response()->json($category, Response::HTTP_OK);
    }

    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $category->update($request->validated());
        return response()->json($category, Response::HTTP_OK);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([
            'message' => 'Категория успешно удалена'
        ], Response::HTTP_OK);
    }
}
