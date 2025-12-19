<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductService
{

    private const CACHE_DURATION = 60; // in minutes

    private $productsPerPage = 10;
    public function getPaginatedProducts($page = 1, $perPage = 10)
    {
        $this->productsPerPage = $perPage;
        $cacheKey = "products_page_{$page}_perpage_{$this->productsPerPage}";

        // Check if products are cached
        $cachedProducts = Cache::get($cacheKey);
        if ($cachedProducts) {
            return $cachedProducts;
        }

        $products = Product::with('media')->paginate($perPage);

        $FormattedProducts = $products->map(function ($product) {
            return $this->prepareProductData($product);
        });
        $response = [
            'data' => $FormattedProducts,
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'next_page_url' => $products->nextPageUrl(),
            'prev_page_url' => $products->previousPageUrl(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
        ];
        Cache::put($cacheKey, $response, now()->addMinutes(self::CACHE_DURATION));

        return $response;
    }

    public function getProductById($id)
    {
        $product = Product::with('media')->find($id);

        if (!$product) {
            return [
                'message' => 'Product not found',
            ];
        }
        return $this->prepareProductData($product);
    }

    public static function prepareProductData(Product $product)
    {
        return [
            'id' => $product->id,
            'title' => [
                'en' => $product->getTranslation('title', 'en'),
                'ar' => $product->getTranslation('title', 'ar'),
            ],
            'description' => [
                'en' => $product->getTranslation('description', 'en'),
                'ar' => $product->getTranslation('description', 'ar'),
            ],
            'price' => $product->price,
            'quantity' => $product->quantity,
            'image_url' => $product->image_url,
        ];
    }
}
