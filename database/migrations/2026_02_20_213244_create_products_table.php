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
        /*
        |-------------------------------------------------------
        | PRODUCTS
        |-------------------------------------------------------
        */
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('sku', 100)->unique();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('brand', 120)->nullable();
            $table->string('line', 120)->nullable();

            $table->decimal('height', 6, 3)->nullable();
            $table->decimal('weight_est', 6, 3)->nullable();
            $table->decimal('weight_real', 6, 3)->nullable();

            $table->date('release_date')->nullable();
            
            $table->timestamps();

            /*
            |-------------------------------------------------------
            | INDEXES
            |-------------------------------------------------------
            */
            $table->index('brand');
            $table->index('release_date');
            $table->index('created_at');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
