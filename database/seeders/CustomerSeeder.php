<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::count() === 0) {
            $this->command->warn('⚠️ Tidak ada produk di tabel products. Seeder dibatalkan.');
            return;
        }

        // Buat 10 customer dengan waktu berbeda
        $customers = collect();
        for ($i = 0; $i < 10; $i++) {
            $timestamp = now()->subDays(10 - $i);
            $customers->push(Customer::create([
                'name' => fake()->name(),
                'phone_number' => fake()->phoneNumber(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]));
        }

        // Buat 20 order acak
        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random();
            $timestamp = now()->subHours(rand(1, 240));

            $order = Order::create([
                'customer_id' => $customer->id,
                'total_amount' => 0, // akan diupdate
                'status' => fake()->randomElement(['pending', 'processing', 'ready', 'completed', 'cancelled']),
                'payment_status' => fake()->randomElement(['unpaid', 'paid']),
                'payment_method' => fake()->randomElement(['cash', 'credit_card', 'transfer']),
                'notes' => fake()->optional()->sentence(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            $products = Product::inRandomOrder()->take(rand(1, 3))->get();
            $total = 0;

            foreach ($products as $product) {
                $quantity = rand(1, 5);
                $unitPrice = $product->price ?? rand(10000, 100000);
                $subtotal = $quantity * $unitPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'special_instructions' => fake()->optional()->sentence(),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $total += $subtotal;
            }

            $order->update(['total_amount' => $total]);
        }
    }
}
