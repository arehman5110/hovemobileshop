<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('screens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('model');          // e.g. iPhone 15 Pro
            $table->string('screen_type');    // Soft (OLED) / Hard (Original)
            $table->string('quality')->default('Compatible'); // Original / Compatible / Refurbished
            $table->integer('stock')->default(0);
            $table->decimal('cost_price', 8, 2)->nullable();
            $table->decimal('sell_price', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screens');
    }
};
