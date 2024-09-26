<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start'); // yyyy-MM-dd'T'HH:mm:ss.SSS'Z'
            $table->timestamp('end'); // yyyy-MM-dd'T'HH:mm:ss.SSS'Z'
            $table->string('text', 255)->nullable();
            $table->string('backColor', 15)->nullable();
            $table->string('borderColor', 15)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendars');
    }
};
