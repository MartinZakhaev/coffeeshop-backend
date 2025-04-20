<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::query();
        
        // Filter by availability
        if ($request->has('available')) {
            $query->where('is_available', $request->boolean('available'));
        }
        
        $products = $query->get();
        return ProductResource::collection($products);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }
    
    /**
     * Get products by category.
     */
    public function byCategory(Category $category): AnonymousResourceCollection
    {
        $products = $category->products()->where('is_available', true)->get();
        return ProductResource::collection($products);
    }
}