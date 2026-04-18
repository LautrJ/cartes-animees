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
        Schema::create('child_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->restrictOnDelete();
            $table->foreignId('series_id')->constrained('series')->restrictOnDelete();
            $table->foreignId('unlocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['unlocked', 'completed'])->default('unlocked');
            $table->timestamp('unlocked_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['child_id', 'series_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_series');
    }
};
