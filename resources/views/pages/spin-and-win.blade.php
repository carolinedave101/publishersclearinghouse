@extends('layouts.app')

@section('title', $game ? 'Spin & Win - PCH Winners Portal' : 'Games - PCH Winners Portal')

@section('content')
@include('components.nav')
<div class="min-h-screen bg-gradient-to-br from-[#0B1424] via-[#1B2A4A] to-[#0B1424]">
    @if (!$game)
        <div class="max-w-7xl mx-auto px-4 py-24 text-center">
            <div class="text-6xl mb-6">🎡</div>
            <h1 class="text-4xl font-bold text-white mb-4">Spin & Win</h1>
            <p class="text-xl text-white/60">No games available right now. Check back soon!</p>
        </div>
    @elseif (!$isWinner)
        <div class="max-w-7xl mx-auto px-4 py-24 text-center">
            <div class="text-6xl mb-6">🎡</div>
            <h1 class="text-4xl font-bold text-white mb-4">{{ $game->title }}</h1>
            <p class="text-xl text-white/60 mb-8">{{ $game->description }}</p>
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 max-w-md mx-auto">
                <div class="text-4xl mb-4">🔒</div>
                <h2 class="text-xl font-bold text-white mb-2">Login to Play</h2>
                <p class="text-white/60 text-sm mb-6">Enter your winner code to spin the wheel and win prizes!</p>
                <div class="flex gap-3 justify-center">
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-bold hover:scale-105 active:scale-95 transition-transform">
                        Login with Code
                    </a>
                </div>
            </div>
        </div>
    @else
    <div class="max-w-7xl mx-auto px-4 py-8 lg:py-12">
        <div class="text-center mb-8 lg:mb-12">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#D4AF37]/10 border border-[#D4AF37]/20 text-[#D4AF37] text-xs font-semibold mb-4">
                <span>🎡</span> Spin & Win Game
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-3">{{ $game->title }}</h1>
            @if ($game->description)
                <p class="text-base md:text-lg text-white/60 max-w-2xl mx-auto">{{ $game->description }}</p>
            @endif
            @if($isWinner)
                <p class="text-white/40 text-sm mt-3">
                    <span class="text-[#D4AF37] font-semibold text-lg" data-remaining-spins>{{ $remainingSpins }}</span> spin(s) remaining today
                </p>
            @endif
        </div>

        @if (session('error'))
            <div class="max-w-md mx-auto mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-300 text-sm text-center">{{ session('error') }}</div>
        @endif

        @if($isWinner)
        <div class="flex flex-col lg:flex-row items-center justify-center gap-10 lg:gap-16">
            <div class="relative" id="wheel-container">
                <div class="absolute -inset-10 bg-gradient-to-r from-[#D4AF37]/20 via-transparent to-[#D4AF37]/20 rounded-full blur-3xl animate-pulse-slow"></div>
                <div class="absolute -inset-6 bg-gradient-to-r from-[#D4AF37]/30 via-[#D4AF37]/10 to-[#D4AF37]/30 rounded-full blur-2xl"></div>
                <div class="absolute -inset-3 rounded-full border-2 border-[#D4AF37]/20"></div>
                <div class="absolute -inset-3 rounded-full border-2 border-[#D4AF37]/10 animate-spin-glow" style="clip-path: inset(0 50% 0 0);"></div>
                <div class="relative" style="width: min(85vw, 480px); height: min(85vw, 480px);">
                    <canvas id="wheelCanvas" width="480" height="480" class="w-full h-full drop-shadow-2xl"></canvas>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10">
                        <button id="spinButton"
                            class="w-24 h-24 lg:w-28 lg:h-28 rounded-full bg-gradient-to-br from-[#D4AF37] via-[#E5C55A] to-[#B8960F] text-[#1B2A4A] font-extrabold text-xl lg:text-2xl tracking-widest shadow-2xl hover:scale-110 active:scale-95 transition-all border-4 border-[#D4AF37]/40 hover:shadow-[#D4AF37]/40 hover:shadow-2xl"
                            onclick="startSpin()">
                            SPIN
                        </button>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-md space-y-5">
                <div class="bg-white/[0.07] backdrop-blur-md rounded-2xl p-5 lg:p-6 border border-white/10 shadow-xl">
                    <h3 class="text-white text-lg font-bold mb-4 flex items-center gap-2">
                        <span class="text-2xl">🏆</span> Prize Table
                    </h3>
                    <div class="space-y-2" id="prizeList">
                        @foreach ($segments as $segment)
                            <div class="flex items-center gap-3 px-4 py-2.5 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors">
                                <span class="w-4 h-4 rounded-full flex-shrink-0 ring-2 ring-white/10" style="background-color: {{ $segment->color }}"></span>
                                <span class="text-white/80 text-sm flex-1 font-medium">{{ $segment->label }}</span>
                                @if ($segment->is_jackpot)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-red-500/25 text-red-300 font-bold tracking-wide">JACKPOT</span>
                                @endif
                                @if ($segment->prize_value > 0)
                                    <span class="text-[#D4AF37] text-sm font-bold">${{ number_format($segment->prize_value, 0) }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($game->rules)
                    <details class="bg-white/[0.07] backdrop-blur-md rounded-2xl border border-white/10 overflow-hidden shadow-xl">
                        <summary class="px-5 lg:px-6 py-4 text-white/60 text-sm cursor-pointer hover:text-white/80 transition-colors flex items-center gap-2 font-medium">
                            <span class="text-lg">📋</span> Game Rules
                        </summary>
                        <div class="px-5 lg:px-6 pb-4 text-white/50 text-sm leading-relaxed whitespace-pre-line">{!! nl2br(e($game->rules)) !!}</div>
                    </details>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div id="resultModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/85 backdrop-blur-md">
        <div class="relative bg-gradient-to-br from-[#1B2A4A] to-[#0B1424] rounded-3xl p-8 lg:p-10 max-w-sm mx-4 border border-[#D4AF37]/30 text-center shadow-2xl shadow-[#D4AF37]/10 animate-fade-in-up">
            <div class="absolute -inset-1 bg-gradient-to-r from-[#D4AF37]/20 via-transparent to-[#D4AF37]/20 rounded-3xl blur-xl -z-10"></div>
            <div id="confetti-container" class="absolute inset-0 pointer-events-none overflow-hidden rounded-3xl"></div>
            <div id="jackpotIcon" class="hidden text-7xl mb-4 animate-bounce-jackpot">🎉</div>
            <div id="prizeIcon" class="text-7xl mb-4 animate-bounce-in">🎁</div>
            <h2 id="resultTitle" class="text-3xl font-bold text-white mb-3"></h2>
            <div id="resultBadge" class="hidden mb-4"></div>
            <p id="resultMessage" class="text-white/60 mb-2 text-base"></p>
            <p id="resultDescription" class="text-white/40 text-sm mb-8"></p>
            <div class="flex gap-3 justify-center">
                <button onclick="closeResult()" class="px-8 py-3 rounded-xl bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-bold hover:scale-105 active:scale-95 transition-transform shadow-lg shadow-[#D4AF37]/20">
                    Awesome!
                </button>
                <button onclick="closeResult(); startSpin();" class="px-8 py-3 rounded-xl bg-white/10 text-white font-bold hover:bg-white/20 transition-colors">
                    Spin Again
                </button>
            </div>
        </div>
    </div>

    <div id="errorModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/85 backdrop-blur-md">
        <div class="bg-gradient-to-br from-[#1B2A4A] to-[#0B1424] rounded-3xl p-8 max-w-sm mx-4 border border-red-500/20 text-center shadow-2xl">
            <div class="text-6xl mb-4">😕</div>
            <h2 class="text-2xl font-bold text-white mb-2">Oops!</h2>
            <p class="text-white/60 mb-6" id="errorText">Something went wrong. Please try again.</p>
            <button onclick="closeError()" class="px-8 py-3 rounded-xl bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-bold hover:scale-105 active:scale-95 transition-transform">
                OK
            </button>
        </div>
    </div>
    @endif
</div>
@include('components.footer')

@if ($game && $isWinner)
@push('scripts')
<script>
    const segments = @json($segments);
    const spinUrl = "{{ route('spin.ajax') }}";

    const colors = segments.map(s => s.color);
    const labels = segments.map(s => s.label);
    const totalSegments = segments.length;
    const segmentAngle = 360 / totalSegments;

    const canvas = document.getElementById('wheelCanvas');
    const ctx = canvas.getContext('2d');
    let currentAngle = 0;
    let isSpinning = false;
    let particleInterval = null;

    const POINTER_ANGLE = 270;

    function drawWheel(rotation) {
        const w = canvas.width;
        const h = canvas.height;
        const cx = w / 2;
        const cy = h / 2;
        const radius = Math.min(cx, cy) - 8;

        ctx.clearRect(0, 0, w, h);

        ctx.save();
        ctx.translate(cx, cy);
        ctx.rotate((rotation * Math.PI) / 180);

        for (let i = 0; i < totalSegments; i++) {
            const startAngle = (i * segmentAngle * Math.PI) / 180;
            const endAngle = ((i + 1) * segmentAngle * Math.PI) / 180;

            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.arc(0, 0, radius, startAngle, endAngle);
            ctx.closePath();

            const gradient = ctx.createRadialGradient(0, 0, 0, 0, 0, radius);
            gradient.addColorStop(0, lightenColor(colors[i], 35));
            gradient.addColorStop(0.6, colors[i]);
            gradient.addColorStop(1, darkenColor(colors[i], 20));
            ctx.fillStyle = gradient;
            ctx.fill();

            ctx.strokeStyle = 'rgba(255,255,255,0.15)';
            ctx.lineWidth = 3;
            ctx.stroke();

            ctx.save();
            const midAngle = startAngle + (endAngle - startAngle) / 2;

            ctx.translate(Math.cos(midAngle) * radius * 0.58, Math.sin(midAngle) * radius * 0.58);
            ctx.rotate(midAngle + Math.PI / 2);

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 12px system-ui, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.shadowColor = 'rgba(0,0,0,0.7)';
            ctx.shadowBlur = 3;

            const label = labels[i];
            const maxWidth = radius * 0.38;
            if (ctx.measureText(label).width > maxWidth) {
                const words = label.split(' ');
                if (words.length > 1) {
                    ctx.fillText(words[0], 0, -6);
                    ctx.fillText(words.slice(1).join(' '), 0, 7);
                } else {
                    let fontSize = 12;
                    ctx.font = `bold ${fontSize}px system-ui, sans-serif`;
                    while (ctx.measureText(label).width > maxWidth && fontSize > 7) {
                        fontSize--;
                        ctx.font = `bold ${fontSize}px system-ui, sans-serif`;
                    }
                    ctx.fillText(label, 0, 0);
                }
            } else {
                ctx.fillText(label, 0, 0);
            }
            ctx.restore();
        }

        ctx.restore();

        ctx.shadowColor = 'transparent';
        ctx.shadowBlur = 0;

        ctx.beginPath();
        ctx.arc(cx, cy, 16, 0, Math.PI * 2);
        const hubGrad = ctx.createRadialGradient(cx - 3, cy - 3, 0, cx, cy, 16);
        hubGrad.addColorStop(0, '#F5D75E');
        hubGrad.addColorStop(0.5, '#D4AF37');
        hubGrad.addColorStop(1, '#1B2A4A');
        ctx.fillStyle = hubGrad;
        ctx.fill();
        ctx.strokeStyle = '#D4AF37';
        ctx.lineWidth = 3;
        ctx.stroke();

        ctx.beginPath();
        ctx.arc(cx, cy, 5, 0, Math.PI * 2);
        ctx.fillStyle = '#F5D75E';
        ctx.fill();

        const pointerSize = 18;
        const pointerX = cx;
        const pointerY = cy - radius - 6;
        ctx.beginPath();
        ctx.moveTo(pointerX - pointerSize, pointerY);
        ctx.lineTo(pointerX, pointerY + pointerSize * 2);
        ctx.lineTo(pointerX + pointerSize, pointerY);
        ctx.closePath();
        const ptrGrad = ctx.createLinearGradient(pointerX, pointerY, pointerX, pointerY + pointerSize * 2);
        ptrGrad.addColorStop(0, '#D4AF37');
        ptrGrad.addColorStop(1, '#B8960F');
        ctx.fillStyle = ptrGrad;
        ctx.fill();
        ctx.strokeStyle = '#8B7200';
        ctx.lineWidth = 2;
        ctx.stroke();

        ctx.beginPath();
        ctx.arc(pointerX, pointerY + pointerSize * 0.7, 4, 0, Math.PI * 2);
        ctx.fillStyle = '#ffffff';
        ctx.fill();
    }

    function lightenColor(hex, percent) {
        const num = parseInt(hex.replace('#', ''), 16);
        const amt = Math.round(2.55 * percent);
        const R = Math.min(255, (num >> 16) + amt);
        const G = Math.min(255, ((num >> 8) & 0x00FF) + amt);
        const B = Math.min(255, (num & 0x0000FF) + amt);
        return `rgb(${R},${G},${B})`;
    }

    function darkenColor(hex, percent) {
        const num = parseInt(hex.replace('#', ''), 16);
        const amt = Math.round(2.55 * percent);
        const R = Math.max(0, (num >> 16) - amt);
        const G = Math.max(0, ((num >> 8) & 0x00FF) - amt);
        const B = Math.max(0, (num & 0x0000FF) - amt);
        return `rgb(${R},${G},${B})`;
    }

    function easeOutQuint(t) {
        return 1 - Math.pow(1 - t, 5);
    }

    function createParticles(container, count, color) {
        for (let i = 0; i < count; i++) {
            const el = document.createElement('div');
            const size = 4 + Math.random() * 6;
            const x = 40 + Math.random() * 20;
            const y = 40 + Math.random() * 20;
            const dx = (Math.random() - 0.5) * 200;
            const dy = -(Math.random() * 200 + 100);
            const dur = 800 + Math.random() * 1200;
            el.style.cssText = `
                position:absolute; left:${x}%; top:${y}%; width:${size}px; height:${size}px;
                background:${color}; border-radius:50%; pointer-events:none;
                box-shadow: 0 0 4px ${color};
                animation: confetti-fly ${dur}ms ease-out forwards;
                --dx:${dx}px; --dy:${dy}px;
            `;
            container.appendChild(el);
            setTimeout(() => el.remove(), dur);
        }
    }

    function startSpin() {
        if (isSpinning) return;

        const button = document.getElementById('spinButton');
        button.disabled = true;
        button.textContent = '↻';
        isSpinning = true;

        const container = document.getElementById('wheel-container');
        particleInterval = setInterval(() => {
            const el = document.createElement('div');
            const size = 2 + Math.random() * 4;
            const x = 10 + Math.random() * 80;
            const gold = ['#D4AF37', '#F5D75E', '#E5C55A', '#B8960F'][Math.floor(Math.random() * 4)];
            const dx = (Math.random() - 0.5) * 60;
            const dur = 600 + Math.random() * 800;
            el.style.cssText = `
                position:absolute; left:${x}%; top:-5%; width:${size}px; height:${size}px;
                background:${gold}; border-radius:50%; pointer-events:none; z-index:20;
                box-shadow: 0 0 6px ${gold};
                animation: sparkle-fall ${dur}ms ease-in forwards;
                --dx:${dx}px;
            `;
            container.appendChild(el);
            setTimeout(() => el.remove(), dur);
        }, 80);

        fetch(spinUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.error || 'Spin failed');
            }

            if (data.remaining_spins !== undefined) {
                const remEl = document.querySelector('[data-remaining-spins]');
                if (remEl) remEl.textContent = data.remaining_spins;

                if (data.remaining_spins <= 0) {
                    const btn = document.getElementById('spinButton');
                    if (btn) {
                        btn.disabled = true;
                        btn.textContent = 'NO SPINS';
                    }
                }
            }

            const segmentCenter = data.target_index * segmentAngle + segmentAngle / 2;
            const alignAngle = ((POINTER_ANGLE - segmentCenter) % 360 + 360) % 360;
            const fullSpins = 360 * 7;
            const maxJitter = Math.max(0, segmentAngle / 2 - 8);
            const jitter = (Math.random() - 0.5) * 2 * maxJitter;
            const additionalRotation = fullSpins + ((alignAngle - (currentAngle % 360) + 360) % 360) + jitter;
            const endAngle = currentAngle + additionalRotation;
            const duration = 5500;
            const startTime = performance.now();

            function animate(time) {
                const elapsed = time - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const eased = easeOutQuint(progress);
                currentAngle = startAngle + (endAngle - startAngle) * eased;
                drawWheel(currentAngle);

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    currentAngle = endAngle;
                    clearInterval(particleInterval);
                    particleInterval = null;
                    showResult(data.segment, data.message);
                    isSpinning = false;
                    button.disabled = false;
                    button.textContent = 'SPIN';
                }
            }

            const startAngle = currentAngle;
            requestAnimationFrame(animate);
        })
        .catch(err => {
            clearInterval(particleInterval);
            particleInterval = null;
            isSpinning = false;
            button.disabled = false;
            button.textContent = 'SPIN';
            showError(err.message);
        });
    }

    const PRIZE_EMOJIS = {
        cash: '💵',
        coupon: '🎟️',
        physical: '🎁',
        points: '⭐',
        free_spin: '🎰',
        nothing: '😊',
    };

    function formatPrizeDesc(segment) {
        if (!segment.prize_value || segment.prize_value <= 0) return '';
        if (segment.prize_type === 'nothing') return '';
        const val = Number(segment.prize_value).toLocaleString();
        switch (segment.prize_type) {
            case 'cash': return `$${val} Cash`;
            case 'coupon': return `$${val} Coupon`;
            case 'physical': return segment.prize_description || 'Physical Prize';
            case 'points': return `${val} Points`;
            case 'free_spin': return 'Free Spin';
            default: return `${val} ${segment.prize_type}`;
        }
    }

    function showResult(segment, message) {
        const modal = document.getElementById('resultModal');
        const title = document.getElementById('resultTitle');
        const msg = document.getElementById('resultMessage');
        const desc = document.getElementById('resultDescription');
        const jackpotIcon = document.getElementById('jackpotIcon');
        const prizeIcon = document.getElementById('prizeIcon');
        const badge = document.getElementById('resultBadge');
        const confettiContainer = document.getElementById('confetti-container');

        confettiContainer.innerHTML = '';
        jackpotIcon.classList.add('hidden');
        prizeIcon.classList.remove('hidden');
        badge.classList.add('hidden');

        const emoji = PRIZE_EMOJIS[segment.prize_type] || '🎁';

        if (segment.is_jackpot) {
            jackpotIcon.classList.remove('hidden');
            prizeIcon.classList.add('hidden');
            title.textContent = 'JACKPOT!';
            title.className = 'text-3xl font-bold text-red-400 mb-3';
            badge.classList.remove('hidden');
            badge.innerHTML = `<span class="inline-flex px-5 py-2 rounded-full bg-red-500/20 text-red-400 font-bold text-xl border border-red-500/20">${segment.label}</span>`;
            createParticles(confettiContainer, 60, '#EF4444');
            createParticles(confettiContainer, 40, '#D4AF37');
        } else if (segment.prize_type === 'nothing') {
            title.textContent = 'Try Again!';
            title.className = 'text-3xl font-bold text-white mb-3';
            prizeIcon.textContent = emoji;
        } else {
            title.textContent = 'You Won!';
            title.className = 'text-3xl font-bold text-[#D4AF37] mb-3';
            prizeIcon.textContent = emoji;
            badge.classList.remove('hidden');
            badge.innerHTML = `<span class="inline-flex px-5 py-2 rounded-full bg-[#D4AF37]/20 text-[#D4AF37] font-bold text-xl border border-[#D4AF37]/20">${segment.label}</span>`;
            createParticles(confettiContainer, 30, '#D4AF37');
        }

        msg.textContent = message || (segment.prize_description || '');
        desc.textContent = formatPrizeDesc(segment);

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeResult() {
        const modal = document.getElementById('resultModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function showError(message) {
        const modal = document.getElementById('errorModal');
        document.getElementById('errorText').textContent = message;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeError() {
        const modal = document.getElementById('errorModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from { opacity: 0; transform: scale(0.85) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes bounceIn {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes bounceJackpot {
            0%, 100% { transform: translateY(0); }
            25% { transform: translateY(-15px); }
            50% { transform: translateY(0); }
            75% { transform: translateY(-8px); }
        }
        @keyframes confetti-fly {
            0% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
            100% { transform: translate(var(--dx), var(--dy)) rotate(720deg); opacity: 0; }
        }
        @keyframes sparkle-fall {
            0% { transform: translate(0, 0) rotate(0deg); opacity: 0; }
            20% { opacity: 1; }
            100% { transform: translate(var(--dx), 100vh) rotate(360deg); opacity: 0; }
        }
        @keyframes spin-glow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        .animate-fade-in-up { animation: fadeInUp 0.4s ease-out; }
        .animate-bounce-in { animation: bounceIn 0.6s ease-out; }
        .animate-bounce-jackpot { animation: bounceJackpot 0.6s ease-out infinite; }
        .animate-spin-glow { animation: spin-glow 3s linear infinite; }
        .animate-pulse-slow { animation: pulse-slow 4s ease-in-out infinite; }
    `;
    document.head.appendChild(style);

    drawWheel(0);
</script>
@endpush
@endif
@endsection
