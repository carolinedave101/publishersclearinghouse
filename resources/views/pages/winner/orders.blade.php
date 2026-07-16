@extends('layouts.app')

@section('title', 'My Orders - PCH Winners Portal')

@section('content')
@include('components.nav')
<div class="min-h-[calc(100vh-8rem)] bg-gradient-to-br from-[#f5f0e8] to-[#e8e0d0] py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#1B2A4A]">My Orders</h1>
                <p class="text-[#1B2A4A]/60 mt-1">Your purchase history</p>
            </div>
            <a href="{{ route('winner.dashboard') }}" class="px-4 py-2 rounded-xl bg-white border border-[#D4AF37]/20 text-[#1B2A4A] font-medium hover:bg-[#D4AF37]/5 transition-colors text-sm">
                &larr; Back to Dashboard
            </a>
        </div>

        @if ($orders->isEmpty())
            <div class="bg-white rounded-2xl shadow-lg p-12 border border-[#D4AF37]/10 text-center">
                <p class="text-4xl mb-4">🛍️</p>
                <p class="text-lg font-medium text-[#1B2A4A] mb-2">No orders yet</p>
                <p class="text-sm text-[#1B2A4A]/60 mb-6">Start shopping to see your order history here.</p>
                <a href="{{ route('shop') }}" class="inline-block px-6 py-3 bg-[#D4AF37] text-[#1B2A4A] rounded-xl font-bold hover:bg-[#C5A55A] transition-colors">
                    Visit the Shop
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($orders as $order)
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-[#D4AF37]/10">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-[#1B2A4A]">Order #{{ $order->id }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    @if(in_array($order->status, ['completed', 'delivered'])) bg-green-50 text-green-600
                                    @elseif($order->status === 'shipped') bg-blue-50 text-blue-600
                                    @elseif($order->status === 'cancelled') bg-red-50 text-red-600
                                    @else bg-yellow-50 text-yellow-600 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-[#1B2A4A]">${{ number_format($order->total, 2) }}</p>
                        </div>
                        @if($winnerConfig['show_dates'])
                        <p class="text-xs text-[#1B2A4A]/40 mb-3">Placed {{ $order->created_at->format('F j, Y g:i A') }}</p>
                        @endif
                        @if ($order->payment_method)
                            <p class="text-xs text-[#1B2A4A]/60 mb-2">Payment: {{ ucfirst($order->payment_method) }}</p>
                        @endif
                        @if ($order->items)
                            <div class="border-t border-gray-100 pt-3 space-y-2">
                                @foreach ($order->items as $item)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-[#1B2A4A]/70">{{ $item['name'] ?? 'Item' }}
                                            @if (isset($item['quantity'])) &times; {{ $item['quantity'] }} @endif
                                        </span>
                                        @if (isset($item['price']))
                                            <span class="text-[#1B2A4A] font-medium">${{ number_format($item['price'], 2) }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@include('components.footer')
@endsection
