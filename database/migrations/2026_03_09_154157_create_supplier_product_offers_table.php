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
        Schema::create('supplier_product_offers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete()
                ->comment('Supplier offering the product');

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete()
                ->comment('Product offered by the supplier');

            $table->decimal('base_cost', 10, 2)
                ->comment('Base cost of the product without taxes or shipping');

            $table->char('currency', 3)
                ->default('USD')
                ->comment('Currency code in ISO 4217 format');

            $table->decimal('estimated_tax', 10, 2)
                ->default(0)
                ->comment('Estimated tax applicable to this offer');

            $table->decimal('estimated_shipping', 10, 2)
                ->default(0)
                ->comment('Estimated shipping cost associated with this offer');

            $table->decimal('other_fees', 10, 2)
                ->default(0)
                ->comment('Other estimated charges (customs, commissions, etc.)');

            $table->boolean('is_available')
                ->default(true)
                ->comment('Indicates if the supplier currently has product availability');

            $table->timestamp('last_checked_at')
                ->nullable()
                ->comment('Date and time of the last availability or price check');

            $table->timestamps();

            $table->unique(['supplier_id', 'product_id']);
            $table->index(['product_id', 'is_available', 'base_cost']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_product_offers');
    }
};
