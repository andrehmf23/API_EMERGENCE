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
        Schema::create('data_api_weather', function (Blueprint $table) {
            $table->id();
            $table->decimal('lat', 10, 7);
            $table->decimal('lon', 10, 7);
            $table->string('weather_main');
            $table->string('weather_description');
            $table->string('weather_icon');
            $table->decimal('temp', 6, 2);
            $table->decimal('feels_like', 6, 2);
            $table->decimal('temp_min', 6, 2);
            $table->decimal('temp_max', 6, 2);
            $table->integer('pressure');
            $table->integer('humidity');
            $table->integer('visibility');
            $table->decimal('wind_speed', 5, 2);
            $table->integer('wind_deg');
            $table->integer('clouds_all');
            $table->bigInteger('dt');
            $table->string('country', 5);
            $table->bigInteger('sunrise');
            $table->bigInteger('sunset');
            $table->integer('timezone');
            $table->string('city_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_api_weather');
    }
};
