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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->nullable();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained('service_time_slots')->onDelete('cascade');
            $table->date('date');
            $table->string('subtotal');
            $table->string('discount')->nullable();
            $table->string('tax')->nullable();
            $table->string('total');
            $table->string('advance')->default('0');
            $table->string('due');
            $table->string('payment_status')->default('unpaid');
            $table->string('transaction_id')->nullable();
            $table->enum('payment_method', ['cash', 'stripe'])->default('cash');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'rescheduled'])->default('pending');
            $table->enum('booking_type', ['standard', 'custom'])->default('standard');
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
