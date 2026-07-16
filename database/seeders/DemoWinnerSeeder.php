<?php

namespace Database\Seeders;

use App\Models\Deposit;
use App\Models\Document;
use App\Models\Message;
use App\Models\PaymentMethod;
use App\Models\ShopOrder;
use App\Models\ShopProduct;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Services\CodeGenerator;
use Illuminate\Database\Seeder;

class DemoWinnerSeeder extends Seeder
{
    public function run(): void
    {
        $codeGen = app(CodeGenerator::class);

        // ── 1. Seed payment methods & shop products (once) ────────────
        $this->seedBaseData();

        $pmCard = PaymentMethod::where('slug', 'card')->first();
        $pmPaypal = PaymentMethod::where('slug', 'paypal')->first();
        $pmBank = PaymentMethod::where('slug', 'bank')->first();
        $products = ShopProduct::all();

        // ── 2. Ten demo winners ───────────────────────────────────────
        $demos = [
            ['John', 'Demo', '123 Winners Circle', 'Prize City', 'CA', '90210', 'john.demo@example.com', 50000.00, 'Grand Prize Sweepstakes — $50,000 Cash', true, 'approved'],
            ['Sarah', 'Johnson', '456 Victory Ave', 'Los Angeles', 'CA', '90001', 'sarah.j@example.com', 10000.00, 'Second Place — $10,000 Cash', true, 'approved'],
            ['Michael', 'Chen', '789 Luck St', 'San Francisco', 'CA', '94102', 'michael.chen@example.com', 250000.00, 'Mega Jackpot Winner — $250,000', false, 'new'],
            ['Patricia', 'Williams', '321 Fortune Blvd', 'New York', 'NY', '10001', 'patricia.w@example.com', 75000.00, 'Lucky Draw Winner — $75,000', true, 'approved'],
            ['Robert', 'Davis', '654 Prize Ln', 'Chicago', 'IL', '60601', 'robert.davis@example.com', 5000.00, 'Weekly Bonus Winner — $5,000', true, 'processing'],
            ['Linda', 'Martinez', '987 Gold Rd', 'Houston', 'TX', '77001', 'linda.m@example.com', 150000.00, 'Super Prize Winner — $150,000', false, 'new'],
            ['James', 'Brown', '111 Cash Ct', 'Phoenix', 'AZ', '85001', 'james.brown@example.com', 25000.00, 'Monthly Draw Winner — $25,000', true, 'approved'],
            ['Barbara', 'Wilson', '222 Treasure Trl', 'Philadelphia', 'PA', '19101', 'barbara.w@example.com', 100000.00, 'Grand Draw Winner — $100,000', true, 'approved'],
            ['Charles', 'Taylor', '333 Jackpot Dr', 'San Antonio', 'TX', '78201', 'charles.t@example.com', 85000.00, 'Cash Splash Winner — $85,000', false, 'processing'],
            ['Jennifer', 'Anderson', '444 Prize Pkwy', 'San Diego', 'CA', '92101', 'jennifer.a@example.com', 200000.00, 'Millionaire Maker — $200,000', true, 'approved'],
        ];

        foreach ($demos as $i => $data) {
            [$fname, $lname, $addr, $city, $state, $zip, $email, $prize, $desc, $claimed, $status] = $data;

            $winner = \App\Models\Winner::create([
                'first_name' => $fname,
                'last_name' => $lname,
                'address' => $addr,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
                'email' => $email,
                'prize_amount' => $prize,
                'prize_description' => $desc,
                'unique_code' => $codeGen->generateUniqueCode(),
                'is_claimed' => $claimed,
                'claimed_at' => $claimed ? now()->subDays(14 - $i) : null,
                'is_active' => true,
                'status' => $status,
                'is_demo' => true,
                'next_steps' => "1. Review your documents\n2. Choose a withdrawal method\n3. Start depositing",
                'admin_notes' => "Demo account #" . ($i + 1) . " for testing.",
            ]);

            // Messages (2-3 each)
            Message::create(['winner_id' => $winner->id, 'subject' => 'Welcome to PCH!', 'content' => "Congratulations {$fname}! You are a winner!", 'sent_by_admin' => true, 'sent_by' => 'admin', 'read' => true]);
            Message::create(['winner_id' => $winner->id, 'subject' => 'Documents Needed', 'content' => 'Please submit your verification documents to proceed.', 'sent_by_admin' => true, 'sent_by' => 'admin', 'read' => $claimed]);
            if ($i % 2 === 0) {
                Message::create(['winner_id' => $winner->id, 'subject' => 'Quick Question', 'content' => 'How do I check my balance?', 'sent_by_admin' => false, 'sent_by' => 'winner', 'read' => true]);
            }

            // Documents (1-2 each)
            Document::create(['winner_id' => $winner->id, 'document_type' => 'government_id', 'custom_type' => 'Government ID', 'file_path' => "documents/{$winner->id}/id.pdf", 'file_name' => 'id.pdf', 'file_size' => 200000, 'mime_type' => 'application/pdf', 'status' => $claimed ? 'verified' : 'submitted', 'submitted_at' => now()->subDays(12 - $i), 'verified_at' => $claimed ? now()->subDays(10 - $i) : null]);
            if ($i < 7) {
                Document::create(['winner_id' => $winner->id, 'document_type' => 'proof_of_address', 'custom_type' => 'Proof of Address', 'file_path' => "documents/{$winner->id}/address.pdf", 'file_name' => 'utility_bill.pdf', 'file_size' => 180000, 'mime_type' => 'application/pdf', 'status' => $claimed ? 'verified' : 'submitted', 'submitted_at' => now()->subDays(11 - $i), 'verified_at' => $claimed ? now()->subDays(9 - $i) : null]);
            }

            // Deposits (1-2 each)
            Deposit::create(['winner_id' => $winner->id, 'payment_method_id' => $pmPaypal->id, 'amount' => 500 + ($i * 100), 'fee' => 0, 'net_amount' => 500 + ($i * 100), 'status' => 'approved', 'approved_at' => now()->subDays(7)]);
            if ($i % 3 !== 0) {
                Deposit::create(['winner_id' => $winner->id, 'payment_method_id' => $pmBank->id, 'amount' => 200 + ($i * 50), 'fee' => 2.50, 'net_amount' => 197.50 + ($i * 50), 'status' => 'pending']);
            }

            // Withdrawals (1-2 each)
            Withdrawal::create(['winner_id' => $winner->id, 'payment_method_id' => $pmPaypal->id, 'amount' => 200 + ($i * 30), 'fee' => 0, 'net_amount' => 200 + ($i * 30), 'account_details' => ['paypal_email' => $email], 'status' => $claimed ? 'completed' : 'pending', 'approved_at' => $claimed ? now()->subDays(4) : null, 'completed_at' => $claimed ? now()->subDays(3) : null]);
            if ($i < 6) {
                Withdrawal::create(['winner_id' => $winner->id, 'payment_method_id' => $pmBank->id, 'amount' => 300 + ($i * 40), 'fee' => 5.00, 'net_amount' => 295 + ($i * 40), 'account_details' => ['bank_name' => 'Test Bank', 'account_name' => "{$fname} {$lname}", 'account_number' => '****' . str_pad((string)($i * 1111), 4, '0', STR_PAD_LEFT), 'routing_number' => '021000021'], 'status' => $claimed ? 'approved' : 'pending', 'approved_at' => $claimed ? now()->subDay() : null]);
            }

            // Transactions (2-3 each)
            Transaction::create(['winner_id' => $winner->id, 'type' => 'deposit', 'amount' => 500 + ($i * 100), 'fee' => 0, 'net_amount' => 500 + ($i * 100), 'payment_method' => 'PayPal', 'reference_type' => 'deposit', 'reference_id' => $winner->deposits()->first()?->id, 'status' => 'completed', 'description' => 'Deposit via PayPal']);
            Transaction::create(['winner_id' => $winner->id, 'type' => 'withdrawal', 'amount' => 200 + ($i * 30), 'fee' => 0, 'net_amount' => 200 + ($i * 30), 'payment_method' => 'PayPal', 'reference_type' => 'withdrawal', 'reference_id' => $winner->withdrawals()->first()?->id, 'status' => 'completed', 'description' => 'Withdrawal to PayPal']);
            Transaction::create(['winner_id' => $winner->id, 'type' => 'prize', 'amount' => $prize, 'fee' => 0, 'net_amount' => $prize, 'status' => 'completed', 'description' => $desc]);

            // Shop order (1 each)
            $itemProduct = $products->random();
            $qty = rand(1, 3);
            ShopOrder::create([
                'customer_name' => "{$fname} {$lname}",
                'customer_email' => $email,
                'address' => $addr,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
                'items' => [['product_id' => $itemProduct->id, 'name' => $itemProduct->name, 'price' => $itemProduct->price, 'quantity' => $qty]],
                'total' => $itemProduct->price * $qty,
                'payment_method' => 'paypal',
                'status' => ['pending', 'processing', 'shipped', 'delivered'][array_rand(['pending', 'processing', 'shipped', 'delivered'])],
            ]);

            $this->command?->line("  ✓ {$fname} {$lname} — code: {$winner->unique_code}");
        }

        $this->command->info("\n10 demo winners created with full data.");
    }

