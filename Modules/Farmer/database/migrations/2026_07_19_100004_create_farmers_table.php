<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('village_id')->nullable()->constrained('villages')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('farmer_group_id')->nullable()->constrained('farmer_groups')->cascadeOnUpdate()->nullOnDelete();
            $table->string('name', 120);
            $table->string('slug', 150)->unique();
            $table->string('phone', 20);
            $table->decimal('land_area_ha', 8, 2)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};
