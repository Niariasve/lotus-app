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

            $table->unsignedInteger('priority')
                ->default(50)
                ->comment('Priority level on which a price should be given to a customer (lower number is more priority).');

            $table->decimal('base_cost', 10, 2)
                ->comment('Base cost of the product without taxes or shipping');

            $table->decimal('retail_price', 10, 2)
                ->comment('Retail price calculated by {[(base_cost + shipping)*supplier_tax + 8.75*product_weight]*profit_percentage}');

            $table->decimal('profit_percentage', 5, 4)
                ->comment('Profit percentage on this supplier product offer');

            $table->boolean('is_available')
                ->default(true)
                ->comment('Indicates if the supplier currently has product availability');

            $table->timestamp('last_checked_at')
                ->nullable()
                ->comment('Date and time of the last availability or price check');

            $table->timestamps();

            $table->unique(['supplier_id', 'product_id', 'priority']);
            $table->index(['product_id', 'is_available', 'retail_price'], 'spo_product_available_price_idx');
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
