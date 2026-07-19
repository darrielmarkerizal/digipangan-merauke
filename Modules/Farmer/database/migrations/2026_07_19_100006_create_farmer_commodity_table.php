<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmer_commodity', function (Blueprint $table) {
            $table->foreignId('farmer_id')->constrained('farmers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedSmallInteger('commodity_id');
            $table->primary(['farmer_id', 'commodity_id']);

            $table->foreign('commodity_id')->references('id')->on('commodities')
                ->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_commodity');
    }
};
