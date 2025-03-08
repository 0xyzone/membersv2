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
        Schema::create('tournament_registration_players', function (Blueprint $table) {
            $table->foreignId('registration_id')->constrained('tournament_registrations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_team_id')->constrained()->cascadeOnDelete();
            // $table->enum('role', ['player', 'substitute'])->default('player');
            $table->timestamps();

            $table->unique(['registration_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_registration_players');
    }
};
