<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $siteConfig = \App\Models\Setting::getSiteConfig(); @endphp
    <link rel="icon" type="image/png" href="{{ $siteConfig['favicon'] ?? asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ $siteConfig['favicon'] ?? asset('favicon.png') }}">
    <title>@yield('title', ($siteConfig['site_name'] ?? 'PCH Winners Portal') . ' - Publishers Clearing House')</title>
    <meta name="description" content="@yield('description', $siteConfig['site_description'] ?? 'Enter your unique winner code to claim your prize from Publishers Clearing House')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('cart', {
            items: JSON.parse(localStorage.getItem('pch-cart') || '[]'),
            get count() {
                return this.items.reduce((s, i) => s + i.quantity, 0);
            },
            get total() {
                return this.items.reduce((s, i) => s + i.product.price * i.quantity, 0);
            },
            get isEmpty() {
                return this.items.length === 0;
            },
            init() {
                this.$watch('items', () => {
                    localStorage.setItem('pch-cart', JSON.stringify(this.items));
                });
            },
            add(product) {
                const existing = this.items.find(i => i.product.id === product.id);
                if (existing) existing.quantity++;
                else this.items.push({ product, quantity: 1 });
            },
            updateQty(idx, delta) {
                const item = this.items[idx];
                if (!item) return;
                item.quantity += delta;
                if (item.quantity <= 0) this.items.splice(idx, 1);
            },
            remove(idx) {
                this.items.splice(idx, 1);
            },
            clear() {
                this.items = [];
            }
        });
    });
    </script>
    @stack('head')
</head>
<body class="min-h-full flex flex-col font-serif bg-[#f5f0e8] text-[#1a1a2e]">
    @yield('content')
    @stack('scripts')
</body>
</html>
