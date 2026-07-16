@extends('layouts.app')

@section('title', 'My Dashboard - PCH Winners Portal')

@section('content')
@include('components.nav')

{{-- Quick Navigation --}}
<div class="bg-gradient-to-r from-[#1B2A4A] to-[#0F1A2E] border-b border-[#D4AF37]/10">
    <div class="max-w-6xl mx-auto px-4 py-4">
        <div class="flex items-center gap-2 text-xs text-white/40 mb-3">
            <a href="{{ route('home') }}" class="hover:text-[#D4AF37] transition-colors">Home</a>
            <span>/</span>
            <span class="text-[#D4AF37]">My Dashboard</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('winner.dashboard') }}" class="px-4 py-2 bg-[#D4AF37]/20 text-[#D4AF37] rounded-lg text-sm font-semibold border border-[#D4AF37]/30 transition-all hover:bg-[#D4AF37]/30">
                🏠 Dashboard
            </a>
            @if($winnerConfig['show_messages'])
                <a href="{{ route('winner.dashboard') }}#messages" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    💬 Messages
                </a>
            @endif
            @if($winnerConfig['show_documents'])
                <a href="{{ route('winner.dashboard') }}#documents" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    📄 Documents
                </a>
            @endif
            @if($winnerConfig['show_deposits'])
                <a href="{{ route('winner.deposits') }}" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    📥 Deposit
                </a>
            @endif
            @if($winnerConfig['show_withdrawals'])
                <a href="{{ route('winner.withdrawals') }}" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    💸 Withdraw
                </a>
            @endif
            @if($winnerConfig['show_transactions'])
                <a href="{{ route('winner.transactions') }}" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    📊 Transactions
                </a>
            @endif
            @if($winnerConfig['show_orders'])
                <a href="{{ route('winner.orders') }}" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    📦 My Orders
                </a>
            @endif
            @if($winnerConfig['show_giveaways'])
                <a href="{{ route('giveaways') }}" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    🎁 Giveaways
                </a>
            @endif
            @if($winnerConfig['show_games'])
                <a href="{{ route('games') }}" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    🎡 Spin & Win
                </a>
            @endif
            @if($winnerConfig['show_shop'])
                <a href="{{ route('shop') }}" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    🛍️ Shop
                </a>
            @endif
            @if($winnerConfig['show_memberships'])
                <a href="{{ route('memberships') }}" class="px-4 py-2 bg-white/5 text-white/70 rounded-lg text-sm font-medium border border-white/10 transition-all hover:bg-white/10 hover:text-[#D4AF37] hover:border-[#D4AF37]/30">
                    ⭐ Memberships
                </a>
            @endif
        </div>
    </div>
</div>

<main class="flex-1 bg-gradient-to-b from-[#FFFBF0] to-[#F5F0E0]">
    @if(session('success'))
        <div class="max-w-6xl mx-auto px-4 pt-6">
            <div class="animate-slide-down p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2 shadow-sm">
                <span>✅</span> {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Hero Banner --}}
    @if(!$winner->is_claimed)
        <div class="relative overflow-hidden bg-gradient-to-r from-[#1B2A4A] via-[#2A1F00] to-[#1B2A4A]">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-5 left-10 text-5xl animate-float" style="animation-delay: 0s">🏆</div>
                <div class="absolute top-10 right-20 text-4xl animate-float" style="animation-delay: 1s">💰</div>
                <div class="absolute bottom-10 left-20 text-5xl animate-float" style="animation-delay: 2s">🎉</div>
                <div class="absolute bottom-5 right-10 text-3xl animate-float" style="animation-delay: 0.5s">⭐</div>
            </div>
            <div class="max-w-6xl mx-auto px-4 py-16 md:py-20 text-center relative">
                <div class="inline-block px-5 py-1.5 bg-[#D4AF37]/20 border border-[#D4AF37]/30 rounded-full text-[#D4AF37] text-xs font-semibold mb-5 tracking-widest uppercase animate-fade-in-up">
                    🎯 Prize Unclaimed
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-3 animate-fade-in-up animate-delay-100">
                    Congratulations, {{ $winner->first_name }}!
                </h1>
                <p class="text-white/70 text-lg md:text-xl mb-2 animate-fade-in-up animate-delay-200">
                    You've won a prize worth
                </p>
                <div class="text-5xl md:text-7xl font-bold gold-gradient inline-block mb-6 animate-fade-in-up animate-delay-300">
                    ${{ number_format($winner->prize_amount, 0) }}
                </div>
                @if($winner->prize_description)
                    <p class="text-white/50 text-sm mb-6 animate-fade-in-up animate-delay-300">{{ $winner->prize_description }}</p>
                @endif
                <div class="animate-fade-in-up animate-delay-400">
                    <form action="{{ route('winner.claim') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-10 py-4 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-bold rounded-xl text-lg hover:from-[#C5A55A] hover:to-[#A8850D] transition-all shadow-2xl shadow-[#D4AF37]/30 animate-pulse-gold">
                            🎉 Claim Your Prize Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="relative overflow-hidden bg-gradient-to-r from-[#1B2A4A] via-[#1A3A1A] to-[#1B2A4A]">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-5 left-10 text-5xl animate-float" style="animation-delay: 0s">🎉</div>
                <div class="absolute top-10 right-20 text-4xl animate-float" style="animation-delay: 1s">💰</div>
                <div class="absolute bottom-10 left-20 text-5xl animate-float" style="animation-delay: 2s">🏆</div>
                <div class="absolute bottom-5 right-10 text-3xl animate-float" style="animation-delay: 0.5s">⭐</div>
            </div>
            <div class="max-w-6xl mx-auto px-4 py-16 md:py-20 text-center relative">
                <div class="text-6xl mb-4 animate-bounce">🎉</div>
                <div class="inline-block px-5 py-1.5 bg-green-500/20 border border-green-500/30 rounded-full text-green-400 text-xs font-semibold mb-5 tracking-widest uppercase animate-fade-in-up">
                    ✅ Prize Claimed
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-3 animate-fade-in-up animate-delay-100">
                    Congratulations, {{ $winner->first_name }}!
                </h1>
                <p class="text-white/80 text-lg md:text-xl mb-2 animate-fade-in-up animate-delay-200">
                    Your prize of <span class="text-[#D4AF37] font-bold">${{ number_format($winner->prize_amount, 0) }}</span> has been claimed successfully!
                </p>
                @if($winnerConfig['show_dates'])
                    <p class="text-white/40 text-sm animate-fade-in-up animate-delay-300">
                        Claimed on {{ $winner->claimed_at ? date('F j, Y', strtotime($winner->claimed_at)) : 'N/A' }}
                    </p>
                @endif
            </div>
        </div>
    @endif

    @if($winnerConfig['show_balance_summary'] || $winnerConfig['show_winner_code'])
    {{-- Stats Cards --}}
    <div class="max-w-6xl mx-auto px-4 -mt-8 relative z-10">
        <div class="grid md:grid-cols-3 gap-6">
            @if($winnerConfig['show_balance_summary'])
            <div class="dashboard-stat bg-white rounded-2xl gold-border p-6 animate-fade-in-up">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-xl">💰</div>
                    <span class="text-xs text-gray-400">Prize Amount</span>
                </div>
                <p class="text-3xl font-bold gold-text-gradient">{{ number_format($winner->prize_amount, 0) }}</p>
                @if($winner->prize_description)
                    <p class="text-xs text-gray-400 mt-1">{{ $winner->prize_description }}</p>
                @endif
            </div>
            @endif
            @if($winnerConfig['show_balance_summary'])
            <div class="dashboard-stat bg-white rounded-2xl gold-border p-6 animate-fade-in-up animate-delay-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-xl">📊</div>
                    <span class="text-xs text-gray-400">Status</span>
                </div>
                <p class="text-2xl font-bold text-[#1B2A4A] capitalize">
                    @php
                        $labels = ['new' => 'New', 'review' => 'Under Review', 'docs_needed' => 'Documents Needed', 'processing' => 'Processing', 'approved' => 'Approved', 'delivered' => 'Delivered'];
                        $statusColors = ['new' => 'text-[#D4AF37]', 'review' => 'text-orange-500', 'docs_needed' => 'text-blue-500', 'processing' => 'text-purple-500', 'approved' => 'text-green-500', 'delivered' => 'text-green-600'];
                    @endphp
                    <span class="{{ $statusColors[$winner->status] ?? 'text-gray-500' }}">{{ $labels[$winner->status] ?? $winner->status }}</span>
                </p>
            </div>
            @endif
            @if($winnerConfig['show_winner_code'])
            <div class="dashboard-stat bg-white rounded-2xl gold-border p-6 animate-fade-in-up animate-delay-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-xl">🔑</div>
                    <span class="text-xs text-gray-400">Winner Code</span>
                </div>
                <p class="text-xl font-bold text-[#1B2A4A] font-mono tracking-wider">{{ $winner->unique_code }}</p>
                <button onclick="navigator.clipboard.writeText('{{ $winner->unique_code }}'); this.textContent='Copied!'; setTimeout(()=>this.textContent='Copy', 2000)" class="text-xs text-[#D4AF37] hover:underline mt-1">
                    Copy
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Main Content --}}
    <div class="max-w-6xl mx-auto px-4 py-10">
        {{-- Next Steps --}}
        @if($winnerConfig['show_next_steps'] && $winner->next_steps)
            <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-2xl p-6 mb-8 animate-fade-in-up">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0 text-xl">📋</div>
                    <div>
                        <h3 class="font-bold text-[#1B2A4A] text-lg mb-2">Next Steps</h3>
                        <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $winner->next_steps }}</div>
                    </div>
                </div>
            </div>
        @endif

        @if($winnerConfig['show_quick_actions'])
        {{-- Quick Actions --}}
        <div class="grid md:grid-cols-6 gap-4 mb-10">
            @if($winnerConfig['show_giveaways'])
            <a href="{{ route('giveaways') }}" class="winner-card-hover bg-gradient-to-br from-[#D4AF37]/10 to-amber-50 border border-[#D4AF37]/20 rounded-xl p-5 text-center group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">🎁</div>
                <p class="text-sm font-semibold text-[#1B2A4A]">Enter Giveaways</p>
                <p class="text-xs text-gray-400 mt-1">Win more prizes</p>
            </a>
            @endif
            @if($winnerConfig['show_games'])
            <a href="{{ route('games') }}" class="winner-card-hover bg-gradient-to-br from-[#D4AF37]/10 to-amber-50 border border-[#D4AF37]/20 rounded-xl p-5 text-center group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">🎡</div>
                <p class="text-sm font-semibold text-[#1B2A4A]">Spin & Win</p>
                <p class="text-xs text-gray-400 mt-1">Try your luck!</p>
            </a>
            @endif
            @if($winnerConfig['show_deposits'])
            <a href="{{ route('winner.deposits') }}" class="winner-card-hover bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-5 text-center group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">📥</div>
                <p class="text-sm font-semibold text-[#1B2A4A]">Make a Deposit</p>
                <p class="text-xs text-gray-400 mt-1">Add funds</p>
            </a>
            @endif
            @if($winnerConfig['show_withdrawals'])
            <a href="{{ route('winner.withdrawals') }}" class="winner-card-hover bg-gradient-to-br from-amber-50 to-yellow-50 border border-amber-200 rounded-xl p-5 text-center group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">💸</div>
                <p class="text-sm font-semibold text-[#1B2A4A]">Withdraw Funds</p>
                <p class="text-xs text-gray-400 mt-1">Cash out winnings</p>
            </a>
            @endif
            @if($winnerConfig['show_shop'])
            <a href="{{ route('shop') }}" class="winner-card-hover bg-gradient-to-br from-[#D4AF37]/10 to-amber-50 border border-[#D4AF37]/20 rounded-xl p-5 text-center group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">🛍️</div>
                <p class="text-sm font-semibold text-[#1B2A4A]">Shop PCH Gear</p>
                <p class="text-xs text-gray-400 mt-1">Exclusive merch</p>
            </a>
            @endif
            @if($winnerConfig['show_memberships'])
            <a href="{{ route('memberships') }}" class="winner-card-hover bg-gradient-to-br from-[#D4AF37]/10 to-amber-50 border border-[#D4AF37]/20 rounded-xl p-5 text-center group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">⭐</div>
                <p class="text-sm font-semibold text-[#1B2A4A]">Memberships</p>
                <p class="text-xs text-gray-400 mt-1">VIP benefits</p>
            </a>
            @endif
        </div>
        @endif

        @if($winnerConfig['show_messages'] || $winnerConfig['show_documents'])
        {{-- Messages & Documents --}}
        <div class="grid md:grid-cols-2 gap-6">
            @if($winnerConfig['show_messages'])
            <div id="messages" class="bg-white rounded-2xl gold-border overflow-hidden animate-fade-in-up winner-card-hover">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-[#D4AF37]/5 to-transparent">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-lg">💬</div>
                        <div>
                            <h3 class="font-bold text-[#1B2A4A]">Messages</h3>
                            <p class="text-xs text-gray-400">Chat with admin</p>
                        </div>
                    </div>
                    <span class="text-xs bg-[#D4AF37]/10 text-[#D4AF37] px-3 py-1 rounded-full font-semibold">{{ $winner->messages->count() }}</span>
                </div>
                <div class="p-5 max-h-72 overflow-y-auto border-b border-gray-100">
                    @forelse($winner->messages as $message)
                        <div class="mb-4 pb-4 border-b border-gray-100 last:border-0 last:mb-0 last:pb-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-full {{ $message->sent_by_admin ? 'bg-[#D4AF37]/20 text-[#D4AF37]' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center text-xs font-bold">
                                        {{ $message->sent_by_admin ? 'PCH' : 'W' }}
                                    </span>
                                    <span class="text-xs font-medium {{ $message->sent_by_admin ? 'text-[#D4AF37]' : 'text-gray-500' }}">{{ $message->sent_by_admin ? 'PCH Admin' : 'You' }}</span>
                                </div>
                                @if($winnerConfig['show_dates'])
                                    <span class="text-[10px] text-gray-400">{{ $message->created_at->diffForHumans() }}</span>
                                @endif
                            </div>
                            @if($message->subject)
                                <p class="text-sm font-semibold text-[#1B2A4A] mb-1">{{ $message->subject }}</p>
                            @endif
                            <p class="text-sm text-gray-600">{{ $message->content }}</p>
                            @if(!$message->read && !$message->sent_by_admin)
                                <form action="{{ route('winner.messages.read', $message) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="submit" class="text-xs text-[#D4AF37] font-medium bg-[#D4AF37]/5 px-3 py-1 rounded-lg hover:bg-[#D4AF37]/10 transition-colors">Mark as read</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <div class="text-3xl mb-2">📬</div>
                            <p class="text-sm text-gray-400">No messages yet</p>
                            <p class="text-xs text-gray-300 mt-1">Send a message to admin below</p>
                        </div>
                    @endforelse
                </div>
                {{-- Send Message Form --}}
                <div class="p-5 bg-gradient-to-r from-[#D4AF37]/5 to-transparent">
                    <form action="{{ route('winner.messages.send') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="text" name="subject" placeholder="Subject" required maxlength="255"
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                        <textarea name="content" rows="2" placeholder="Type your message..." required
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all resize-none"></textarea>
                        <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] rounded-xl text-sm font-bold hover:from-[#C5A55A] hover:to-[#A8850D] transition-all shadow-lg shadow-[#D4AF37]/20">
                            📤 Send Message
                        </button>
                    </form>
                </div>
            </div>
            @endif

            @if($winnerConfig['show_documents'])
            <div id="documents" class="bg-white rounded-2xl gold-border overflow-hidden animate-fade-in-up animate-delay-100 winner-card-hover">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-[#D4AF37]/5 to-transparent">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center text-lg">📄</div>
                        <div>
                            <h3 class="font-bold text-[#1B2A4A]">Documents</h3>
                            <p class="text-xs text-gray-400">Upload required documents</p>
                        </div>
                    </div>
                    <span class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-semibold">{{ $winner->documents->count() }}</span>
                </div>
                <div class="p-5 max-h-60 overflow-y-auto border-b border-gray-100">
                    @forelse($winner->documents as $doc)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-sm flex-shrink-0">
                                    @php $icons = ['requested' => '📋', 'submitted' => '🕐', 'verified' => '✅', 'rejected' => '❌']; @endphp
                                    {{ $icons[$doc->status] ?? '📄' }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-[#1B2A4A]">{{ $doc->custom_type ?? ucfirst(str_replace('_', ' ', $doc->document_type)) }}</p>
                                    @if($winnerConfig['show_dates'] && $doc->submitted_at)
                                        <p class="text-xs text-green-600">Submitted {{ $doc->submitted_at->diffForHumans() }}</p>
                                    @endif
                                    @if($doc->file_name)
                                        <p class="text-xs text-gray-400 truncate max-w-[180px]">{{ $doc->file_name }}</p>
                                    @endif
                                </div>
                            </div>
                            @php
                                $statusColors = ['requested' => 'bg-gray-100 text-gray-600', 'submitted' => 'bg-yellow-100 text-yellow-700', 'verified' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700'];
                            @endphp
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0 {{ $statusColors[$doc->status] ?? 'bg-gray-100' }}">
                                {{ ucfirst($doc->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <div class="text-3xl mb-2">📋</div>
                            <p class="text-sm text-gray-400">No documents yet</p>
                            <p class="text-xs text-gray-300 mt-1">Upload a document below</p>
                        </div>
                    @endforelse
                </div>
                {{-- Upload Document Form --}}
                <div class="p-5 bg-gradient-to-r from-[#D4AF37]/5 to-transparent">
                    <form action="{{ route('winner.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <select name="document_type" required
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                            <option value="">Select document type...</option>
                            <option value="government_id">Government ID</option>
                            <option value="proof_of_address">Proof of Address</option>
                            <option value="tax_form_w9">Tax Form W-9</option>
                            <option value="bank_information">Bank Information</option>
                            <option value="signed_agreement">Signed Agreement</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="flex items-center gap-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx"
                                    class="hidden" onchange="this.nextElementSibling.textContent = this.files[0]?.name || 'Choose file'">
                                <span class="block w-full px-3 py-2 rounded-xl border border-gray-200 text-sm text-gray-400 truncate hover:border-[#D4AF37] transition-colors">📎 Choose file (max 10MB)</span>
                            </label>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl text-sm font-bold hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/20">
                            📤 Upload Document
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</main>
@include('components.footer')
@endsection