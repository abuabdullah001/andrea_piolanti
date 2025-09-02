<?php

use App\Models\SubscriptionPricingOption;
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
        Schema::create('subscription_pricing_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->enum('billing_period', ['monthly', 'yearly' , 'custom'])->default('monthly');
            $table->decimal('price', 8, 2)->nullable();
            $table->integer('duration_days')->nullable();
            $table->string('discount_note')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_pricing_options');
    }
};
