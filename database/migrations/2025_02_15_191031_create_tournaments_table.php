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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('logo_image_path')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->longText('description')->nullable();
            $table->string('platform')->nullable();
            $table->string('type')->nullable();
            $table->json('meta_tags')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('discord_invite_link')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('registration_start_date')->nullable();
            $table->date('registration_end_date')->nullable();
            $table->integer('max_teams')->nullable();
            $table->integer('min_team_players')->nullable();
            $table->integer('max_team_players')->nullable();
            $table->string('organizer_name')->nullable();
            $table->bigInteger('organizer_contact_number')->nullable();
            $table->bigInteger('organizer_alt_contact_number')->nullable();
            $table->string('organizer_contact_email')->nullable();
            $table->longText('rules')->nullable();
            $table->longText('prize_pool')->nullable();
            $table->longText('road_map')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('draft');
            $table->boolean('is_public')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->string('stream_url')->nullable();
            $table->string('official_hashtag')->nullable();
            $table->string('website_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->integer('min_player_age')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['user_id', 'game_id']);
            $table->index('start_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
