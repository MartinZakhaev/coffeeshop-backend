<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Order::query();
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->latest()->get();
        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request): OrderResource
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.special_instructions' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            // Create order
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'total_amount' => 0, // Will calculate below
                'notes' => $validated['notes'] ?? null,
            ]);

            $totalAmount = 0;

            // Add order items
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ]);
            }

            // Update order total
            $order->update(['total_amount' => $totalAmount]);

            return new OrderResource($order->fresh(['items.product', 'customer']));
        });
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): OrderResource
    {
        return new OrderResource($order->load(['items.product', 'customer']));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order): OrderResource
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,processing,ready,completed,cancelled',
            'payment_status' => 'sometimes|in:unpaid,paid',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);
        return new OrderResource($order->fresh(['items.product', 'customer']));
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order): Response
    {
        // Only allow cancellation if order is pending
        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be deleted'], 403);
        }
        
        $order->delete();
        return response()->noContent();
    }
    
    /**
     * Get orders for a specific customer.
     */
    public function customerOrders(Customer $customer): AnonymousResourceCollection
    {
        $orders = $customer->orders()->latest()->get();
        return OrderResource::collection($orders);
    }
}