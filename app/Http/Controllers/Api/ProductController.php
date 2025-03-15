<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\ProductStoreRequest;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::all();
        return response()->json($products, Response::HTTP_OK);
    }

    public function store(ProductStoreRequest $request)
    {
        $product = Product::create($request->validated());
        return response()->json($product, Response::HTTP_CREATED);
    }

    public function show(Product $product)
    {
        return response()->json($product, Response::HTTP_OK);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
            'message' => 'Продукт успешно удалена'
        ], Response::HTTP_OK);
    }
}
