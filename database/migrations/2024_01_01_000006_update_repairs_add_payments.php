<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add repair_type_id to repairs, remove technician & labour_price
        Schema::table('repairs', function (Blueprint $table) {
            $table->foreignId('repair_type_id')->nullable()->constrained('repair_types')->nullOnDelete()->after('screen_id');
            $table->dropColumn(['technician', 'labour_price']);
        });

        // Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_id')->constrained()->onDelete('cascade');
            $table->string('payment_type'); // Cash, Card, Trade
            $table->decimal('amount', 8, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->dropForeign(['repair_type_id']);
            $table->dropColumn('repair_type_id');
            $table->string('technician')->nullable();
            $table->decimal('labour_price', 8, 2)->default(0);
        });
        Schema::dropIfExists('payments');
    }
};
