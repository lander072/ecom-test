<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * Get all products
     */
    public function index(): JsonResponse
    {
        $products = Cache::remember('products_all', 300, function () {
            return Product::all();
        });

        return response()->json([
            'data' => $products
        ]);
    }

    /**
     * Get single product by ID
     */
    public function show(int $id): JsonResponse
    {
        $product = Cache::remember("product_{$id}", 300, function () use ($id) {
            return Product::find($id);
        });

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'data' => $product
        ]);
    }
}
