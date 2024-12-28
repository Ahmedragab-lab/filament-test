<?php

use App\Models\Setting;
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
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            // $table->string('label');
            $table->text('value')->nullable();
            $table->json('attributes')->nullable();
            $table->string('type');
            $table->timestamps();
        });
        Setting::create([
            'key' => 'site_name',
            'label' => 'Site Name',
            'value' => null,
            'type' => 'text',
        ]);

        // Setting::create([
        //     'key' => 'basic_plan_price',
        //     'label' => 'Basic Plan Price',
        //     'value' => 1000,
        //     'type' => 'number',
        // ]);

        // Setting::create([
        //     'key' => 'pro_plan_price',
        //     'label' => 'Pro Plan Price',
        //     'value' => 2000,
        //     'type' => 'number',
        // ]);

        // Setting::create([
        //     'key' => 'environment',
        //     'label' => 'Environment',
        //     'value' => 'production',
        //     'type' => 'select',
        //     'attributes' => [
        //         'options' => [
        //             'production' => 'Production',
        //             'staging' => 'Staging',
        //             'local' => 'Local',
        //         ],
        //     ],
        // ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
