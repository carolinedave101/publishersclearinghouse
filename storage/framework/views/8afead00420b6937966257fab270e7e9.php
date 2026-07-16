<div class="bg-gradient-to-r from-[#1B2A4A] to-[#0F1A2E] py-14 border-t border-[#D4AF37]/10 border-b border-[#D4AF37]/10">
    <div class="max-w-5xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6" id="stats-container">
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold gold-text-gradient mb-1" id="stat-total-prizes">Loading...</div>
                <div class="text-white/40 text-sm">Total Prizes Awarded</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-white mb-1" id="stat-winners">Loading...</div>
                <div class="text-white/40 text-sm">Happy Winners</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold gold-text-gradient mb-1" id="stat-monthly">Loading...</div>
                <div class="text-white/40 text-sm">Won This Month</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold text-white mb-1">57+</div>
                <div class="text-white/40 text-sm">Years of PCH</div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
async function loadStats() {
    try {
        const res = await fetch('<?php echo e(route("winners.stats")); ?>');
        const stats = await res.json();
        animateNumber('stat-total-prizes', stats.total_prizes, true);
        animateNumber('stat-winners', stats.total_winners);
        animateNumber('stat-monthly', stats.recent_count);
    } catch (err) {
        document.getElementById('stat-total-prizes').textContent = '---';
        document.getElementById('stat-winners').textContent = '---';
        document.getElementById('stat-monthly').textContent = '---';
    }
}
function animateNumber(elId, target, isCurrency = false) {
    const el = document.getElementById(elId);
    if (!el) return;
    let current = 0;
    const increment = Math.max(1, Math.floor(target / 30));
    const interval = setInterval(() => {
        current += increment;
        if (current >= target) { current = target; clearInterval(interval); }
        el.textContent = isCurrency ? '$' + current.toLocaleString() : current.toLocaleString();
    }, 50);
}
loadStats();
</script>
<?php $__env->stopPush(); ?><?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/components/stats-bar.blade.php ENDPATH**/ ?>