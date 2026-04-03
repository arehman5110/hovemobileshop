<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create parts table (replaces screens - now general repair stock)
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');             // e.g. iPhone 15 Pro Screen, Samsung Charging Port
            $table->string('part_type');        // Screen / Battery / Charging Port / Speaker / Back Glass / Camera / Other
            $table->string('quality')->default('Compatible'); // Original / Compatible / Refurbished / Good Used
            $table->integer('stock')->default(0);
            $table->decimal('cost_price', 8, 2)->nullable();
            $table->decimal('sell_price', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Update repairs table - replace screen_id with part_id (optional)
        Schema::table('repairs', function (Blueprint $table) {
            $table->dropForeign(['screen_id']);
            $table->dropColumn('screen_id');
            $table->foreignId('part_id')->nullable()->constrained('parts')->nullOnDelete()->after('repair_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->dropForeign(['part_id']);
            $table->dropColumn('part_id');
            $table->foreignId('screen_id')->nullable()->constrained('screens')->nullOnDelete();
        });
        Schema::dropIfExists('parts');
    }
};
