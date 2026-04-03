<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the existing foreign key first
            $table->dropForeign(['repair_id']);

            // Make repair_id nullable and re-add foreign key with nullOnDelete
            $table->unsignedBigInteger('repair_id')->nullable()->change();

            $table->foreign('repair_id')
                  ->references('id')
                  ->on('repairs')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['repair_id']);

            $table->unsignedBigInteger('repair_id')->nullable(false)->change();

            $table->foreign('repair_id')
                  ->references('id')
                  ->on('repairs')
                  ->onDelete('cascade');
        });
    }
};