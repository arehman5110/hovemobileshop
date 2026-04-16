<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. POS Categories (Screen Protector, Cover, Charger, Cable...)
        Schema::create('pos_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->default('📦');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. POS Brands (Apple, Samsung, Google, Universal...)
        Schema::create('pos_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. POS Models (iPhone 11, iPhone 12, Galaxy S24...)
        Schema::create('pos_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('pos_brands')->onDelete('cascade');
            $table->string('name');          // e.g. iPhone 15 Pro
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. POS Stock (actual sellable items)
        Schema::create('pos_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('pos_categories')->onDelete('cascade');
            $table->foreignId('model_id')->nullable()->constrained('pos_models')->nullOnDelete();
            $table->string('name');                              // e.g. Privacy Tempered Glass
            $table->string('variant')->nullable();               // e.g. Privacy, Clear, Black, Blue
            $table->string('sku')->nullable();
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('sell_price', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('low_stock_alert')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_stock');
        Schema::dropIfExists('pos_models');
        Schema::dropIfExists('pos_brands');
        Schema::dropIfExists('pos_categories');
    }
};
