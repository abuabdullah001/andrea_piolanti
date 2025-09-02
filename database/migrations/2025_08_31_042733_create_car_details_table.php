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
        Schema::create('car_details', function (Blueprint $table) {
        $table->id();
        $table->foreignId('car_id')->constrained()->onDelete('cascade');
        $table->string('body_type', 100);
        $table->string('condition', 100);
        $table->year('year'); // Only stores year (e.g., 2025)
        $table->string('cylinders', 50)->nullable();
        $table->string('mileage', 50)->nullable();
        $table->string('transmission', 100)->nullable();
        $table->string('displacement', 50)->nullable();
        $table->string('color', 50)->nullable();
        $table->string('fuel_type', 50)->nullable();
        $table->string('drive_type', 50)->nullable();
        $table->tinyInteger('doors')->nullable(); // small integer (2, 4, 5)
        $table->string('vin', 100)->unique(); // vehicle identification number

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_details');
    }
};
