<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Jobs table — one job = one customer visit (can have multiple devices/repairs)
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->date('date_in');
            $table->date('date_out')->nullable();
            $table->string('status')->default('In Progress');
            $table->text('notes')->nullable();
            // Discount
            $table->string('discount_type')->nullable();   // percent / fixed
            $table->decimal('discount_value', 8, 2)->default(0);
            // Voucher applied
            $table->string('voucher_code')->nullable();
            $table->decimal('voucher_amount', 8, 2)->default(0);
            $table->timestamps();
        });

        // Repair items — each individual repair within a job/device
        Schema::create('repair_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->nullable()->constrained()->nullOnDelete();
            $table->string('device')->nullable();
            $table->foreignId('repair_type_id')->nullable()->constrained('repair_types')->nullOnDelete();
            $table->foreignId('part_id')->nullable()->constrained('parts')->nullOnDelete();
            $table->text('issue')->nullable();
            $table->decimal('price', 8, 2)->default(0);
            $table->string('status')->default('In Progress');
            $table->timestamps();
        });

        // Vouchers table
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type');                         // percent / fixed
            $table->decimal('value', 8, 2);
            $table->decimal('min_spend', 8, 2)->default(0);
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('uses_limit')->nullable();
            $table->integer('uses_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Move payments to reference jobs instead of repairs
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('job_id')->nullable()->after('repair_id')->constrained('jobs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
            $table->dropColumn('job_id');
        });
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('repair_items');
        Schema::dropIfExists('jobs');
    }
};
