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
        Schema::create('cars', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->string('slug');
        $table->string('model');
        $table->string('brand_name')->nullable();
        $table->longText('description');
        $table->string('image');
        $table->string('location');
        $table->date('date');
        $table->decimal('price', 12, 2); // safer than float


        $table->timestamps();
        });
    }

//       id int
//   title string
//   model string
//   description longtext
//   image string
//   location string
//   date date
//   price float
//   user_id int [ref: > user.id]
//   car_details_id int [ref: > car_details.id]
//   multi_image_id int [ref: > multi_image.id]
//   char_internal_id int [ref: > char_internal.id]
//   char_external_id int [ref: > char_external.id]
//   char_safety_id int [ref: > char_safety.id]
//   char_comfort_id int [ref: > char_comfort.id]
//   quickSpecs_id int [ref: > quickSpecs.id]

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
