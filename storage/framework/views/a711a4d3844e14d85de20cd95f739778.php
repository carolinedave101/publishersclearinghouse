<?php $__env->startSection('title', 'Giveaways - PCH Winners Portal'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('components.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<main class="flex-1">
    <div class="bg-gradient-to-br from-[#1B2A4A] via-[#2A1F00] to-[#1B2A4A] text-white border-b border-[#D4AF37]/20">
        <div class="max-w-4xl mx-auto px-4 py-16 md:py-24 text-center">
            <div class="inline-flex items-center gap-2 px-5 py-1.5 bg-[#D4AF37]/20 border border-[#D4AF37]/30 rounded-full text-[#D4AF37] text-xs font-semibold mb-5 tracking-widest uppercase winner-glow">
                🎁 Active Giveaways
            </div>
            <h1 class="text-3xl md:text-5xl font-bold mb-4">Enter to Win Big</h1>
            <p class="text-white/60 text-lg max-w-2xl mx-auto">Browse our active giveaways and enter for your chance to win life-changing prizes.</p>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 -mt-10 pb-16">
        <div class="flex gap-2 mb-6">
            <button onclick="filterGiveaways('all')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium bg-[#1B2A4A] text-white transition-all" data-filter="all">All</button>
            <button onclick="filterGiveaways('active')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all" data-filter="active">Active</button>
            <button onclick="filterGiveaways('upcoming')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 transition-all" data-filter="upcoming">Upcoming</button>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" id="giveaways-container">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $giveaways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $giveaway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="giveaway-card winner-card-hover bg-white rounded-2xl gold-border overflow-hidden" data-status="<?php echo e($giveaway['status']); ?>" data-id="<?php echo e($giveaway['id']); ?>">
                    <div class="h-32 bg-gradient-to-br from-[#D4AF37]/20 to-amber-50 flex items-center justify-center text-5xl border-b border-[#D4AF37]/10">
                        <?php echo e($giveaway['image']); ?>

                    </div>
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full
                                <?php if($giveaway['status'] === 'active'): ?> bg-green-100 text-green-700
                                <?php elseif($giveaway['status'] === 'upcoming'): ?> bg-blue-100 text-blue-700
                                <?php else: ?> bg-gray-100 text-gray-600 <?php endif; ?>">
                                <?php echo e(ucfirst($giveaway['status'])); ?>

                            </span>
                            <span class="text-sm font-bold gold-text-gradient"><?php echo e($giveaway['prizeValue']); ?></span>
                        </div>
                        <h3 class="font-bold text-[#1B2A4A] text-lg mb-1"><?php echo e($giveaway['title']); ?></h3>
                        <p class="text-sm text-gray-500 mb-3 line-clamp-2"><?php echo e($giveaway['description']); ?></p>
                        <div class="flex items-center justify-between text-xs text-gray-400 mb-3">
                            <span>⏱ Ends in <span class="countdown" data-ends="<?php echo e($giveaway['endsAt']); ?>">--</span></span>
                            <span class="entry-count-label" data-id="<?php echo e($giveaway['id']); ?>"><?php echo e(number_format($giveaway['entries'])); ?>/<?php echo e(number_format($giveaway['maxEntries'])); ?></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 mb-4">
                            <div class="bg-gradient-to-r from-[#D4AF37] to-amber-500 h-2 rounded-full entry-bar" data-id="<?php echo e($giveaway['id']); ?>" style="width: <?php echo e(min(100, $giveaway['maxEntries'] > 0 ? ($giveaway['entries'] / $giveaway['maxEntries']) * 100 : 0)); ?>%"></div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($giveaway['status'] === 'active'): ?>
                            <button onclick="openEntryForm(<?php echo e($giveaway['id']); ?>, '<?php echo e(addslashes($giveaway['title'])); ?>')" class="enter-btn w-full py-2.5 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] rounded-xl text-sm font-bold hover:from-[#C5A55A] hover:to-[#A8850D] transition-all shadow-lg shadow-[#D4AF37]/20">
                                🎯 Enter Now
                            </button>
                        <?php else: ?>
                            <button disabled class="w-full py-2.5 bg-gray-100 text-gray-400 rounded-xl text-sm font-bold cursor-not-allowed">
                                Coming Soon
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</main>


<div id="entryModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md mx-4 w-full p-6 animate-fade-in-up border border-[#D4AF37]/20">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-[#1B2A4A] text-lg" id="modalTitle">Enter Giveaway</h3>
                <p class="text-xs text-gray-400" id="modalSubtitle">Fill in your details to enter</p>
            </div>
            <button onclick="closeEntryForm()" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-gray-200 transition-colors">✕</button>
        </div>
        <form id="entryForm" class="space-y-4">
            <input type="hidden" name="giveaway_id" id="entryGiveawayId">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">First Name</label>
                    <input type="text" name="first_name" id="entryFirstName" required
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Last Name</label>
                    <input type="text" name="last_name" id="entryLastName" required
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                <input type="email" name="email" id="entryEmail" required
                    class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none text-sm transition-all">
            </div>
            <div id="entryError" class="hidden p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm"></div>
            <button type="submit" class="w-full py-3 bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] font-bold rounded-xl hover:from-[#C5A55A] hover:to-[#A8850D] transition-all shadow-lg shadow-[#D4AF37]/20">
                🎯 Enter Now
            </button>
        </form>
    </div>
</div>

<?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach(el => {
        const endsAt = new Date(el.dataset.ends).getTime();
        const now = Date.now();
        const diff = endsAt - now;
        if (diff <= 0) { el.textContent = 'Ended'; return; }
        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        el.textContent = d + 'd ' + h + 'h ' + m + 'm';
    });
}
updateCountdowns();
setInterval(updateCountdowns, 60000);

function filterGiveaways(filter) {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.toggle('bg-[#1B2A4A]', btn.dataset.filter === filter);
        btn.classList.toggle('text-white', btn.dataset.filter === filter);
        btn.classList.toggle('bg-white', btn.dataset.filter !== filter);
        btn.classList.toggle('text-gray-600', btn.dataset.filter !== filter);
        btn.classList.toggle('border', btn.dataset.filter !== filter);
    });
    document.querySelectorAll('.giveaway-card').forEach(card => {
        card.style.display = (filter === 'all' || card.dataset.status === filter) ? '' : 'none';
    });
}

