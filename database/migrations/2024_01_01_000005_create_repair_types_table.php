<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('repair_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // e.g. Screen, Charging Port, Speaker
            $table->string('icon')->nullable(); // emoji
            $table->string('color', 7)->default('#0A84FF');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_types');
    }
};
