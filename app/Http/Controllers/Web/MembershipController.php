<?php

namespace App\Http\Controllers\Web;

use App\Helpers\EmailHelper;
use App\Http\Controllers\Controller;
use App\Mail\AdminMembershipNotification;
use App\Mail\MembershipConfirmation;
use App\Models\MembershipSubscription;
use App\Models\MembershipTier;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipController extends Controller
{
    public function index(): View
    {
        $tiers = MembershipTier::active()->orderBy('sort_order')->get()->map(function ($t) {
            return [
                'id' => $t->id,
                'name' => $t->name,
                'price' => $t->price == 0 ? '$0' : '$' . number_format($t->price, 2),
                'period' => $t->price == 0 ? 'forever' : 'per month',
                'color' => $t->badge_color ? "from-[{$t->badge_color}] to-[{$t->badge_color}]80" : 'from-gray-500 to-gray-600',
                'features' => $t->features ?? [],
                'highlighted' => $t->name === 'Gold',
                'badge' => $t->name === 'Gold' ? 'Most Popular' : null,
            ];
        });

        $faqs = [
            ['q' => 'Can I cancel anytime?', 'a' => 'Yes, you can cancel your membership at any time. Your benefits will continue until the end of the billing period.'],
            ['q' => 'How do memberships increase my chances?', 'a' => 'Members receive multiplied entries to eligible giveaways, giving you more chances to win compared to non-members.'],
            ['q' => 'What payment methods do you accept?', 'a' => 'We accept all major credit cards, PayPal, and digital wallet payments.'],
            ['q' => 'Are there any hidden fees?', 'a' => 'No hidden fees. The price you see is the price you pay. Cancel anytime with no penalties.'],
        ];

        $paymentMethods = PaymentMethod::active()->orderBy('sort_order')->get();

        return view('pages.memberships', compact('tiers', 'faqs', 'paymentMethods'));
    }

    public function signup(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tier' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'payment' => ['required', 'string'],
        ]);

        $tier = MembershipTier::where('name', $data['tier'])->first();

        $subscription = MembershipSubscription::create([
            'subscriber_name' => $data['name'],
            'subscriber_email' => $data['email'],
            'membership_tier_id' => $tier?->id,
            'status' => 'active',
            'starts_at' => now(),
        ]);

        EmailHelper::send(new MembershipConfirmation($subscription), $data['email'], $data['name']);
        EmailHelper::sendAdmin(new AdminMembershipNotification($subscription));

        return response()->json([
            'success' => true,
            'tier' => $data['tier'],
            'message' => 'Membership signup successful!',
        ]);
    }
}
