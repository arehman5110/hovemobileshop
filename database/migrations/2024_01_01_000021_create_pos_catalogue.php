<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Product types: Charger, Cable, Screen Protector, Cover, etc.
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // e.g. Screen Protector
            $table->string('icon')->default('📦');
            $table->string('color')->default('#0d6efd');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Phone models: iPhone 11, iPhone 12, Samsung S24, etc.
        Schema::create('phone_models', function (Blueprint $table) {
            $table->id();
            $table->string('brand');         // e.g. Apple, Samsung
            $table->string('name');          // e.g. iPhone 11
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Variants: Privacy, Clear, Matte, Black, Blue, etc.
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // e.g. Privacy, Clear, Matte
            $table->string('product_type_id'); // which types this variant applies to (JSON array or FK)
            $table->timestamps();
        });

        // Link parts to the new structure
        Schema::table('parts', function (Blueprint $table) {
            $table->unsignedBigInteger('product_type_id')->nullable()->after('category_id');
            $table->unsignedBigInteger('phone_model_id')->nullable()->after('product_type_id');
            $table->string('variant')->nullable()->after('phone_model_id'); // e.g. Privacy, Clear
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn(['product_type_id','phone_model_id','variant']);
        });
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('phone_models');
        Schema::dropIfExists('product_types');
    }
};
