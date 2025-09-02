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
        Schema::create('booking_due_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');

            $table->decimal('requested_payable_amount', 10, 2);
            $table->decimal('full_payable_amount', 10, 2); // fixed typo: double underscore

            $table->text('note')->nullable();

            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('paid_at')->nullable();

            $table->enum('status', ['pending', 'paid', 'rejected'])->default('pending')->comment('pending, paid, rejected');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_due_requests');
    }
};
