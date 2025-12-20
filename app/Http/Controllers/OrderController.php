<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $orders = $this->orderService->getUserOrders($user);
        return response()->json($orders);
    }
    public function create(Request $request)
    {
        $user = $request->user();
        $response = $this->orderService->createOrder($user);
        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 400);
        }
        return response()->json($response, 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $response = $this->orderService->getOrderById($user, $id);
        if (isset($response['error'])) {
            return response()->json($response, 404);
        }
        return response()->json($response);
    }
}
