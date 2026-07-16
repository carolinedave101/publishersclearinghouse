<div class="max-w-6xl mx-auto px-4 py-16" id="recent-winners">
    <div class="text-center mb-10 animate-fade-in-up">
        <div class="inline-flex items-center gap-2 px-5 py-1.5 bg-[#D4AF37]/10 border border-[#D4AF37]/20 rounded-full text-[#D4AF37] text-xs font-semibold mb-4 tracking-widest uppercase">
            🏆 Recent Winners
        </div>
        <h2 class="text-3xl md:text-4xl font-bold text-[#1B2A4A] mb-3">Our Latest Winners</h2>
        <p class="text-gray-500 max-w-xl mx-auto">Every day, real people win real prizes. Could you be next?</p>
    </div>
    <div id="winners-container" class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="col-span-full text-center py-8 text-gray-400">Loading recent winners...</div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
async function loadRecentWinners() {
    try {
        const res = await fetch('<?php echo e(route("winners.recent")); ?>');
        const winners = await res.json();
        const container = document.getElementById('winners-container');
        if (winners.length === 0) {
            container.innerHTML = '<div class="col-span-full text-center py-8 text-gray-400">No recent winners yet. Be the first!</div>';
            return;
        }
        container.innerHTML = winners.map((w, i) => `
            <div class="winner-card-hover bg-white rounded-xl gold-border p-5 animate-fade-in-up" style="animation-delay: ${i * 100}ms">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#D4AF37] to-amber-500 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-[#D4AF37]/20">
                        ${w.first_name.charAt(0)}${w.last_name.charAt(0)}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-[#1B2A4A] truncate">${w.first_name} ${w.last_name}</p>
                        <p class="text-xs text-gray-400">${w.city}, ${w.state}</p>
                    </div>
                    <span class="text-2xl opacity-20">🏆</span>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-lg font-bold gold-text-gradient">$${Number(w.prize_amount).toLocaleString()}</p>
                        <p class="text-xs text-gray-400 truncate max-w-[180px]">${w.prize_description || 'Prize Awarded'}</p>
                    </div>
                    <span class="text-xs text-green-600 bg-green-50 px-2.5 py-1 rounded-full font-medium border border-green-200">Claimed</span>
                </div>
            </div>
        `).join('');
    } catch (err) {
        document.getElementById('winners-container').innerHTML = '<div class="col-span-full text-center py-8 text-gray-400">Failed to load winners.</div>';
    }
}
loadRecentWinners();
</script>
<?php $__env->stopPush(); ?><?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/components/recent-winners.blade.php ENDPATH**/ ?>