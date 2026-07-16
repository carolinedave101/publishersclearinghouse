<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('payment_methods')
            ->where('purpose', 'both')
            ->update(['purpose' => 'deposit,withdrawal']);
    }

    public function down(): void
    {
        DB::table('payment_methods')
            ->where('purpose', 'deposit,withdrawal')
            ->update(['purpose' => 'both']);
    }
};
