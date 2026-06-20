<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_view_products()
    {
        Product::factory()->create(['name' => 'Laptop']);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonPath('data.0.name', 'Laptop');
    }

    public function test_only_admin_can_create_product()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('admin')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/products', [
            'name' => 'Mouse',
            'price' => 150000,
            'status' => 'active',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'Mouse']);
    }

    public function test_user_cannot_create_product()
    {
        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('user')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/products', [
            'name' => 'Keyboard',
            'price' => 200000,
            'status' => 'active',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('products', ['name' => 'Keyboard']);
    }

    public function test_product_validation_fails()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('admin')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/products', [
            'price' => -100, // Invalid price
            'status' => 'unknown', // Invalid status
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors' => ['name', 'price', 'status']
                 ]);
    }
}
