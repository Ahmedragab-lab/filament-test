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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_logo')->nullable(); // Path to the logo
            $table->string('company_name')->default(__('Rubix'));
            $table->string('time_zone')->default('UTC'); // Default timezone
            $table->string('date_format')->default('Y-m-d'); // Default date format
            $table->string('language')->default('en'); // Default language
            $table->string('theme')->default('light'); // Default theme (light or dark)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
