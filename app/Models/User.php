<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_super_admin',
        'role',
        'permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_SUPPORT = 'support';

    const PERM_VIEW_WINNERS = 'view_winners';
    const PERM_VIEW_DEPOSITS = 'view_deposits';
    const PERM_VIEW_WITHDRAWALS = 'view_withdrawals';
    const PERM_VIEW_TRANSACTIONS = 'view_transactions';
    const PERM_VIEW_MESSAGES = 'view_messages';
    const PERM_VIEW_DOCUMENTS = 'view_documents';
    const PERM_VIEW_USER_MESSAGES = 'view_user_messages';
    const PERM_VIEW_PAYMENT_METHODS = 'view_payment_methods';
    const PERM_VIEW_SHOP_PRODUCTS = 'view_shop_products';
    const PERM_VIEW_SHOP_ORDERS = 'view_shop_orders';
    const PERM_VIEW_MEMBERSHIP_TIERS = 'view_membership_tiers';
    const PERM_VIEW_MEMBERSHIP_SUBSCRIPTIONS = 'view_membership_subscriptions';
    const PERM_VIEW_SPIN_AND_WIN = 'view_spin_and_win';
    const PERM_VIEW_SPIN_RESULTS = 'view_spin_results';
    const PERM_VIEW_GIVEAWAYS = 'view_giveaways';
    const PERM_VIEW_PAGES = 'view_pages';
    const PERM_VIEW_SETTINGS = 'view_settings';
    const PERM_VIEW_ACTIVITY_LOG = 'view_activity_log';
    const PERM_VIEW_MAIL_SETTINGS = 'view_mail_settings';
    const PERM_VIEW_SITE_SETTINGS = 'view_site_settings';
    const PERM_VIEW_WINNER_FEATURES = 'view_winner_features';
    const PERM_VIEW_EMAIL_CAMPAIGNS = 'view_email_campaigns';
    const PERM_SEND_EMAIL_CAMPAIGNS = 'send_email_campaigns';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
            'permissions' => 'array',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function isAdmin(): bool
    {
        return $this->is_admin ?? false;
    }

    public function role(): string
    {
        return $this->role ?? self::ROLE_USER;
    }

    public static function roleOptions(): array
    {
        return [
            self::ROLE_ADMIN => 'Super Admin',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_SUPPORT => 'Support',
            self::ROLE_USER => 'User',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin ?? false;
    }

    public static function allPermissionKeys(): array
    {
        return [
            self::PERM_VIEW_WINNERS,
            self::PERM_VIEW_DEPOSITS,
            self::PERM_VIEW_WITHDRAWALS,
            self::PERM_VIEW_TRANSACTIONS,
            self::PERM_VIEW_MESSAGES,
            self::PERM_VIEW_DOCUMENTS,
            self::PERM_VIEW_USER_MESSAGES,
            self::PERM_VIEW_PAYMENT_METHODS,
            self::PERM_VIEW_SHOP_PRODUCTS,
            self::PERM_VIEW_SHOP_ORDERS,
            self::PERM_VIEW_MEMBERSHIP_TIERS,
            self::PERM_VIEW_MEMBERSHIP_SUBSCRIPTIONS,
            self::PERM_VIEW_SPIN_AND_WIN,
            self::PERM_VIEW_SPIN_RESULTS,
            self::PERM_VIEW_GIVEAWAYS,
            self::PERM_VIEW_PAGES,
            self::PERM_VIEW_SETTINGS,
            self::PERM_VIEW_ACTIVITY_LOG,
            self::PERM_VIEW_MAIL_SETTINGS,
            self::PERM_VIEW_SITE_SETTINGS,
            self::PERM_VIEW_WINNER_FEATURES,
            self::PERM_VIEW_EMAIL_CAMPAIGNS,
            self::PERM_SEND_EMAIL_CAMPAIGNS,
        ];
    }

    public static function allPermissions(): array
    {
        return [
            self::PERM_VIEW_WINNERS => 'View Winners',
            self::PERM_VIEW_DEPOSITS => 'View Deposits',
            self::PERM_VIEW_WITHDRAWALS => 'View Withdrawals',
            self::PERM_VIEW_TRANSACTIONS => 'View Transactions',
            self::PERM_VIEW_MESSAGES => 'View Messages',
            self::PERM_VIEW_DOCUMENTS => 'View Documents',
            self::PERM_VIEW_USER_MESSAGES => 'View User Messages',
            self::PERM_VIEW_PAYMENT_METHODS => 'View Payment Methods',
            self::PERM_VIEW_SHOP_PRODUCTS => 'View Shop Products',
            self::PERM_VIEW_SHOP_ORDERS => 'View Shop Orders',
            self::PERM_VIEW_MEMBERSHIP_TIERS => 'View Membership Tiers',
            self::PERM_VIEW_MEMBERSHIP_SUBSCRIPTIONS => 'View Membership Subscriptions',
            self::PERM_VIEW_SPIN_AND_WIN => 'View Spin & Win',
            self::PERM_VIEW_SPIN_RESULTS => 'View Spin Results',
            self::PERM_VIEW_GIVEAWAYS => 'View Giveaways',
            self::PERM_VIEW_PAGES => 'View Pages',
            self::PERM_VIEW_SETTINGS => 'View Settings',
            self::PERM_VIEW_ACTIVITY_LOG => 'View Activity Log',
            self::PERM_VIEW_MAIL_SETTINGS => 'View Mail Settings',
            self::PERM_VIEW_SITE_SETTINGS => 'View Site Settings',
            self::PERM_VIEW_EMAIL_CAMPAIGNS => 'View Email Campaigns',
            self::PERM_SEND_EMAIL_CAMPAIGNS => 'Send Email Campaigns',
        ];
    }

    public function grantedPermissions(): array
    {
        if ($this->isSuperAdmin()) {
            return self::allPermissionKeys();
        }
        if (is_array($this->permissions) && !empty($this->permissions)) {
            return $this->permissions;
        }
        return $this->roleDefaultPermissions();
    }

    public function roleDefaultPermissions(): array
    {
        return match ($this->role) {
            self::ROLE_ADMIN => self::allPermissionKeys(),
            self::ROLE_MANAGER => [
                self::PERM_VIEW_WINNERS,
                self::PERM_VIEW_DEPOSITS,
                self::PERM_VIEW_WITHDRAWALS,
                self::PERM_VIEW_TRANSACTIONS,
                self::PERM_VIEW_MESSAGES,
                self::PERM_VIEW_DOCUMENTS,
                self::PERM_VIEW_USER_MESSAGES,
                self::PERM_VIEW_PAYMENT_METHODS,
                self::PERM_VIEW_SHOP_PRODUCTS,
                self::PERM_VIEW_SHOP_ORDERS,
                self::PERM_VIEW_MEMBERSHIP_TIERS,
                self::PERM_VIEW_MEMBERSHIP_SUBSCRIPTIONS,
                self::PERM_VIEW_SPIN_AND_WIN,
                self::PERM_VIEW_SPIN_RESULTS,
                self::PERM_VIEW_GIVEAWAYS,
                self::PERM_VIEW_PAGES,
                self::PERM_VIEW_SETTINGS,
                self::PERM_VIEW_ACTIVITY_LOG,
                self::PERM_VIEW_MAIL_SETTINGS,
                self::PERM_VIEW_SITE_SETTINGS,
                self::PERM_VIEW_WINNER_FEATURES,
                self::PERM_VIEW_EMAIL_CAMPAIGNS,
                self::PERM_SEND_EMAIL_CAMPAIGNS,
            ],
            self::ROLE_SUPPORT => [
                self::PERM_VIEW_WINNERS,
                self::PERM_VIEW_DEPOSITS,
                self::PERM_VIEW_WITHDRAWALS,
                self::PERM_VIEW_TRANSACTIONS,
                self::PERM_VIEW_MESSAGES,
                self::PERM_VIEW_DOCUMENTS,
                self::PERM_VIEW_USER_MESSAGES,
            ],
            default => [],
        };
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->is_admin) return false;
        return in_array($permission, $this->grantedPermissions());
    }

    public function canViewWinners(): bool { return $this->hasPermission(self::PERM_VIEW_WINNERS); }
    public function canViewDeposits(): bool { return $this->hasPermission(self::PERM_VIEW_DEPOSITS); }
    public function canViewWithdrawals(): bool { return $this->hasPermission(self::PERM_VIEW_WITHDRAWALS); }
    public function canViewTransactions(): bool { return $this->hasPermission(self::PERM_VIEW_TRANSACTIONS); }
    public function canViewMessages(): bool { return $this->hasPermission(self::PERM_VIEW_MESSAGES); }
    public function canViewDocuments(): bool { return $this->hasPermission(self::PERM_VIEW_DOCUMENTS); }
    public function canViewUserMessages(): bool { return $this->hasPermission(self::PERM_VIEW_USER_MESSAGES); }
    public function canViewPaymentMethods(): bool { return $this->hasPermission(self::PERM_VIEW_PAYMENT_METHODS); }
    public function canViewShopProducts(): bool { return $this->hasPermission(self::PERM_VIEW_SHOP_PRODUCTS); }
    public function canViewShopOrders(): bool { return $this->hasPermission(self::PERM_VIEW_SHOP_ORDERS); }
    public function canViewMembershipTiers(): bool { return $this->hasPermission(self::PERM_VIEW_MEMBERSHIP_TIERS); }
    public function canViewMembershipSubscriptions(): bool { return $this->hasPermission(self::PERM_VIEW_MEMBERSHIP_SUBSCRIPTIONS); }
    public function canViewSpinAndWin(): bool { return $this->hasPermission(self::PERM_VIEW_SPIN_AND_WIN); }
    public function canViewSpinResults(): bool { return $this->hasPermission(self::PERM_VIEW_SPIN_RESULTS); }
    public function canViewGiveaways(): bool { return $this->hasPermission(self::PERM_VIEW_GIVEAWAYS); }
    public function canViewPages(): bool { return $this->hasPermission(self::PERM_VIEW_PAGES); }
    public function canViewSettings(): bool { return $this->hasPermission(self::PERM_VIEW_SETTINGS); }
    public function canViewActivityLog(): bool { return $this->hasPermission(self::PERM_VIEW_ACTIVITY_LOG); }
    public function canViewMailSettings(): bool { return $this->hasPermission(self::PERM_VIEW_MAIL_SETTINGS); }
    public function canViewSiteSettings(): bool { return $this->hasPermission(self::PERM_VIEW_SITE_SETTINGS); }
    public function canViewWinnerFeatures(): bool { return $this->hasPermission(self::PERM_VIEW_WINNER_FEATURES); }
    public function canViewEmailCampaigns(): bool { return $this->hasPermission(self::PERM_VIEW_EMAIL_CAMPAIGNS); }
    public function canSendEmailCampaigns(): bool { return $this->hasPermission(self::PERM_SEND_EMAIL_CAMPAIGNS); }
}
