<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add IMEI, color, warranty to devices
        Schema::table('devices', function (Blueprint $table) {
            $table->string('imei')->nullable()->after('name');
            $table->string('color')->nullable()->after('imei');
            $table->string('warranty')->nullable()->after('color'); // e.g. "3 months", "6 months"
        });

        // Phone deals — buy & sell
        Schema::create('phone_deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');                    // buy / sell
            $table->string('brand')->nullable();
            $table->string('model');
            $table->string('color')->nullable();
            $table->string('imei')->nullable();
            $table->string('storage')->nullable();     // e.g. 128GB
            $table->string('condition')->nullable();   // e.g. Good, Fair, Excellent
            $table->decimal('price', 8, 2)->default(0);
            $table->string('payment_type')->nullable();// Cash / Card / Trade
            $table->text('notes')->nullable();
            $table->string('id_card_path')->nullable();// uploaded ID card image path
            $table->date('deal_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_deals');
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['imei', 'color', 'warranty']);
        });
    }
};
