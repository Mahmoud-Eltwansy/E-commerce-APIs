<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class OrderService
{
    public function __construct(private CartService $cartService, private ProductService $productService) {}
    public function createOrder($user)
    {
        $cartItems = $user->cartItems()->with('product')->get();
        // Check if cart is empty
        if ($cartItems->isEmpty()) {
            return ['error' => 'Cart is empty'];
        }


        return DB::transaction(function () use ($cartItems, $user) {
            $totalPrice = 0;
            $orderItems = [];
            $productsIds = $cartItems->pluck('product_id')->toArray();
            $products = Product::whereIn('id', $productsIds)->lockForUpdate()->get()->keyBy('id');

            // Validate stock and calculate total price
            foreach ($cartItems as $item) {
                $product = $products[$item->product_id];
                if ($product->quantity < $item->quantity) {
                    return ['error' => "Insufficient stock for product: {$product->title}"];
                }

                $totalPrice += $product->price * $item->quantity;

                $orderItems[] = [
                    'product_id' => $item->product_id,
                    'price' => $product->price,
                    'quantity' => $item->quantity
                ];

                // Deduct Product
                Product::where('id', $item->product_id)->decrement('quantity', $item->quantity);
            }

            // Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => round($totalPrice, 2),
                'status' => 'pending'
            ]);

            // Create OrderItems
            foreach ($orderItems as &$item) {
                $item['order_id'] = $order->id;
                $item['created_at'] = now();
                $item['updated_at'] = now();
            }
            unset($item);
            OrderItem::insert($orderItems);

            // Empty Cart
            $this->cartService->emptyCart($user->id);

            // Invalidate Product Cache
            $this->productService->invalidateProductsCache();

            return [
                'message' => 'Order Created Successfully',
                'data' => [
                    'order_id' => $order->id,
                    'total_price' => $order->total_price,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                ],
            ];
        });
    }

    public function getUserOrders($user)
    {
        $orders = Order::where('user_id', $user->id)
            ->get();

        $response = [
            'message' => 'Orders retrieved successfully',
            'data' => $orders->map(fn($order) => [
                'id' => $order->id,
                'total_price' => $order->total_price,
                'status' => $order->status,
                'created_at' => $order->create_at,
            ])
        ];
        return $response;
    }

    public function getOrderByID($user, $order_id)
    {
        $order = Order::where('id', $order_id)
            ->where('user_id', $user->id)
            ->with('orderItems.product.media')
            ->first();
        if (!$order) {
            return ['error' => 'Order Not found'];
        }

        $response = [
            'message' => 'Order retrieved successfully',
            'data' => [
                'id' => $order->id,
                'total_price' => $order->total_price,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'items' => $order->orderItems->map(fn($item) => [
                    'product_id' => $item->product_id,
                    'title' => [
                        'en' => $item->product->getTranslation('title', 'en'),
                        'ar' => $item->product->getTranslation('title', 'ar'),
                    ],
                    'description' => [
                        'en' => $item->product->getTranslation('description', 'en'),
                        'ar' => $item->product->getTranslation('description', 'ar'),
                    ],
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'image' => $item->product->image_url
                ])
            ]
        ];
        return $response;
    }
}
