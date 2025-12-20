<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

use function Illuminate\Support\now;

class CartService
{
    private const CACHE_KEY = "cart_user_";

    public function addToCart(array $items, $userId)
    {
        $cacheKey = self::CACHE_KEY . $userId;
        $addedItems = [];
        // Solve N+1 problem
        $productsIds = collect($items)->pluck('product_id')->toArray();
        $products = Product::whereIn('id', $productsIds)
            ->get()->keyBy('id');

        foreach ($items as $item) {
            $product = $products[$item['product_id']];

            if ($item['quantity'] > $product->quantity) {
                return [
                    'error' => "Insufficient stock for product {$product->title}. Avalailable: {$product->quantity}"
                ];
            }

            // Add or update cart
            $cartItem = Cart::where('user_id', $userId)
                ->where('product_id', $product->id)
                ->first();
            if (!$cartItem) {
                $cartItem = Cart::create([
                    'user_id' => $userId,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity']
                ]);
            } else {
                $cartItem->increment('quantity', $item['quantity']);
            }
            $addedItems[] = $cartItem;
        }

        // Invalidate cache
        Cache::forget($cacheKey);

        return $addedItems;
    }


    public function getCartItems($userId): array
    {
        // Check cache
        $cacheKey = self::CACHE_KEY . $userId;
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $cartItems = Cart::with('product.media')
            ->where('user_id', $userId)
            ->get();
        if ($cartItems->isEmpty()) {
            return ['error' => 'Cart is empty.'];
        }

        $formattedItems = [];
        $total = 0;
        foreach ($cartItems as $cartItem) {
            $formattedItems[] = [
                'id' => $cartItem->id,
                'cart_quantity' => $cartItem->quantity,
                'product' => ProductService::prepareProductData($cartItem->product),
            ];

            // Calculate total price
            $total_for_item = $cartItem->quantity * $cartItem->product->price;
            $total += $total_for_item;
        }
        $formattedItems['total_cart_price'] = round($total, 2);

        Cache::put($cacheKey, $formattedItems, now()->addMinutes(30));
        return $formattedItems;
    }

    public function removeItemFromCart($userId, $productId)
    {
        $cartItem = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
        if (!$cartItem) {
            return [
                'error' => 'Item not found in cart.'
            ];
        }
        $cartItem->delete();
        Cache::forget(self::CACHE_KEY . $userId);

        return [
            'message' => 'Item removed from cart successfully.'
        ];
    }

    public function emptyCart($userId)
    {
        Cart::where('user_id', $userId)->delete();
        Cache::forget(self::CACHE_KEY . $userId);
    }
}
