<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}
    public function add(CartRequest $request)
    {
        $validated = $request->validated();
        $response = $this->cartService->addToCart($validated['items'], $request->user()->id);

        if (isset($response['error'])) {
            return response()->json(['message' => $response['error']], 422);
        }
        return response()->json([
            'message' => 'Items added to cart successfully.',
            'cart' => $response
        ], 201);
    }

    public function view(Request $request)
    {
        $cartItems = $this->cartService->getCartItems($request->user()->id);
        return response()->json([
            'message' => 'Cart retrieved successfully.',
            'cart' => $cartItems
        ], 200);
    }

    public function remove(Request $request, $product_id)
    {
        $response = $this->cartService->removeItemFromCart($request->user()->id, $product_id);
        if (isset($response['error'])) {
            return response()->json(['message' => $response['error']], 422);
        }
        return response()->json($response, 200);
    }
}
