<?php

namespace App\Http\Controllers\Web;

use App\Helpers\EmailHelper;
use App\Http\Controllers\Controller;
use App\Mail\AdminOrderNotification;
use App\Mail\OrderConfirmation;
use App\Models\PaymentMethod;
use App\Models\ShopOrder;
use App\Models\ShopProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        $products = ShopProduct::active()->orderBy('sort_order')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'price' => (float) $p->price,
                'category' => $p->category,
                'image' => $p->image ?? '📦',
            ];
        });

        $paymentMethods = PaymentMethod::active()->forShop()->orderBy('sort_order')->get();

        return view('pages.shop', compact('products', 'paymentMethods'));
    }

    public function placeOrder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'zip' => ['required', 'string'],
            'items' => ['required', 'string'],
            'payment_method' => ['required', 'string'],
            'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf', 'max:5120'],
        ]);

        $cartItems = json_decode($data['items'], true);
        if (!is_array($cartItems) || empty($cartItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty.',
            ], 422);
        }

        $productIds = collect($cartItems)->pluck('product_id')->unique()->toArray();
        $dbProducts = ShopProduct::whereIn('id', $productIds)->get()->keyBy('id');

        $validatedItems = [];
        $calculatedTotal = 0;

        foreach ($cartItems as $item) {
            $productId = $item['product_id'] ?? null;
            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $dbProduct = $dbProducts->get($productId);

            if (!$dbProduct || !$dbProduct->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => "Product '{$item['name']}' is no longer available.",
                ], 422);
            }

            $lineTotal = round((float) $dbProduct->price * $quantity, 2);
            $calculatedTotal += $lineTotal;

            $validatedItems[] = [
                'product_id' => $dbProduct->id,
                'name' => $dbProduct->name,
                'price' => (float) $dbProduct->price,
                'quantity' => $quantity,
                'line_total' => $lineTotal,
            ];
        }

        $calculatedTotal = round($calculatedTotal, 2);

        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        $order = ShopOrder::create([
            'customer_name' => $data['name'],
            'customer_email' => $data['email'],
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'zip' => $data['zip'],
            'items' => $validatedItems,
            'total' => $calculatedTotal,
            'status' => 'pending',
            'payment_method' => $data['payment_method'],
            'payment_proof' => $paymentProofPath,
        ]);

        EmailHelper::send(new OrderConfirmation($order), $data['email'], $data['name']);
        EmailHelper::sendAdmin(new AdminOrderNotification($order));

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully!',
            'order_id' => $order->id,
        ]);
    }
}
