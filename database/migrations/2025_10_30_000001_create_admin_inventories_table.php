<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('product_variation_id')->nullable();
            $table->bigInteger('qty')->default(0);

            // Ensure either product or variation can be tracked; one must be non-null at the app level
            $table->index(['admin_id']);
            $table->index(['product_id']);
            $table->index(['product_variation_id']);

            // Unique per admin across the triplet (admin, product, variation)
            // This supports rows for simple products (variation null) and variations (both set)
            $table->unique(['admin_id', 'product_id', 'product_variation_id'], 'uniq_admin_product_variation_triplet');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_inventories');
    }
};
