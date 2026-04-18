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
        Schema::create('therapist_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapist_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('processed_by')->constrained('users')->restrictOnDelete();
            $table->decimal('amount', 8, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->unsignedSmallInteger('patient_count');
            $table->date('period_start');
            $table->date('period_end');
            $table->text('note')->nullable();
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
        Schema::dropIfExists('therapist_payouts');
    }
};
