<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('winners', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('zip');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->string('gender', 10)->nullable()->after('date_of_birth');
            $table->json('demographics')->nullable()->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('winners', function (Blueprint $table) {
            $table->dropColumn(['phone', 'date_of_birth', 'gender', 'demographics']);
        });
    }
};
