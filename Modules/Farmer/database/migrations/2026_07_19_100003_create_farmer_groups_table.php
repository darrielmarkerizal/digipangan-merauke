<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmer_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('village_id')->nullable()->constrained('villages')->cascadeOnUpdate()->nullOnDelete();
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_groups');
    }
};
