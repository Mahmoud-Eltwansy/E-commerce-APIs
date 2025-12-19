<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}
    public function index()
    {

        $perPage = request()->query('per_page', 10);
        $page = request()->query('page', 1);

        $response = $this->productService->getPaginatedProducts($page, $perPage);

        return response()->json($response);
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);

        if (!isset($product['price'])) {
            return response()->json($product, 404);
        }

        return response()->json($product);
    }
}
