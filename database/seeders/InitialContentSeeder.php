<?php

namespace Database\Seeders;

use App\Models\Giveaway;
use App\Models\MembershipTier;
use App\Models\SpinAndWin;
use App\Models\SpinWheelSegment;
use App\Models\ShopProduct;
use App\Models\Page;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InitialContentSeeder extends Seeder
{
    public function run(): void
    {
        $spinAndWin = SpinAndWin::create([
            'title' => 'Spin & Win',
            'slug' => 'spin-and-win',
            'description' => 'Spin the wheel for your chance to win amazing prizes, cash, and more!',
            'rules' => "• You get 3 spins per day\n• Each spin is random — good luck!\n• Prizes vary from cash to free spins\n• Jackpot prizes are rare but life-changing!\n• No purchase necessary",
            'success_message' => 'You won {prize}! Congratulations!',
            'is_active' => true,
            'sort_order' => 1,
            'max_spins_per_day' => 3,
            'cooldown_minutes' => 0,
        ]);

        $segments = [
            ['Try Again', '#6B7280', 'nothing', 0, null, 40, false, 1],
            ['$5 Cash', '#22C55E', 'cash', 5, 'Win $5 cash!', 20, false, 2],
            ['Free Spin', '#3B82F6', 'free_spin', 0, 'Earn an extra free spin!', 15, false, 3],
            ['50 Points', '#A855F7', 'points', 50, 'Earn 50 loyalty points!', 12, false, 4],
            ['$10 Cash', '#EAB308', 'cash', 10, 'Win $10 cash!', 8, false, 5],
            ['PCH Mug', '#F97316', 'physical', 19.99, 'Win an official PCH coffee mug!', 3, false, 6],
            ['JACKPOT!', '#EF4444', 'cash', 10000, 'YOU WON $10,000 CASH!', 1, true, 7],
            ['$100 Cash', '#EC4899', 'cash', 100, 'Win $100 cash!', 1, false, 8],
        ];

        foreach ($segments as $seg) {
            SpinWheelSegment::create([
                'spin_and_win_id' => $spinAndWin->id,
                'label' => $seg[0],
                'color' => $seg[1],
                'prize_type' => $seg[2],
                'prize_value' => $seg[3],
                'prize_description' => $seg[4],
                'weight' => $seg[5],
                'is_jackpot' => $seg[6],
                'sort_order' => $seg[7],
            ]);
        }

        Giveaway::insert([
            [
                'title' => 'Dream Home Makeover',
                'slug' => 'dream-home-makeover',
                'description' => 'Win a complete home makeover worth $250,000! Includes new furniture, renovation, and interior design consultation.',
                'prize' => '$250,000 Home Makeover',
                'prize_value' => 250000,
                'status' => 'active',
                'color' => '#D4AF37',
                'max_entries' => 50000,
                'ends_at' => Carbon::now()->addDays(30),
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Luxury SUV Giveaway',
                'slug' => 'luxury-suv-giveaway',
                'description' => 'Drive away in a brand new luxury SUV! Choose from BMW, Mercedes, or Lexus.',
                'prize' => 'Luxury SUV of Your Choice',
                'prize_value' => 85000,
                'status' => 'active',
                'color' => '#1B2A4A',
                'max_entries' => 35000,
                'ends_at' => Carbon::now()->addDays(45),
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Summer Vacation Package',
                'slug' => 'summer-vacation-package',
                'description' => 'Win an all-inclusive vacation to a tropical paradise! Round-trip airfare, 7-night stay, and $5,000 spending money.',
                'prize' => 'All-Inclusive Tropical Vacation',
                'prize_value' => 25000,
                'status' => 'active',
                'color' => '#2E8B57',
                'max_entries' => 25000,
                'ends_at' => Carbon::now()->addDays(60),
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Mega Cash Prize',
                'slug' => 'mega-cash-prize',
                'description' => 'Win a life-changing cash prize of $1,000,000! No strings attached.',
                'prize' => '$1,000,000 Cash',
                'prize_value' => 1000000,
                'status' => 'upcoming',
                'color' => '#D4AF37',
                'max_entries' => 100000,
                'ends_at' => Carbon::now()->addDays(90),
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Tech Bundle Giveaway',
                'slug' => 'tech-bundle-giveaway',
                'description' => 'Win the ultimate tech bundle including the latest laptop, smartphone, tablet, and smartwatch!',
                'prize' => 'Ultimate Tech Bundle',
                'prize_value' => 15000,
                'status' => 'upcoming',
                'color' => '#4169E1',
                'max_entries' => 20000,
                'ends_at' => Carbon::now()->addDays(75),
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Holiday Grand Prize',
                'slug' => 'holiday-grand-prize',
                'description' => 'Win the ultimate holiday package worth $500,000! Includes a luxury cruise, shopping spree, and more.',
                'prize' => '$500,000 Holiday Package',
                'prize_value' => 500000,
                'status' => 'upcoming',
                'color' => '#DC143C',
                'max_entries' => 75000,
                'ends_at' => Carbon::now()->addDays(120),
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        ShopProduct::insert([
            ['name' => 'PCH Logo T-Shirt', 'slug' => 'pch-logo-tshirt', 'description' => 'Classic cotton t-shirt with embroidered PCH logo.', 'price' => 29.99, 'category' => 'Apparel', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PCH Winners Cap', 'slug' => 'pch-winners-cap', 'description' => 'Premium adjustable cap with gold "Winner" embroidery.', 'price' => 24.99, 'category' => 'Apparel', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gold Medal Keychain', 'slug' => 'gold-medal-keychain', 'description' => 'Commemorative gold-plated keychain for PCH winners.', 'price' => 14.99, 'category' => 'Accessories', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PCH Coffee Mug', 'slug' => 'pch-coffee-mug', 'description' => 'Ceramic mug with gold rim and PCH logo.', 'price' => 19.99, 'category' => 'Lifestyle', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PCH Hoodie', 'slug' => 'pch-hoodie', 'description' => 'Comfortable fleece hoodie with PCH Winners design.', 'price' => 49.99, 'category' => 'Apparel', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PCH Card Game', 'slug' => 'pch-card-game', 'description' => 'Official PCH playing cards featuring winners throughout history.', 'price' => 9.99, 'category' => 'Games', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PCH Board Game', 'slug' => 'pch-board-game', 'description' => 'Family board game where you race to become the next PCH winner!', 'price' => 34.99, 'category' => 'Games', 'is_active' => true, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Winner\'s Journal', 'slug' => 'winners-journal', 'description' => 'Leather-bound journal for documenting your winning journey.', 'price' => 22.99, 'category' => 'Lifestyle', 'is_active' => true, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PCH Travel Bag', 'slug' => 'pch-travel-bag', 'description' => 'Durable travel duffel bag with PCH branding.', 'price' => 39.99, 'category' => 'Accessories', 'is_active' => true, 'sort_order' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PCH Pen Set', 'slug' => 'pch-pen-set', 'description' => 'Gold-plated pen set in presentation box.', 'price' => 34.99, 'category' => 'Accessories', 'is_active' => true, 'sort_order' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PCH Phone Case', 'slug' => 'pch-phone-case', 'description' => 'Premium phone case with PCH Winners design.', 'price' => 19.99, 'category' => 'Accessories', 'is_active' => true, 'sort_order' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PCH Puzzle', 'slug' => 'pch-puzzle', 'description' => '1000-piece jigsaw puzzle featuring PCH history.', 'price' => 16.99, 'category' => 'Games', 'is_active' => true, 'sort_order' => 12, 'created_at' => now(), 'updated_at' => now()],
        ]);

        MembershipTier::insert([
            [
                'name' => 'Silver',
                'slug' => 'silver',
                'description' => 'Get started with enhanced access to games, giveaways, and member perks.',
                'price' => 100,
                'features' => json_encode(['Entry to all giveaways', 'Play all free games', 'Weekly newsletter', 'Double giveaway entries', 'Silver member badge', 'Standard support']),
                'badge_color' => '#94A3B8',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'description' => 'Our most popular plan with exclusive perks and priority support.',
                'price' => 500,
                'features' => json_encode(['Everything in Silver', 'Triple giveaway entries', 'Exclusive Gold games', 'Priority support', 'Monthly bonus entries', 'Gold member badge', 'Early access to giveaways']),
                'badge_color' => '#D4AF37',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Platinum',
                'slug' => 'platinum',
                'description' => 'The ultimate VIP experience with maximum winning potential.',
                'price' => 1000,
                'features' => json_encode(['Everything in Gold', '5x giveaway entries', 'VIP-only games', '24/7 priority support', 'Exclusive Platinum events', 'Platinum member badge', 'Personal account manager', 'Cash-back rewards']),
                'badge_color' => '#E5E7EB',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Page::insert([
            [
                'title' => 'About Us',
                'slug' => 'about',
                'content' => '<h2>Welcome to Publishers Clearing House</h2><p>For over 57 years, Publishers Clearing House has been making dreams come true. We\'ve awarded over $500 million in prizes to winners across the country.</p><p>Our mission is to bring joy and excitement to people\'s lives through our contests, sweepstakes, and prize giveaways.</p>',
                'meta_description' => 'Learn about Publishers Clearing House history and mission.',
                'is_published' => true,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h2>Privacy Policy</h2><p>Your privacy is important to us. This policy outlines how we collect, use, and protect your personal information.</p><p>We collect information you provide when entering sweepstakes, making purchases, or signing up for memberships.</p>',
                'meta_description' => 'PCH Privacy Policy',
                'is_published' => true,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => '<h2>Terms of Service</h2><p>By using our website and services, you agree to these terms and conditions.</p><p>All sweepstakes and contests are subject to official rules and regulations.</p>',
                'meta_description' => 'PCH Terms of Service',
                'is_published' => true,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Setting::insert([
            ['key' => 'site_name', 'value' => 'PCH Winners Portal', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_description', 'value' => 'Enter your unique winner code to claim your prize from Publishers Clearing House', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_email', 'value' => 'support@pch.com', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'hero_title', 'value' => 'Could You Be Our Next Winner?', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'hero_subtitle', 'value' => 'Enter your unique winner code below to claim your prize!', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
