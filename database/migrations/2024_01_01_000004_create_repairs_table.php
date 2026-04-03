<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('screen_id')->constrained()->onDelete('cascade');
            $table->date('date_in');
            $table->date('date_out')->nullable();
            $table->string('status')->default('In Progress'); // In Progress, Completed, Waiting Parts, Cancelled
            $table->text('issue')->nullable();
            $table->decimal('labour_price', 8, 2)->default(0);
            $table->decimal('total_price', 8, 2)->default(0);
            $table->string('technician')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
