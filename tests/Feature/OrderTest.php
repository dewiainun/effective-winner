<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_create_order_with_active_product()
    {
        $product = Product::factory()->create([
            'price' => 100000,
            'status' => 'active'
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Budi',
            'customer_email' => 'budi@mail.com',
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 2
                ]
            ]
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.total_price', 200000);

        $this->assertDatabaseHas('orders', ['customer_email' => 'budi@mail.com', 'total_price' => 200000]);
        $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'qty' => 2, 'subtotal' => 200000]);
    }

    public function test_cannot_create_order_with_inactive_product()
    {
        $product = Product::factory()->create([
            'status' => 'inactive'
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Budi',
            'customer_email' => 'budi@mail.com',
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 1
                ]
            ]
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('errors.items.0', "Product '{$product->name}' is not active and cannot be ordered.");
    }

    public function test_only_admin_can_view_orders()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('admin')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/orders');

        $response->assertStatus(200);
    }

    public function test_user_cannot_view_orders()
    {
        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('user')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/orders');

        $response->assertStatus(403);
    }
}
