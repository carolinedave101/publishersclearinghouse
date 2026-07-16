# Publisher's Clearing House - Project Memory

## Project Context
This is a **Laravel 11** admin panel + public site for a PCH winners portal with:
- **Laravel 11** (PHP 8.3)
- **Filament 3** (admin panel)
- **Tailwind CSS 4**
- **MySQL 8.0** (MariaDB on cPanel target)
- **Laravel Sanctum** (API tokens)
- Deployable on cPanel **without terminal/SSH access**

## Tech Constraints
- cPanel has no terminal/SSH → all artisan commands must be pre-run locally, replaced with web-based setup, or exported as SQL
- `mysql` driver is default
- All Filament assets are pre-built via Vite (`public/build/`)

## Database
- **Name**: `pch_portal`
- **Test DB**: `pch_portal_test`
- **Migrations**: `0001`–`0019` in `database/migrations/`
- **Seeders**: `InitialContentSeeder` creates admin user + Spin & Win game with 8 segments

### Key Tables
| Table | Purpose |
|---|---|
| `users` | User accounts (is_admin flag) |
| `winners` | Prize winners with unique_code, prize_amount, status |
| `messages` | Winner-related messages (legacy) |
| `documents` | Winner-related documents |
| `giveaways` | Giveaway campaigns with entries via `giveaway_entries` |
| `shop_products` | Products for the shop |
| `shop_orders` | Orders with customer_email, items (JSON), status, total |
| `membership_tiers` | Tier definitions with features (JSON), price |
| `membership_subscriptions` | User subscriptions linked to tiers via subscriber_email |
| `pages` | CMS pages (slug, content, published) |
| `settings` | Key-value site settings |
| `spin_and_wins` | Spin & Win game config (daily_limit, cooldown, etc.) |
| `spin_wheel_segments` | Wheel segments (label, color, weight, prize_type, prize_value) |
| `spin_results` | Spin results tracked per user_id |
| `user_messages` | User-to-admin messaging (direction enum, is_read) |
| `activity_log` | Admin activity tracking |

## Admin Panel
- **URL**: `/admin`
- **Login**: `admin@pchportal.com` / `admin123`
- **Resources**: Winner, Message, Document, User, ActivityLog, Giveaway, ShopProduct, ShopOrder, MembershipTier, MembershipSubscription, Page, Setting, SpinAndWin, SpinResult, UserMessage (15 total)
- **Navigation Groups**: Content, Shop, Portal, Gaming, System (no group icons)

## Key Models (14)
Located in `app/Models/`:
- `User` - has `is_admin`, `HasApiTokens` (Sanctum)
- `Winner` - prize info, `unique_code`, status, `next_steps`, hasMany messages/documents
- `Giveaway` - campaigns with entries, `hasMany GiveawayEntry`
- `GiveawayEntry` - tracks email per giveaway
- `ShopProduct` - products for sale, `is_active`
- `ShopOrder` - customer_email, items JSON, status, total
- `MembershipTier` - name, price, features JSON, `is_active`
- `MembershipSubscription` - linked to tier, subscriber_email, status, start/end dates
- `SpinAndWin` - game config, hasMany segments + results
- `SpinWheelSegment` - label, color, weight, prize_type/value, is_jackpot
- `SpinResult` - user_id, segment, prize tracking
- `UserMessage` - user_id, admin_id, direction (user_to_admin/admin_to_user), is_read
- `Page` - slug, title, content, published
- `Setting` - key/value pairs

## User-Facing Routes
| Route | Description |
|---|---|
| `/` | Home page with winner code lookup |
| `/login` | User login |
| `/register` | User registration |
| `/dashboard` | User dashboard (spins, orders, messages, membership, winnings) |
| `/profile` | Edit name, email, password |
| `/orders` | Full order history |
| `/messages` | User messaging with admin |
| `/games` | Spin & Win game (requires auth) |
| `/giveaways` | Browse and enter giveaways |
| `/shop` | Browse and order products |
| `/memberships` | View and sign up for membership tiers |
| `/winner/dashboard` | Winner portal (separate session by unique_code) |
| `/setup` | One-click setup route (requires token) |

## Testing
- **Framework**: PHPUnit
- **Config**: `phpunit.xml` + `.env.testing`
- **Tests**: 88 tests, 124 assertions (Unit + Feature)
- Factory for every model with `HasFactory`
- Test DB refreshed between test runs

## Key Decisions
- Dropped `laravel/reverb`, `tymon/jwt-auth`, `resend/resend-laravel` — winner auth uses sessions
- Navigation group icons removed entirely
- Spin results track `user_id` for per-user limits and dashboard display
- User messages use flat `user_messages` table with `direction` enum
- Old `games` table dropped by migration `0017`; spin-and-win is the only game type

## Build / Deploy
- `npm run build` for Vite assets
- `php artisan migrate:fresh --seed` for fresh DB
- cPanel deploy: export `database/schema.sql`, copy `public/build/`, `setup` route for remote init

## Status Log

### Session 2026-07-05 — Dashboard + Orders + Memory & Graphify
- Added shop orders, winner info, and enhanced membership to user dashboard
- Created `/orders` page with full purchase history
- Updated navigation (desktop + mobile) with My Orders, Messages links
- Enhanced membership section: tier name, status, dates, feature list with checkmarks
- Added Winnings section: spin total + prize winner info linked by email
- Added Quick Stats card to dashboard
- 4 new tests (dashboard/orders access + content), 88 total all passing
- **Pending**: cPanel deployment guide (`database/schema.sql`, pre-built assets, setup route docs)

### Session 2026-06- (Initial Build)
- Project setup: Laravel 11, Filament 3, Tailwind 4, MySQL
- Fixed artisan entry point, ActivityLogResource import
- Created all migrations 0001-0019
- Built 14 Eloquent models with HasFactory, relationships, casts, scopes
- Built 15 Filament resources with CRUD + page classes + relation managers
- Built Spin & Win system (canvas wheel, weighted random, daily limits, cooldown)
- Built user auth (register/login/logout/profile)
- Built user messaging system
- Built web controllers: Auth, Dashboard, Profile, SpinAndWin, UserMessage, WinnerDashboard, Giveaway, Shop, Membership, Setup
- Created test infrastructure: phpunit.xml, .env.testing, all factories
- Removed old games system (migration 0017)
- Beautified game page (gradients, glow, animations, modal)
- Updated navigation with auth state and winner badge
- Fixed icon error in UserMessageResource

## Next Steps
1. Produce full cPanel deployment guide with `database/schema.sql` export, pre-built `public/build/`, and setup route documentation
