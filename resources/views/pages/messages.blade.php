@extends('layouts.app')

@section('title', 'Messages - PCH Winners Portal')

@section('content')
@include('components.nav')
<div class="min-h-[calc(100vh-8rem)] bg-gradient-to-br from-[#f5f0e8] to-[#e8e0d0] py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-[#1B2A4A]">Messages</h1>
            <button onclick="document.getElementById('composeForm').classList.toggle('hidden')"
                class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-semibold hover:scale-105 transition-transform text-sm">
                + New Message
            </button>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        <div id="composeForm" class="hidden mb-8 bg-white rounded-2xl shadow-lg p-8 border border-[#D4AF37]/10">
            <h2 class="text-xl font-bold text-[#1B2A4A] mb-6">Send a Message to Admin</h2>
            <form method="POST" action="{{ route('messages.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Subject</label>
                    <input type="text" name="subject" required maxlength="255"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all">
                    @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1B2A4A]/80 mb-1.5">Message</label>
                    <textarea name="message" required rows="5"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none transition-all"></textarea>
                    @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-gradient-to-r from-[#1B2A4A] to-[#2A3A5A] text-white font-semibold hover:from-[#2A3A5A] hover:to-[#1B2A4A] transition-all">
                    Send Message
                </button>
            </form>
        </div>

        <div class="space-y-4">
            @forelse ($messages as $msg)
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-[#D4AF37]/10 {{ !$msg->is_read && $msg->direction === 'admin_to_user' ? 'ring-2 ring-[#D4AF37]/20' : '' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full {{ $msg->direction === 'admin_to_user' ? 'bg-[#D4AF37]/20 text-[#D4AF37]' : 'bg-[#1B2A4A]/10 text-[#1B2A4A]' }} flex items-center justify-center font-bold text-sm">
                                {{ $msg->direction === 'admin_to_user' ? 'A' : 'U' }}
                            </div>
                            <div>
                                <p class="font-semibold text-[#1B2A4A]">{{ $msg->subject }}</p>
                                <p class="text-xs text-[#1B2A4A]/40">
                                    {{ $msg->direction === 'admin_to_user' ? 'PCH Admin' : 'You' }}
                                    &middot; {{ $msg->created_at->format('M j, Y g:i A') }}
                                </p>
                            </div>
                        </div>
                        @if (!$msg->is_read && $msg->direction === 'admin_to_user')
                            <span class="text-xs px-2 py-1 rounded-full bg-[#D4AF37]/10 text-[#D4AF37] font-medium">New</span>
                        @endif
                    </div>
                    <p class="text-[#1B2A4A]/70 text-sm leading-relaxed whitespace-pre-line">{{ $msg->message }}</p>
                </div>
            @empty
                <div class="text-center py-16">
                    <div class="text-5xl mb-4">💬</div>
                    <h3 class="text-xl font-bold text-[#1B2A4A] mb-2">No Messages Yet</h3>
                    <p class="text-[#1B2A4A]/60">Send a message to admin and they'll get back to you.</p>
                </div>
            @endforelse
        </div>

        @if ($messages->hasPages())
            <div class="mt-8">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@include('components.footer')
@endsection