let currentGiveawayId = null;

function openEntryForm(id, title) {
    currentGiveawayId = id;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalSubtitle').textContent = 'Fill in your details to enter';
    document.getElementById('entryGiveawayId').value = id;
    document.getElementById('entryError').classList.add('hidden');
    document.getElementById('entryForm').reset();
    document.getElementById('entryModal').classList.remove('hidden');
    document.getElementById('entryModal').classList.add('flex');
}

function closeEntryForm() {
    document.getElementById('entryModal').classList.add('hidden');
    document.getElementById('entryModal').classList.remove('flex');
    currentGiveawayId = null;
}

document.getElementById('entryForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Entering...';

    const errorEl = document.getElementById('entryError');
    errorEl.classList.add('hidden');

    try {
        const res = await fetch('<?php echo e(url("/giveaways")); ?>/' + currentGiveawayId + '/enter', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                first_name: document.getElementById('entryFirstName').value,
                last_name: document.getElementById('entryLastName').value,
                email: document.getElementById('entryEmail').value,
            }),
        });

        const data = await res.json();

        if (!data.success) {
            errorEl.textContent = data.message || 'Failed to enter giveaway.';
            errorEl.classList.remove('hidden');
            submitBtn.disabled = false;
            submitBtn.textContent = '🎯 Try Again';
            return;
        }

        // Update entry count on the card
        const label = document.querySelector(`.entry-count-label[data-id="${currentGiveawayId}"]`);
        if (label) label.textContent = data.entries.toLocaleString() + '/' + (data.max_entries || '∞').toLocaleString();

        const bar = document.querySelector(`.entry-bar[data-id="${currentGiveawayId}"]`);
        if (bar && data.max_entries > 0) {
            bar.style.width = Math.min(100, (data.entries / data.max_entries) * 100) + '%';
        }

        // Show success
        const btn = document.querySelector(`.giveaway-card[data-id="${currentGiveawayId}"] .enter-btn`);
        if (btn) {
            btn.textContent = '✓ Entered!';
            btn.classList.remove('from-[#D4AF37]', 'to-[#B8960F]', 'hover:from-[#C5A55A]', 'hover:to-[#A8850D]');
            btn.classList.add('from-green-500', 'to-green-600');
            btn.disabled = true;
        }

        closeEntryForm();
        showToast(data.message || 'Successfully entered!');
    } catch (err) {
        errorEl.textContent = 'Network error. Please try again.';
        errorEl.classList.remove('hidden');
        submitBtn.disabled = false;
        submitBtn.textContent = '🎯 Try Again';
    }
});

function showToast(msg) {
    const existing = document.querySelector('.toast-notification');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.className = 'toast-notification fixed bottom-6 right-6 z-50 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl animate-fade-in-up text-sm font-medium';
    toast.textContent = '✅ ' + msg;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/pages/giveaways.blade.php ENDPATH**/ ?>