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
        // 1. Add tenant_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
        });

        // Set tenant_id for existing users
        $admins = DB::table('users')->where('role', 'Admin')->get();
        foreach ($admins as $admin) {
            DB::table('users')->where('id', $admin->id)->update(['tenant_id' => $admin->id]);
        }
        
        $firstAdminId = $admins->first()?->id ?? 1;
        $nonAdmins = DB::table('users')->where('role', '!=', 'Admin')->get();
        foreach ($nonAdmins as $user) {
            DB::table('users')->where('id', $user->id)->update(['tenant_id' => $firstAdminId]);
        }

        // 2. Add tenant_id to other main tables
        $tables = ['products', 'customers', 'suppliers', 'orders', 'expenses', 'reminders'];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            });
            // Assign existing records to the default tenant
            DB::table($tableName)->update(['tenant_id' => $firstAdminId]);
        }
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tenant_id');
        });

        $tables = ['products', 'customers', 'suppliers', 'orders', 'expenses', 'reminders'];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
    }
};
