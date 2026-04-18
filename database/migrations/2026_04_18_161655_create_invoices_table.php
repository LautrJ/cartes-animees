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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->restrictOnDelete();
            $table->string('stripe_invoice_id')->unique();
            $table->decimal('amount', 8, 2);
            $table->enum('status', ['draft', 'open', 'paid', 'uncollectible', 'void']);
            $table->string('invoice_pdf')->nullable();
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->timestamp('paid_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
