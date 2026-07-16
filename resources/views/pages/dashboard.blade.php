@extends('layouts.app')

@section('title', 'My Dashboard - PCH Winners Portal')

@section('content')
@include('components.nav')
<div class="min-h-[calc(100vh-8rem)] bg-gradient-to-br from-[#f5f0e8] to-[#e8e0d0] py-12">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#1B2A4A]">Welcome, {{ auth()->user()->name }}!</h1>
                <p class="text-[#1B2A4A]/60 mt-1">Here's your activity overview</p>
            </div>
            <a href="{{ route('profile') }}"
                class="px-4 py-2 rounded-xl bg-white border border-[#D4AF37]/20 text-[#1B2A4A] font-medium hover:bg-[#D4AF37]/5 transition-colors text-sm">
                Edit Profile
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-[#D4AF37]/10 text-center">
                <div class="text-3xl font-bold text-[#1B2A4A]">{{ $spinStats['total'] }}</div>
                <div class="text-sm text-[#1B2A4A]/60 mt-1">Total Spins</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-[#D4AF37]/10 text-center">
                <div class="text-3xl font-bold text-[#D4AF37]">{{ $spinStats['wins'] }}</div>
                <div class="text-sm text-[#1B2A4A]/60 mt-1">Prizes Won</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-[#D4AF37]/10 text-center">
                <div class="text-3xl font-bold text-red-500">{{ $spinStats['jackpots'] }}</div>
                <div class="text-sm text-[#1B2A4A]/60 mt-1">Jackpots Hit</div>
            </div>
            <a href="{{ route('messages') }}" class="bg-white rounded-2xl shadow-lg p-6 border border-[#D4AF37]/10 text-center hover:border-[#D4AF37]/30 transition-colors relative">
                <div class="text-3xl font-bold text-[#1B2A4A]">@if($unreadMessages > 0)<span class="text-[#D4AF37]">{{ $unreadMessages }}</span>@else 0 @endif</div>
                <div class="text-sm text-[#1B2A4A]/60 mt-1">Unread Messages</div>
                @if($unreadMessages > 0)
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                @endif
            </a>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <div class="space-y-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <h2 class="text-xl font-bold text-[#1B2A4A] mb-6">Recent Spins</h2>
                    @if ($recentSpins->isEmpty())
                        <p class="text-[#1B2A4A]/40 text-center py-8">No spins yet. <a href="{{ route('games') }}" class="text-[#D4AF37] hover:underline">Play now!</a></p>
                    @else
                        <div class="space-y-3">
                            @foreach ($recentSpins as $spin)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <span class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $spin->segment?->color ?? '#888' }}"></span>
                                        <div>
                                            <p class="text-sm font-medium text-[#1B2A4A]">{{ $spin->prize_label }}</p>
                                            <p class="text-xs text-[#1B2A4A]/40">{{ $spin->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    @if ($spin->prize_value > 0 && $spin->prize_type !== 'nothing')
                                        <span class="text-sm font-semibold text-[#D4AF37]">
                                            @if ($spin->prize_type === 'cash')
                                                ${{ number_format($spin->prize_value, 0) }}
                                            @else
                                                {{ number_format($spin->prize_value) }} {{ $spin->prize_type }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <h2 class="text-xl font-bold text-[#1B2A4A] mb-6">Your Giveaways</h2>
                    @if ($entries->isEmpty())
                        <p class="text-[#1B2A4A]/40 text-center py-8">No entries yet. <a href="{{ route('giveaways') }}" class="text-[#D4AF37] hover:underline">Browse giveaways!</a></p>
                    @else
                        <div class="space-y-3">
                            @foreach ($entries as $entry)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
                                    <div>
                                        <p class="text-sm font-medium text-[#1B2A4A]">{{ $entry->giveaway?->title ?? 'Giveaway' }}</p>
                                        <p class="text-xs text-[#1B2A4A]/40">{{ $entry->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-full bg-[#D4AF37]/10 text-[#D4AF37] font-medium">Entered</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-[#1B2A4A]">Recent Orders</h2>
                        @if($orderStats['total'] > 0)
                            <a href="{{ route('orders') }}" class="text-sm text-[#D4AF37] hover:underline">View all</a>
                        @endif
                    </div>
                    @if ($orders->isEmpty())
                        <p class="text-[#1B2A4A]/40 text-center py-8">No orders yet. <a href="{{ route('shop') }}" class="text-[#D4AF37] hover:underline">Visit the shop!</a></p>
                    @else
                        <div class="space-y-3">
                            @foreach ($orders as $order)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
                                    <div>
                                        <p class="text-sm font-medium text-[#1B2A4A]">{{ Str::limit($order->items[0]['name'] ?? 'Order', 40) }}</p>
                                        <p class="text-xs text-[#1B2A4A]/40">{{ $order->created_at->format('M j, Y') }} &middot; {{ count($order->items ?? []) }} item(s)</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-[#1B2A4A]">${{ number_format($order->total, 2) }}</p>
                                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                            @if($order->status === 'completed') bg-green-50 text-green-600
                                            @elseif($order->status === 'shipped') bg-blue-50 text-blue-600
                                            @elseif($order->status === 'cancelled') bg-red-50 text-red-600
                                            @else bg-yellow-50 text-yellow-600 @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-[#1B2A4A]">Messages</h2>
                        <a href="{{ route('messages') }}" class="text-sm text-[#D4AF37] hover:underline">View all</a>
                    </div>
                    @if ($recentMessages->isEmpty())
                        <p class="text-[#1B2A4A]/40 text-center py-4">No messages yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($recentMessages as $msg)
                                <div class="p-3 rounded-xl {{ $msg->direction === 'admin_to_user' ? 'bg-[#1B2A4A]/5 border border-[#1B2A4A]/10' : 'bg-gray-50 border border-gray-100' }}">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $msg->direction === 'admin_to_user' ? 'bg-[#D4AF37]/10 text-[#D4AF37]' : 'bg-gray-200 text-gray-600' }}">
                                            {{ $msg->direction === 'admin_to_user' ? 'Admin' : 'You' }}
                                        </span>
                                        <span class="text-xs text-[#1B2A4A]/40">{{ $msg->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm font-medium text-[#1B2A4A]">{{ $msg->subject }}</p>
                                    <p class="text-xs text-[#1B2A4A]/60 mt-1 line-clamp-2">{{ $msg->message }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <h2 class="text-xl font-bold text-[#1B2A4A] mb-6">Winnings</h2>
                    @if ($totalWon > 0)
                        <div class="mb-3 p-4 rounded-xl bg-gradient-to-r from-[#D4AF37]/10 to-yellow-50 border border-[#D4AF37]/20 text-center">
                            <p class="text-sm text-[#1B2A4A]/60">Total Cash Won from Spins</p>
                            <p class="text-2xl font-bold text-[#D4AF37]">${{ number_format($totalWon, 0) }}</p>
                        </div>
                    @endif
                    @if ($winner)
                        <div class="p-4 rounded-xl bg-green-50 border border-green-200">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-lg">🏆</span>
                                <span class="text-sm font-bold text-green-800">Prize Winner</span>
                            </div>
                            <p class="text-sm font-medium text-green-700">{{ $winner->prize_description ?? 'Congratulations!' }}</p>
                            <p class="text-sm text-green-600 font-semibold mt-1">${{ number_format($winner->prize_amount, 2) }}</p>
                            @if ($winner->next_steps)
                                <p class="text-xs text-green-600 mt-2">{{ $winner->next_steps }}</p>
                            @endif
                            <div class="flex gap-2 mt-3">
                                <a href="{{ route('winner.dashboard') }}" class="text-xs px-3 py-1.5 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition-colors">
                                    Winner Dashboard
                                </a>
                            </div>
                        </div>
                    @else
                        <p class="text-[#1B2A4A]/40 text-center py-4">No winnings tied to your account yet.</p>
                    @endif
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-[#1B2A4A]">Membership</h2>
                        @if (!$subscription)
                            <a href="{{ route('memberships') }}" class="text-sm text-[#D4AF37] hover:underline">View plans</a>
                        @endif
                    </div>
                    @if ($subscription)
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-sm font-bold text-[#1B2A4A]">{{ $subscription->tier?->name ?? 'Active' }} Member</p>
                                <p class="text-xs text-[#1B2A4A]/40">Since {{ $subscription->created_at->format('M Y') }}</p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-600 font-medium">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </div>
                        @if ($subscription->ends_at)
                            <p class="text-xs text-[#1B2A4A]/60 mb-3">
                                @if ($subscription->ends_at->isFuture())
                                    Renews {{ $subscription->ends_at->format('M j, Y') }}
                                @else
                                    Expired {{ $subscription->ends_at->format('M j, Y') }}
                                @endif
                            </p>
                        @endif
                        @if ($subscription->tier && is_array($subscription->tier->features))
                            <div class="space-y-1.5">
                                @foreach ($subscription->tier->features as $feature)
                                    <div class="flex items-center gap-2 text-xs text-[#1B2A4A]/70">
                                        <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ $feature }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <p class="text-[#1B2A4A]/40 text-center py-4">No membership yet. Check out our plans for exclusive benefits!</p>
                    @endif
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
                    <h2 class="text-xl font-bold text-[#1B2A4A] mb-3">Quick Stats</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 rounded-xl bg-gray-50 text-center">
                            <p class="text-lg font-bold text-[#1B2A4A]">{{ $entries->count() }}</p>
                            <p class="text-xs text-[#1B2A4A]/60">Giveaway Entries</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 text-center">
                            <p class="text-lg font-bold text-[#1B2A4A]">{{ $orderStats['total'] }}</p>
                            <p class="text-xs text-[#1B2A4A]/60">Orders Placed</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 text-center">
                            <p class="text-lg font-bold text-[#D4AF37]">
                                @if ($orderStats['spent'] > 0)
                                    ${{ number_format($orderStats['spent'], 0) }}
                                @else
                                    $0
                                @endif
                            </p>
                            <p class="text-xs text-[#1B2A4A]/60">Total Spent</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 text-center">
                            <p class="text-lg font-bold text-[#1B2A4A]">
                                {{ $subscription ? ucfirst($subscription->tier?->name ?? 'Active') : 'None' }}
                            </p>
                            <p class="text-xs text-[#1B2A4A]/60">Membership</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('components.footer')
@endsection
