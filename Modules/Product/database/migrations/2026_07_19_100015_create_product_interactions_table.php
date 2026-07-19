<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('type', ['view', 'contact']);
            $table->char('visitor_hash', 64)->nullable();
            $table->string('referrer_host', 100)->nullable();
            $table->timestamp('occurred_at');

            $table->index(['product_id', 'type', 'occurred_at']);
            $table->index(['region_id', 'type', 'occurred_at']);
            $table->index(['type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_interactions');
    }
};
