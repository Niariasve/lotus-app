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
        |------------------------------------------------------
        | CUSTOMERS
        |------------------------------------------------------
        */
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('full_name', 150);
            $table->string('email', 150)->nullable()->unique();
            $table->string('city', 100)->nullable();
            $table->string('phone', 30)->nullable();

            $table->timestamps();

            /*
            |------------------------------------------------------
            | INDEXES
            |------------------------------------------------------
            */
            $table->index('phone');
            $table->index('city');
            $table->index('created_at');
        });

        /*
        |------------------------------------------------------
        | CONTACT_PLATFORMS (CATALOG)
        |------------------------------------------------------
        */
        Schema::create('contact_platforms', function (Blueprint $table) {
            $table->id();

            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });

        /*
        |------------------------------------------------------
        | CUSTOMER_CONTACTS 
        |------------------------------------------------------
        */
        Schema::create('customer_contacts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('platform_id')->constrained('contact_platforms')->restrictOnDelete();

            $table->string('contact_identifier', 100);
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            /*
            |------------------------------------------------------
            | INDEXES
            |------------------------------------------------------
            */
            $table->unique(
                ['customer_id', 'platform_id'],
                'customer_platform_unique',
            );

            $table->index(
                ['platform_id', 'contact_identifier'],
                'platform_username_index',
            );

            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_contacts');
        Schema::dropIfExists('contact_platforms');
        Schema::dropIfExists('customers');
    }
};
