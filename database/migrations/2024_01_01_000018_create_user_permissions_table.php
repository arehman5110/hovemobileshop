<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('permission');
            $table->boolean('granted')->default(true); // true = explicitly granted, false = explicitly denied
            $table->timestamps();

            $table->unique(['user_id', 'permission']); // one row per user+permission
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
