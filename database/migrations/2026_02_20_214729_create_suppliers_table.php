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
        | SUPPLIERS
        |-------------------------------------------------------
        */
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150)->unique();
            $table->text('description')->nullable();
            
            $table->unsignedInteger('priority')->default(100);

            $table->decimal('tax_policy', 5, 4)->default(0.0000);
            $table->decimal('shipping_policy', 5, 4)->default(0.0000);

            $table->char('currency', 3);

            $table->timestamps();

            /*
            |-------------------------------------------------------
            | INDEXES
            |-------------------------------------------------------
            */
            $table->index('priority');
            $table->index('currency');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
