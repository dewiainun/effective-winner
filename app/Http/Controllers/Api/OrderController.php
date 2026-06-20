<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('items.product')->get();
        return response()->json([
            'data' => OrderResource::collection($orders)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            $order = DB::transaction(function () use ($validated) {
                $order = Order::create([
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'status' => 'pending',
                    'total_price' => 0, // will calculate
                ]);

                $totalPrice = 0;

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    if ($product->status !== 'active') {
                        throw new \Exception("Product '{$product->name}' is not active and cannot be ordered.");
                    }

                    $price = $product->price;
                    $qty = $item['qty'];
                    $subtotal = $price * $qty;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);

                    $totalPrice += $subtotal;
                }

                $order->update(['total_price' => $totalPrice]);

                return $order->load('items.product');
            });

            return response()->json([
                'data' => new OrderResource($order)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create order',
                'errors' => [
                    'items' => [$e->getMessage()]
                ]
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        return response()->json([
            'data' => new OrderResource($order)
        ]);
    }
}