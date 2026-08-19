<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_links', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('source')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('winners', function (Blueprint $table) {
            $table->foreignId('registration_link_id')
                ->nullable()
                ->after('source')
                ->constrained('registration_links')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('winners', function (Blueprint $table) {
            $table->dropConstrainedForeignId('registration_link_id');
        });

        Schema::dropIfExists('registration_links');
    }
};