    private function seedBaseData(): void
    {
        PaymentMethod::firstOrCreate(['slug' => 'card'], ['name' => 'Credit/Debit Card', 'type' => 'card', 'purpose' => 'deposit,withdrawal,shop', 'description' => 'Pay securely with your credit or debit card', 'is_active' => true, 'sort_order' => 1]);
        PaymentMethod::firstOrCreate(['slug' => 'paypal'], ['name' => 'PayPal', 'type' => 'paypal', 'purpose' => 'deposit,withdrawal,shop', 'description' => 'Pay with your PayPal account', 'is_active' => true, 'sort_order' => 2]);
        PaymentMethod::firstOrCreate(['slug' => 'bank'], ['name' => 'Bank Transfer', 'type' => 'bank', 'purpose' => 'deposit,withdrawal', 'description' => 'Pay via direct bank transfer', 'is_active' => true, 'sort_order' => 3]);

        ShopProduct::firstOrCreate(['slug' => 'pch-logo-tshirt'], ['name' => 'PCH Logo T-Shirt', 'slug' => 'pch-logo-tshirt', 'description' => 'Official PCH cotton t-shirt.', 'price' => 29.99, 'image' => '/images/products/tshirt.png', 'category' => 'apparel', 'is_active' => true, 'sort_order' => 1]);
        ShopProduct::firstOrCreate(['slug' => 'pch-baseball-cap'], ['name' => 'PCH Baseball Cap', 'slug' => 'pch-baseball-cap', 'description' => 'Premium adjustable baseball cap.', 'price' => 24.99, 'image' => '/images/products/cap.png', 'category' => 'apparel', 'is_active' => true, 'sort_order' => 2]);
        ShopProduct::firstOrCreate(['slug' => 'pch-coffee-mug'], ['name' => 'PCH Coffee Mug', 'slug' => 'pch-coffee-mug', 'description' => '12oz ceramic mug with gold logo.', 'price' => 14.99, 'image' => '/images/products/mug.png', 'category' => 'accessories', 'is_active' => true, 'sort_order' => 3]);
        ShopProduct::firstOrCreate(['slug' => 'pch-victory-hoodie'], ['name' => 'PCH Victory Hoodie', 'slug' => 'pch-victory-hoodie', 'description' => 'Limited edition winner\'s hoodie.', 'price' => 59.99, 'image' => '/images/products/hoodie.png', 'category' => 'apparel', 'is_active' => true, 'sort_order' => 4]);
    }
}
