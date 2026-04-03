<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // e.g. iPhone, Samsung, Huawei
            $table->string('slug')->unique();
            $table->string('color', 7)->default('#0A84FF'); // hex color for badge
            $table->string('icon')->nullable(); // emoji or icon name
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
