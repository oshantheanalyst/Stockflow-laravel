<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'Admin',
            'is_active' => true,
        ]);

        // Create sample staff user belonging to the admin's workspace
        User::create([
            'username' => 'staff',
            'password' => Hash::make('staff123'),
            'role' => 'User',
            'is_active' => true,
            'tenant_id' => $admin->id,
        ]);

        // Create sample products belonging to the admin's workspace
        $products = [
            ['product_code' => 'P001', 'name' => 'Rice 5kg', 'category' => 'Grains', 'unit' => 'Packs', 'buying_price' => 450, 'selling_price' => 520, 'current_stock' => 100, 'reorder_level' => 20, 'tenant_id' => $admin->id],
            ['product_code' => 'P002', 'name' => 'Sugar 1kg', 'category' => 'Essentials', 'unit' => 'Packs', 'buying_price' => 120, 'selling_price' => 150, 'current_stock' => 200, 'reorder_level' => 50, 'tenant_id' => $admin->id],
            ['product_code' => 'P003', 'name' => 'Cooking Oil 1L', 'category' => 'Oils', 'unit' => 'Bottles', 'buying_price' => 380, 'selling_price' => 450, 'current_stock' => 80, 'reorder_level' => 15, 'tenant_id' => $admin->id],
            ['product_code' => 'P004', 'name' => 'Flour 1kg', 'category' => 'Grains', 'unit' => 'Packs', 'buying_price' => 95, 'selling_price' => 130, 'current_stock' => 150, 'reorder_level' => 30, 'tenant_id' => $admin->id],
            ['product_code' => 'P005', 'name' => 'Tea Leaves 200g', 'category' => 'Beverages', 'unit' => 'Packs', 'buying_price' => 280, 'selling_price' => 350, 'current_stock' => 60, 'reorder_level' => 10, 'tenant_id' => $admin->id],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
