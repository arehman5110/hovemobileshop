<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Default email template
        DB::table('settings')->insert([
            ['key' => 'shop_name',      'value' => 'Mobile Shop',       'created_at' => now(), 'updated_at' => now()],
            ['key' => 'shop_phone',     'value' => '',                  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'shop_email',     'value' => '',                  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'shop_address',   'value' => '',                  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'email_subject',  'value' => 'Your Repair Receipt — Job #{job_id}', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'email_template', 'value' => "Dear {customer_name},\n\nThank you for choosing {shop_name}!\n\nYour repair job #{job_id} is now {status}.\n\nSummary:\n{repair_summary}\n\nTotal: {total}\nPaid: {paid}\nBalance Due: {balance}\n\nPlease find your receipt attached.\n\nIf you have any questions, feel free to contact us.\n\nKind regards,\n{shop_name}\n{shop_phone}", 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
