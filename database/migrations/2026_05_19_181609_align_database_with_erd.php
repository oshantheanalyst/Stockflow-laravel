<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Run the migrations.
    public function up(): void
    {
        // 1. Create categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Category_ID
            $table->string('category_name'); // Category_name
            $table->timestamps();
        });

        // Populate categories from products
        $categories = DB::table('products')->distinct()->pluck('category')->filter();
        foreach ($categories as $cat) {
            DB::table('categories')->insert([
                'category_name' => $cat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Add category_id and new ERD fields to products table
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->decimal('price', 12, 2)->default(0); // Price
            $table->decimal('stock_level', 12, 2)->default(0); // Stock_level
        });

        // Map category_id and sync price/stock_level
        $categoriesDb = DB::table('categories')->get();
        foreach ($categoriesDb as $catDb) {
            DB::table('products')
                ->where('category', $catDb->category_name)
                ->update(['category_id' => $catDb->id]);
        }
        DB::statement('UPDATE products SET price = selling_price, stock_level = current_stock');

        // 3. Drop foreign keys referencing invoices table before renaming it
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });
        Schema::table('invoice_returns', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });

        // 4. Rename invoices to orders
        Schema::rename('invoices', 'orders');

        // 5. Add user_id and new ERD fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->date('date')->nullable();
            $table->string('status')->default('Pending');
            $table->string('order_type')->default('Sales');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        });

        // Sync date, status, and set default user_id
        DB::statement('UPDATE orders SET date = invoice_date, status = CASE WHEN is_paid = 1 THEN "Paid" ELSE "Unpaid" END');
        $firstUser = DB::table('users')->first();
        if ($firstUser) {
            DB::table('orders')->update(['user_id' => $firstUser->id]);
        }

        // 6. Rename invoice_items to order_items
        Schema::rename('invoice_items', 'order_items');

        // 7. Add ERD fields and restore foreign key on order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('invoice_id', 'order_id');
            $table->integer('quantity')->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
        });

        DB::statement('UPDATE order_items SET quantity = qty, subtotal = line_total');

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });

        // 8. Restore foreign key on invoice_returns
        Schema::table('invoice_returns', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('orders')->restrictOnDelete();
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        // Drop foreign keys
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
        Schema::table('invoice_returns', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
        });

        // Rename order_items back to invoice_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('order_id', 'invoice_id');
            $table->dropColumn(['quantity', 'subtotal']);
        });
        Schema::rename('order_items', 'invoice_items');

        // Rename orders back to invoices
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['date', 'status', 'order_type', 'user_id']);
        });
        Schema::rename('orders', 'invoices');

        // Restore foreign keys referencing invoices
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
        });
        Schema::table('invoice_returns', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('invoices')->restrictOnDelete();
        });

        // Remove categories and new product fields
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'price', 'stock_level']);
        });
        Schema::dropIfExists('categories');
    }
};
