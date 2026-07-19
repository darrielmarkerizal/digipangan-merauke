<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('product_category_id');
            $table->unsignedSmallInteger('unit_id');
            $table->foreignId('farmer_id')->constrained('farmers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('weight_value', 8, 2)->nullable();
            $table->boolean('stock_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_region_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_category_id')->references('id')->on('product_categories')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->index(['is_featured', 'is_active']);
            $table->index(['region_id', 'is_region_featured', 'is_active']);
            $table->index(['is_active', 'created_at']);
            $table->index(['product_category_id', 'region_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
