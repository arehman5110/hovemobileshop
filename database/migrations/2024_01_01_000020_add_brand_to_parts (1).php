<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->string('usage_type')->default('repair')->after('category_id');    // repair | accessory | both
            $table->string('brand')->nullable()->after('usage_type');                 // e.g. Apple, Samsung
            $table->string('compatible_with')->nullable()->after('brand');            // e.g. iPhone 15 Pro
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn(['usage_type', 'brand', 'compatible_with']);
        });
    }
};
