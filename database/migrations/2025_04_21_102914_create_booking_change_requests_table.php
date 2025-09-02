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
        Schema::create('booking_change_requests', function (Blueprint $table) {
            $table->id();
            // Foreign keys
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade'); // assuming customers are in 'users' table
            // Type of request: 'cancel' or 'reschedule'
            $table->enum('type', ['cancel', 'reschedule']);
            // New schedule info (nullable if type is 'cancel')
            $table->date('requested_date')->nullable();
            $table->string('requested_time_slot')->nullable();
            // Optional reason from customer
            $table->text('reason')->nullable();
            // Request status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            // Admin/ServiceHolder who responded (optional)
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            // Optional response note
            $table->text('response_note')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_change_requests');
    }
};
