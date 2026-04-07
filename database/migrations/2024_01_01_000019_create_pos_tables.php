<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name')->nullable(); // walk-in name
            $table->decimal('subtotal',     10, 2)->default(0);
            $table->string('discount_type')->nullable(); // percent | fixed
            $table->decimal('discount_value',10,2)->default(0);
            $table->string('voucher_code')->nullable();
            $table->decimal('voucher_amount',10,2)->default(0);
            $table->decimal('total',        10, 2)->default(0);
            $table->decimal('paid',         10, 2)->default(0);
            $table->decimal('change_given', 10, 2)->default(0);
            $table->string('payment_method')->default('Cash'); // Cash|Card|Split
            $table->text('payment_notes')->nullable(); // split details JSON
            $table->string('status')->default('completed'); // completed|refunded
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pos_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_sale_id')->constrained()->onDelete('cascade');
            $table->string('type');          // service | part | custom
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total',      10, 2);
            $table->unsignedBigInteger('ref_id')->nullable(); // part_id / repair_type_id
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_sale_items');
        Schema::dropIfExists('pos_sales');
    }
};
