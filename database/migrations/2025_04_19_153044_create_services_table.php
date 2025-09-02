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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->integer('owner_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->string('price')->nullable();
            $table->enum('is_deposite', ['yes', 'no'])->default('no');
            $table->string('minimum_deposite')->default(0.00);
            $table->string('tax')->default(0.00);
            $table->enum('service_at', ['person', 'virtual'])->default('person');
            $table->string('location')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
