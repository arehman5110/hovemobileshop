<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Device categories (laptop, phone, tablet, etc.) — separate from repair categories
        Schema::create('device_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');   // e.g. Mobile Phone, Laptop, Tablet
            $table->string('icon')->default('📱');
            $table->timestamps();
        });

        DB::table('device_categories')->insert([
            ['name'=>'Mobile Phone', 'icon'=>'📱', 'created_at'=>now(), 'updated_at'=>now()],
            ['name'=>'Laptop',       'icon'=>'💻', 'created_at'=>now(), 'updated_at'=>now()],
            ['name'=>'Tablet',       'icon'=>'📟', 'created_at'=>now(), 'updated_at'=>now()],
            ['name'=>'Smartwatch',   'icon'=>'⌚', 'created_at'=>now(), 'updated_at'=>now()],
            ['name'=>'Console',      'icon'=>'🎮', 'created_at'=>now(), 'updated_at'=>now()],
            ['name'=>'Other',        'icon'=>'📦', 'created_at'=>now(), 'updated_at'=>now()],
        ]);

        // Inventory — devices we own and want to sell
        Schema::create('inventory_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_category_id')->nullable()->constrained('device_categories')->nullOnDelete();
            $table->string('brand')->nullable();
            $table->string('model');
            $table->string('color')->nullable();
            $table->string('storage')->nullable();
            $table->string('imei')->nullable();
            $table->string('condition')->nullable();
            $table->string('grade')->nullable();      // A, B, C
            $table->decimal('cost_price', 8, 2)->default(0);
            $table->decimal('asking_price', 8, 2)->default(0);
            $table->string('status')->default('Available'); // Available, Sold, Reserved
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Drop old phone_deals and recreate with full features
        Schema::dropIfExists('phone_deals');

        Schema::create('phone_deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('inventory_device_id')->nullable()->constrained('inventory_devices')->nullOnDelete();
            $table->foreignId('device_category_id')->nullable()->constrained('device_categories')->nullOnDelete();

            $table->string('type');                          // buy / sell
            $table->string('status')->default('Completed'); // Pending Check, Offered, Accepted, Rejected, Completed

            // Device details
            $table->string('brand')->nullable();
            $table->string('model');
            $table->string('color')->nullable();
            $table->string('storage')->nullable();
            $table->string('imei')->nullable();
            $table->string('condition')->nullable();
            $table->string('grade')->nullable();

            // Financial
            $table->decimal('price', 8, 2)->default(0);          // agreed price
            $table->decimal('deposit', 8, 2)->default(0);        // deposit paid (for sales)
            $table->string('payment_type')->nullable();
            $table->string('warranty')->nullable();               // warranty given (for sales)

            // Terms & docs
            $table->text('terms_snapshot')->nullable();           // snapshot of T&C at time of deal
            $table->boolean('terms_agreed')->default(false);
            $table->string('id_card_path')->nullable();
            $table->text('notes')->nullable();
            $table->date('deal_date');
            $table->timestamps();
        });

        // Add terms_and_conditions to settings
        DB::table('settings')->insertOrIgnore([
            ['key'=>'terms_buy',  'value'=>"By selling your device to us, you confirm:\n\n1. You are the rightful owner of the device.\n2. The device is not reported lost or stolen.\n3. The device is not subject to any finance agreement.\n4. All data has been backed up and the device has been wiped.\n5. The agreed price is final and no further claims will be made.\n\nWe reserve the right to withdraw our offer if the device does not match the described condition.", 'created_at'=>now(), 'updated_at'=>now()],
            ['key'=>'terms_sell', 'value'=>"By purchasing this device, you agree:\n\n1. You have inspected the device and are satisfied with its condition.\n2. All sales are final unless the device develops a fault within the warranty period.\n3. Warranty covers manufacturing defects only — not accidental damage.\n4. Any deposit paid is non-refundable if you cancel the purchase.\n5. The device must be returned in the same condition for warranty claims.", 'created_at'=>now(), 'updated_at'=>now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_deals');
        Schema::dropIfExists('inventory_devices');
        Schema::dropIfExists('device_categories');
    }
};
