<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_price_histories', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 8, 2);
            $table->string('stripe_price_id');
            $table->timestamp('effective_from');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_price_histories');
    }
};
