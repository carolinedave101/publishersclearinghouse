<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('is_admin');
        });

        User::where('role', 'admin')->update(['is_super_admin' => true]);

        $map = [
            'portal' => ['view_winners', 'view_deposits', 'view_withdrawals', 'view_transactions', 'view_messages', 'view_documents', 'view_user_messages'],
            'shop' => ['view_payment_methods', 'view_shop_products', 'view_shop_orders'],
            'memberships' => ['view_membership_tiers', 'view_membership_subscriptions'],
            'content' => ['view_spin_and_win', 'view_spin_results', 'view_giveaways', 'view_pages'],
            'settings' => ['view_settings', 'view_activity_log', 'view_mail_settings', 'view_site_settings', 'view_winner_features'],
            'users' => [],
        ];

        User::whereNotNull('permissions')->lazyById(100)->each(function ($user) use ($map) {
            $oldPerms = $user->permissions;
            if (empty($oldPerms)) {
                return;
            }
            $newPerms = [];
            foreach ($oldPerms as $old) {
                if (isset($map[$old])) {
                    array_push($newPerms, ...$map[$old]);
                }
            }
            $user->permissions = array_values(array_unique($newPerms));
            $user->save();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });
    }
};
