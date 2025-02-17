<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tournament_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('user_teams')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->unique(['tournament_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_registrations');
    }
};
