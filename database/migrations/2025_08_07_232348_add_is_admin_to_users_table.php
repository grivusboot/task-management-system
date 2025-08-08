<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('admin_type', ['none', 'application', 'project'])->default('none')->after('password');
            // Keep is_admin for backward compatibility, but it will be deprecated
            $table->boolean('is_admin')->default(false)->after('admin_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['admin_type', 'is_admin']);
        });
    }
};
