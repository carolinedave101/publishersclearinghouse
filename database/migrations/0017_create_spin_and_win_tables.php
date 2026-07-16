<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('games');

        Schema::create('spin_and_wins', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('rules')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->integer('max_spins_per_day')->default(3);
            $table->integer('cooldown_minutes')->default(0);
            $table->boolean('requires_login')->default(false);
            $table->text('success_message')->nullable();
            $table->timestamps();
        });

        Schema::create('spin_wheel_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spin_and_win_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('color', 20)->default('#D4AF37');
            $table->string('prize_type')->default('nothing');
            $table->decimal('prize_value', 12, 2)->default(0);
            $table->text('prize_description')->nullable();
            $table->integer('weight')->default(1);
            $table->boolean('is_jackpot')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('spin_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spin_and_win_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spin_wheel_segment_id')->constrained()->cascadeOnDelete();
            $table->string('winner_name')->nullable();
            $table->string('winner_email')->nullable();
            $table->string('prize_label');
            $table->string('prize_type');
            $table->decimal('prize_value', 12, 2)->default(0);
            $table->boolean('is_claimed')->default(false);
            $table->timestamp('claimed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spin_results');
        Schema::dropIfExists('spin_wheel_segments');
        Schema::dropIfExists('spin_and_wins');

        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type');
            $table->text('rules')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
};
