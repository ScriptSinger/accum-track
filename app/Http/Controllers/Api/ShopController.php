<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Shop\ShopStoreRequest;
use App\Http\Requests\Api\Shop\ShopUpdateRequest;
use App\Models\Shop;
use Illuminate\Http\Response;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::all();
        return response()->json($shops, Response::HTTP_OK);
    }

    public function store(ShopStoreRequest $request)
    {
        $shop = Shop::create($request->validated());
        return response()->json($shop, Response::HTTP_CREATED);
    }

    public function show(Shop $shop)
    {
        return response()->json($shop, Response::HTTP_OK);
    }

    public function update(ShopUpdateRequest $request, Shop $shop)
    {
        $shop->update($request->validated());
        return response()->json($shop, Response::HTTP_OK);
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();
        return response()->json([
            'message' => 'Магазин успешно удален'
        ], Response::HTTP_OK);
    }
}
