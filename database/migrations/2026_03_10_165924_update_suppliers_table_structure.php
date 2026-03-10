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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropIndex(['priority']);
            $table->dropColumn('priority');
            $table->dropColumn('shipping_policy');

            $table->decimal('estimated_shipping', 8, 2)->after('tax_policy')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('estimated_shipping');

            $table->unsignedInteger('priority')->default(100);
            $table->decimal('shipping_policy', 5, 4)->default(0.0000)->after('tax_policy');
            $table->index('priority');
        });
    }
};
