<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shops = Shop::all();

        return response()->json($shops, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:shops,name',
            'url' => 'required|url|unique:shops,url',
        ]);

        $shop = Shop::create($data);


        return response()->json($shop, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shop = Shop::find($id);

        if (!$shop) {
            return response()->json([
                'message' => 'Магазин не найден'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($shop, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shop = Shop::find($id);

        if (!$shop) {
            return response()->json([
                'message' => 'Магазин не найден'
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:shops,name,' . $shop->id,
            'url' => 'url|unique:shops,url',
        ]);

        $shop->update($data);

        return response()->json($shop, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shop = Shop::find($id);

        if (!$shop) {
            return response()->json([
                'message' => 'Магазин не найден'
            ], Response::HTTP_NOT_FOUND);
        }

        $shop->delete();

        return response()->json([
            'message' => 'Магазин успешно удален'
        ], Response::HTTP_OK);
    }
}
