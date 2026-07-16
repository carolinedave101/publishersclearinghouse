<?php $__env->startSection('title', 'Memberships - PCH Winners Portal'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('components.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main class="flex-1"
          x-data="{
            view: 'tiers',
            selectedTier: null,
            signupForm: { tier: '', name: '', email: '', payment: '' },
            signingUp: false,
            signupMessage: '',
            faqOpen: [false, false, false, false],
            tiers: <?php echo e(json_encode($tiers)); ?>,
            paymentMethods: <?php echo e(json_encode($paymentMethods)); ?>,
            showSignupForm(tier) {
                this.selectedTier = tier;
                this.signupForm = { tier: tier.name, name: '', email: '', payment: '' };
                this.view = 'signup';
            },
            showTiers() {
                this.view = 'tiers';
                this.signupMessage = '';
            },
            async submitSignup() {
                if (this.signingUp) return;
                this.signingUp = true;
                try {
                    const data = new URLSearchParams({
                        _token: '<?php echo e(csrf_token()); ?>',
                        tier: this.signupForm.tier,
                        name: this.signupForm.name,
                        email: this.signupForm.email,
                        payment: this.signupForm.payment,
                    });
                    const res = await fetch('<?php echo e(route("memberships.signup")); ?>', {
                        method: 'POST',
                        body: data,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const json = await res.json();
                    if (json.success) {
                        this.signupMessage = 'Welcome to ' + json.tier + '!';
                        this.view = 'success';
                    } else {
                        alert('Signup failed. Please try again.');
                    }
                } catch {
                    alert('Signup failed. Please try again.');
                } finally {
                    this.signingUp = false;
                }
            },
            toggleFaq(i) {
                this.faqOpen[i] = !this.faqOpen[i];
            }
          }">
        <div class="bg-gradient-to-br from-[#1B2A4A] via-[#2A1F00] to-[#1B2A4A] text-white border-b border-[#D4AF37]/20">
            <div class="max-w-4xl mx-auto px-4 py-16 md:py-24 text-center">
                <div class="inline-flex items-center gap-2 px-5 py-1.5 bg-[#D4AF37]/20 border border-[#D4AF37]/30 rounded-full text-[#D4AF37] text-xs font-semibold mb-5 tracking-widest uppercase winner-glow">
                    ⭐ Membership Program
                </div>
                <h1 class="text-3xl md:text-5xl font-bold mb-4">Unlock More Ways to Win</h1>
                <p class="text-white/60 text-lg max-w-2xl mx-auto">Join the PCH membership program for exclusive access, multiplied entries, and special member-only prizes.</p>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 -mt-10 pb-16">
            <div x-show="view === 'tiers'">
                <div class="grid md:grid-cols-3 gap-6 mb-16">
                    <template x-for="(tier, i) in tiers" :key="i">
                        <div class="bg-white rounded-2xl shadow-lg border-2 overflow-hidden transition-all hover:shadow-xl"
                             :class="tier.highlighted ? 'border-[#D4AF37] scale-105 md:scale-110' : 'border-transparent'">
                            <template x-if="tier.badge">
                                <div class="bg-gradient-to-r from-[#D4AF37] to-[#C5A55A] text-[#1B2A4A] text-center text-xs font-bold py-1.5 uppercase tracking-wider" x-text="tier.badge"></div>
                            </template>
                            <div class="p-6 md:p-8">
                                <h3 class="text-lg font-bold text-[#1B2A4A] mb-1" x-text="tier.name"></h3>
                                <div class="mb-4">
                                    <span class="text-3xl font-bold text-[#1B2A4A]" x-text="tier.price"></span>
                                    <span class="text-gray-400 text-sm ml-1" x-text="'/' + tier.period"></span>
                                </div>
                                <ul class="space-y-2.5 mb-6">
                                    <template x-for="feature in tier.features" :key="feature">
                                        <li class="flex items-start gap-2 text-sm text-gray-600">
                                            <span class="mt-0.5" :class="tier.highlighted ? 'text-[#D4AF37]' : 'text-green-500'">✓</span>
                                            <span x-text="feature"></span>
                                        </li>
                                    </template>
                                </ul>
                                <button @click="showSignupForm(tier)"
                                        class="w-full py-2.5 rounded-xl text-sm font-bold transition-all"
                                        :class="tier.highlighted
                                            ? 'bg-gradient-to-r from-[#D4AF37] to-[#B8960F] text-[#1B2A4A] hover:from-[#C5A55A] hover:to-[#A8850D] shadow-lg shadow-[#D4AF37]/20'
                                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                                    <span x-text="tier.price === '$0' ? 'Get Started Free' : 'Subscribe Now'"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="max-w-3xl mx-auto">
                    <h2 class="text-2xl font-bold text-[#1B2A4A] text-center mb-6">Frequently Asked Questions</h2>
                    <div class="space-y-3">
                        <template x-for="(faq, i) in <?php echo e(json_encode([
                            ['q' => 'Can I cancel anytime?', 'a' => 'Yes, you can cancel your membership at any time. Your benefits will continue until the end of the billing period.'],
                            ['q' => 'How do memberships increase my chances?', 'a' => 'Members receive multiplied entries to eligible giveaways, giving you more chances to win compared to non-members.'],
                            ['q' => 'What payment methods do you accept?', 'a' => 'We accept all major credit cards, PayPal, and digital wallet payments.'],
                            ['q' => 'Are there any hidden fees?', 'a' => 'No hidden fees. The price you see is the price you pay. Cancel anytime with no penalties.'],
                        ])); ?>" :key="i">
                            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                <button @click="toggleFaq(i)" class="w-full flex items-center justify-between p-4 text-left text-sm font-medium text-[#1B2A4A] hover:bg-gray-50 transition-colors">
                                    <span x-text="faq.q"></span>
                                    <span :class="faqOpen[i] ? 'rotate-180' : ''" class="transition-transform">▼</span>
                                </button>
                                <div x-show="faqOpen[i]" x-cloak class="px-4 pb-4 text-sm text-gray-500 leading-relaxed" x-text="faq.a"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div x-show="view === 'signup'" class="max-w-md mx-auto bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
                <button @click="showTiers()" class="text-sm text-gray-400 hover:text-gray-600 mb-4">← Back to tiers</button>
                <h2 class="text-xl font-bold text-[#1B2A4A] mb-1" x-text="'Join ' + selectedTier?.name"></h2>
                <p class="text-sm text-gray-500 mb-6">Fill in your details to activate your membership.</p>
                <form @submit.prevent="submitSignup()" class="space-y-4">
                    <div>
                        <label class="text-xs text-gray-500 font-medium block mb-1">Full Name</label>
                        <input type="text" x-model="signupForm.name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-medium block mb-1">Email</label>
                        <input type="email" x-model="signupForm.email" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-medium block mb-1">Payment Method</label>
                        <select x-model="signupForm.payment" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-[#D4AF37] outline-none" required>
                            <option value="">Select a method</option>
                            <template x-for="pm in paymentMethods" :key="pm.slug">
                                <option :value="pm.slug" x-text="pm.name"></option>
                            </template>
                        </select>
                    </div>
                    <button type="submit" :disabled="signingUp"
                            class="w-full py-3 bg-gradient-to-r from-[#D4AF37] to-[#C5A55A] text-[#1B2A4A] font-bold rounded-lg text-sm hover:from-[#C5A55A] hover:to-[#B8963E] transition-all disabled:opacity-50">
                        <span x-show="!signingUp">Activate Membership</span>
                        <span x-show="signingUp">Processing...</span>
                    </button>
                    <p class="text-[10px] text-gray-400 text-center">Your membership will activate immediately. Cancel anytime.</p>
                </form>
            </div>

            <div x-show="view === 'success'" class="max-w-md mx-auto bg-white rounded-2xl shadow-lg border border-gray-200 p-8 text-center">
                <p class="text-5xl mb-4">🎉</p>
                <h3 class="text-2xl font-bold text-[#1B2A4A] mb-2" x-text="signupMessage"></h3>
                <p class="text-gray-500 text-sm mb-6">Your membership has been activated.</p>
                <button @click="showTiers()" class="px-6 py-2.5 bg-[#1B2A4A] text-white rounded-lg text-sm font-medium hover:bg-[#243B6A]">View All Tiers</button>
            </div>
        </div>
    </main>
    <?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/og/Desktop/projects/road/publishersclearinghouse/resources/views/pages/memberships.blade.php ENDPATH**/ ?>