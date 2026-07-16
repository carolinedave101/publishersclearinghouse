<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        PaymentMethod::create([
            'name' => 'Credit/Debit Card',
            'slug' => 'card',
            'type' => 'card',
            'purpose' => 'deposit,withdrawal,shop',
            'description' => 'Pay securely with your credit or debit card',
            'config' => ['providers' => ['stripe', 'paypal']],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        PaymentMethod::create([
            'name' => 'PayPal',
            'slug' => 'paypal',
            'type' => 'paypal',
            'purpose' => 'deposit,withdrawal,shop',
            'description' => 'Pay with your PayPal account',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        PaymentMethod::create([
            'name' => 'Bank Transfer',
            'slug' => 'bank',
            'type' => 'bank',
            'purpose' => 'deposit,withdrawal',
            'description' => 'Pay via direct bank transfer',
            'instructions' => '<ol><li>Use your bank app to transfer the total amount</li><li>Use reference: PCH-{order_id}</li><li>Upload your payment receipt below</li></ol>',
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}
