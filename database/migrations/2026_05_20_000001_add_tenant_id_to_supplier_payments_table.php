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
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
        });

        // Set default tenant_id to first admin
        $firstAdmin = DB::table('users')->where('role', 'Admin')->first();
        $defaultTenantId = $firstAdmin?->id ?? 1;

        DB::table('supplier_payments')->update(['tenant_id' => $defaultTenantId]);
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropColumn('tenant_id');
        });
    }
};
