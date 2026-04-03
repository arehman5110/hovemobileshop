<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Devices table
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('note')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add device_id to repair_items (nullable — links repair to a device)
        Schema::table('repair_items', function (Blueprint $table) {
            $table->foreignId('device_id')->nullable()->after('job_id')
                  ->constrained('devices')->nullOnDelete();
        });

        // Add customer_id to vouchers (null = all customers)
        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')
                  ->constrained('customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
        Schema::table('repair_items', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropColumn('device_id');
        });
        Schema::dropIfExists('devices');
    }
};
