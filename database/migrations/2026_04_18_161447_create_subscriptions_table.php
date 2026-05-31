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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->restrictOnDelete();
            $table->foreignId('overridden_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->string('stripe_coupon_id')->nullable()->unique();
            $table->string('stripe_price_id')->nullable();
            $table->enum('status', ['active', 'past_due', 'canceled', 'free'])->default('active');
            $table->decimal('override_price', 8, 2)->nullable();
            $table->timestamp('current_period_start');
            $table->timestamp('current_period_end');
            $table->timestamp('canceled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
