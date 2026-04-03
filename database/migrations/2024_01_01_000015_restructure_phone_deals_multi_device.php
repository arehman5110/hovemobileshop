<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Drop old single-device phone_deals
        Schema::dropIfExists('phone_deals');

        // New phone_deals = one transaction header (customer, date, type, status)
        Schema::create('phone_deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');                           // buy / sell
            $table->string('status')->default('Pending Check');
            $table->string('payment_type')->nullable();
            $table->text('notes')->nullable();
            $table->string('id_card_path')->nullable();
            $table->boolean('terms_agreed')->default(false);
            $table->text('terms_snapshot')->nullable();
            $table->date('deal_date');
            $table->timestamps();
        });

        // Each device in the deal
        Schema::create('deal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_deal_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_device_id')->nullable()->constrained('inventory_devices')->nullOnDelete();
            $table->foreignId('device_category_id')->nullable()->constrained('device_categories')->nullOnDelete();
            $table->string('brand')->nullable();
            $table->string('model');
            $table->string('color')->nullable();
            $table->string('storage')->nullable();
            $table->string('imei')->nullable();
            $table->string('condition')->nullable();
            $table->string('grade')->nullable();
            $table->string('warranty')->nullable();           // for sells
            $table->decimal('price', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Payments against a deal (deposits + subsequent payments)
        Schema::create('deal_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_deal_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 8, 2);
            $table->string('payment_type')->default('Cash');  // Cash / Card / Bank / Trade
            $table->string('payment_label')->nullable();      // e.g. "Deposit", "Final Payment"
            $table->text('notes')->nullable();
            $table->date('paid_date');
            $table->timestamps();
        });

        // Terms & Conditions as a separate table for versioning
        Schema::create('terms_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('type');        // buy / sell
            $table->string('title');
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('terms_conditions')->insert([
            [
                'type'      => 'buy',
                'title'     => 'Standard Buy Terms & Conditions',
                'content'   => "By selling your device to us, you confirm:\n\n1. You are the rightful owner of the device.\n2. The device is not reported lost or stolen.\n3. The device is not subject to any finance agreement.\n4. All data has been backed up and the device has been wiped.\n5. The agreed price is final and no further claims will be made.\n6. We reserve the right to withdraw our offer if the device does not match the described condition.\n7. Payment will be made in the agreed method on the day of the transaction.",
                'is_active' => true,
                'created_at'=> now(), 'updated_at'=> now(),
            ],
            [
                'type'      => 'sell',
                'title'     => 'Standard Sell Terms & Conditions',
                'content'   => "By purchasing this device, you agree:\n\n1. You have inspected the device and are satisfied with its condition.\n2. All sales are final unless the device develops a fault within the warranty period.\n3. Warranty covers manufacturing defects only — not accidental damage, water damage, or screen damage.\n4. Any deposit paid is non-refundable if you cancel the purchase.\n5. The remaining balance must be paid within the agreed timeframe.\n6. The device must be returned in the same condition for any warranty claims.\n7. We are not liable for any data loss after the purchase.",
                'is_active' => true,
                'created_at'=> now(), 'updated_at'=> now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('terms_conditions');
        Schema::dropIfExists('deal_payments');
        Schema::dropIfExists('deal_items');
        Schema::dropIfExists('phone_deals');
    }
};
