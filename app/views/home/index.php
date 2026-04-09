<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= htmlspecialchars($pageTitle) ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          "primary": "#2c694e", "primary-dim": "#1e5d43", "primary-container": "#b1f0ce", "on-primary": "#e1ffec",
          "on-primary-container": "#1d5c42", "secondary": "#126c4a", "secondary-container": "#a1f4c8",
          "on-secondary-container": "#005e3e", "tertiary": "#3f6750", "tertiary-container": "#cbf9db",
          "on-tertiary-container": "#38614a", "surface": "#f7f9ff", "background": "#f7f9ff",
          "surface-container": "#e7eff8", "surface-container-low": "#eff4fc",
          "surface-container-high": "#dfe9f5", "surface-container-highest": "#d7e4f2",
          "surface-container-lowest": "#ffffff", "outline": "#6f7c88", "outline-variant": "#a6b3c1",
          "on-surface": "#28343e", "on-surface-variant": "#54606c",
        },
        fontFamily: { headline: ["Manrope"], body: ["Inter"] }
      }
    }
  }
</script>
<style>
  body { font-family: 'Inter', sans-serif; }
  h1,h2,h3,h4 { font-family: 'Manrope', sans-serif; }
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
  .glass-card { background: rgba(255,255,255,0.8); backdrop-filter: blur(24px); }
</style>
</head>
<body class="bg-background text-on-surface selection:bg-primary-container selection:text-on-primary-container">

<!-- Navbar -->
<header class="fixed top-0 left-0 w-full z-50 bg-background/80 backdrop-blur-xl border-b border-outline-variant/10">
  <div class="flex justify-between items-center px-6 py-4 w-full max-w-7xl mx-auto">
    <div class="flex items-center gap-2">
      <span class="material-symbols-outlined text-primary text-2xl">agriculture</span>
      <span class="font-extrabold text-xl tracking-tight text-[#1B4332]">AgriLink</span>
    </div>
    <nav class="hidden md:flex items-center gap-8">
      <a class="text-on-surface-variant hover:text-primary transition-colors font-medium" href="#how-it-works">How it Works</a>
      <a class="text-on-surface-variant hover:text-primary transition-colors font-medium" href="#features">Features</a>
      <a class="text-on-surface-variant hover:text-primary transition-colors font-medium" href="#about">About</a>
    </nav>
    <div class="flex items-center gap-3">
      <a href="<?= APP_URL ?>/login" class="px-5 py-2 rounded-full border border-outline-variant text-on-surface font-semibold text-sm hover:bg-surface-container transition-colors">Log In</a>
      <a href="<?= APP_URL ?>/register" class="px-5 py-2 rounded-full bg-primary text-on-primary font-bold text-sm hover:opacity-90 transition-opacity shadow-lg shadow-primary/20">Get Started</a>
    </div>
  </div>
</header>

<main class="pt-24">

  <!-- Hero Section -->
  <section class="relative px-6 py-16 md:py-28 max-w-7xl mx-auto overflow-hidden">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
      <div class="lg:col-span-6 z-10">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary-container text-on-primary-container mb-6">
          <span class="material-symbols-outlined text-sm">verified</span>
          <span class="text-[10px] font-bold uppercase tracking-widest">Ghana's #1 Farm-to-Market Platform</span>
        </div>
        <h1 class="text-5xl md:text-6xl font-extrabold text-on-surface leading-[1.1] mb-6">
          Connecting Farmers<br>to <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Better Markets.</span>
        </h1>
        <p class="text-lg text-outline mb-10 max-w-xl leading-relaxed">
          AgriLink brings Ghanaian farmers, buyers, and transporters together on one platform — eliminating middlemen, reducing waste, and growing incomes across all 16 regions.
        </p>
        <div class="flex flex-col sm:flex-row gap-4">
          <a href="<?= APP_URL ?>/register" class="px-8 py-4 rounded-full bg-gradient-to-br from-primary to-primary-dim text-on-primary font-bold flex items-center justify-center gap-2 shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">
            Start Selling Produce
            <span class="material-symbols-outlined">arrow_forward</span>
          </a>
          <a href="<?= APP_URL ?>/register" class="px-8 py-4 rounded-full bg-surface-container-high text-on-surface font-bold hover:bg-surface-container-highest transition-colors flex items-center justify-center gap-2">
            Buy Fresh Produce
          </a>
        </div>
        <!-- Trust stats row -->
        <div class="flex flex-wrap gap-6 mt-10">
          <div><p class="text-2xl font-extrabold text-primary">1,200+</p><p class="text-sm text-outline">Farmers Registered</p></div>
          <div class="w-px bg-outline-variant/30"></div>
          <div><p class="text-2xl font-extrabold text-primary">16</p><p class="text-sm text-outline">Ghana Regions</p></div>
          <div class="w-px bg-outline-variant/30"></div>
          <div><p class="text-2xl font-extrabold text-primary">₵2M+</p><p class="text-sm text-outline">Trade Volume</p></div>
        </div>
      </div>
      <div class="lg:col-span-6 relative">
        <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl bg-surface-container-high aspect-[4/3]">
          <!-- Ghana farm scene representative -->
          <div class="absolute inset-0 bg-gradient-to-br from-primary/30 to-secondary/20 flex items-center justify-center">
            <div class="text-center text-primary">
              <span class="material-symbols-outlined" style="font-size:120px;font-variation-settings:'FILL' 1;opacity:.2">agriculture</span>
            </div>
          </div>
          <!-- Floating stat cards -->
          <div class="absolute top-4 left-4 glass-card p-4 rounded-2xl border border-white/20 shadow-xl">
            <div class="flex items-center gap-3">
              <div class="p-2 bg-primary-container rounded-xl">
                <span class="material-symbols-outlined text-primary">trending_up</span>
              </div>
              <div>
                <p class="text-xs text-outline">Today's Volume</p>
                <p class="font-extrabold text-on-surface">₵48,200</p>
              </div>
            </div>
          </div>
          <div class="absolute bottom-4 right-4 glass-card p-4 rounded-2xl border border-white/20 shadow-xl">
            <div class="flex items-center gap-3">
              <div class="p-2 bg-tertiary-container rounded-xl">
                <span class="material-symbols-outlined text-tertiary">local_shipping</span>
              </div>
              <div>
                <p class="text-xs text-outline">Active Deliveries</p>
                <p class="font-extrabold text-on-surface">34 routes</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- How it Works -->
  <section id="how-it-works" class="bg-surface-container-low py-24 px-6">
    <div class="max-w-7xl mx-auto">
      <div class="text-center mb-16">
        <span class="text-xs font-semibold uppercase tracking-widest text-primary mb-2 block">Simple & Transparent</span>
        <h2 class="text-4xl font-extrabold">How AgriLink Works</h2>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-surface-container-lowest p-8 rounded-[2rem] shadow-sm text-center">
          <div class="mx-auto w-16 h-16 bg-primary-container rounded-full flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings:'FILL' 1">agriculture</span>
          </div>
          <div class="w-8 h-8 rounded-full bg-primary text-on-primary font-extrabold flex items-center justify-center mx-auto mb-4 -mt-2">1</div>
          <h3 class="text-xl font-bold mb-3">Farmers List Produce</h3>
          <p class="text-outline text-sm leading-relaxed">Farmers register and upload their available produce — yam, cassava, cocoa, maize, tomatoes — with pricing, quantity, and location.</p>
        </div>
        <div class="bg-surface-container-lowest p-8 rounded-[2rem] shadow-sm text-center">
          <div class="mx-auto w-16 h-16 bg-secondary-container rounded-full flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-secondary text-3xl" style="font-variation-settings:'FILL' 1">shopping_bag</span>
          </div>
          <div class="w-8 h-8 rounded-full bg-secondary text-on-secondary font-extrabold flex items-center justify-center mx-auto mb-4 -mt-2">2</div>
          <h3 class="text-xl font-bold mb-3">Buyers Browse & Order</h3>
          <p class="text-outline text-sm leading-relaxed">Buyers from Accra, Kumasi, and across Ghana browse listings, compare prices, and place orders or competitive bids directly.</p>
        </div>
        <div class="bg-surface-container-lowest p-8 rounded-[2rem] shadow-sm text-center">
          <div class="mx-auto w-16 h-16 bg-tertiary-container rounded-full flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-tertiary text-3xl" style="font-variation-settings:'FILL' 1">local_shipping</span>
          </div>
          <div class="w-8 h-8 rounded-full bg-tertiary text-on-tertiary font-extrabold flex items-center justify-center mx-auto mb-4 -mt-2">3</div>
          <h3 class="text-xl font-bold mb-3">Transporters Deliver</h3>
          <p class="text-outline text-sm leading-relaxed">Verified transporters accept delivery jobs, track routes in real-time, and earn fair fees — produce moves from farm to table reliably.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Bento -->
  <section id="features" class="py-24 px-6 max-w-7xl mx-auto">
    <div class="mb-16">
      <span class="text-xs font-semibold uppercase tracking-widest text-primary mb-2 block">Built for Ghana</span>
      <h2 class="text-4xl font-extrabold mb-4">Everything You Need to Trade Produce</h2>
      <p class="text-outline max-w-2xl">From real-time pricing to delivery tracking, AgriLink is purpose-built for the Ghanaian agricultural market.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <!-- Large Feature -->
      <div class="md:col-span-2 md:row-span-2 bg-surface-container-lowest p-10 rounded-[2rem] flex flex-col justify-between border border-outline-variant/10 group overflow-hidden relative">
        <div>
          <div class="w-14 h-14 bg-primary rounded-2xl flex items-center justify-center text-on-primary mb-6">
            <span class="material-symbols-outlined text-3xl">hub</span>
          </div>
          <h3 class="text-2xl font-extrabold mb-4">Real-Time Marketplace</h3>
          <p class="text-on-surface-variant leading-relaxed">Browse fresh produce listings from all 16 regions of Ghana. Filter by crop type, region, price, and quantity. Place orders instantly or submit competitive bids.</p>
        </div>
        <div class="mt-10 bg-surface-container-low rounded-2xl p-4 space-y-3">
          <?php foreach (['Yam — Kumasi, Ashanti · ₵150/bag','Cocoa Beans — Sefwi, Western · ₵320/kg','Fresh Tomatoes — Techiman · ₵45/crate'] as $item): ?>
          <div class="flex items-center gap-3 bg-surface-container-lowest rounded-xl p-3">
            <div class="w-8 h-8 bg-primary-container rounded-full flex items-center justify-center">
              <span class="material-symbols-outlined text-primary text-sm">eco</span>
            </div>
            <p class="text-sm font-medium"><?= e($item) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="absolute -right-6 -bottom-6 opacity-[0.04] pointer-events-none">
          <span class="material-symbols-outlined" style="font-size:200px;font-variation-settings:'FILL' 1">store</span>
        </div>
      </div>
      <!-- Feature: Delivery Tracking -->
      <div class="md:col-span-2 bg-primary text-on-primary p-8 rounded-[2rem] flex flex-col justify-center relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-48 h-48 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <h3 class="text-xl font-extrabold mb-2 relative z-10">Live Delivery Tracking</h3>
        <p class="text-primary-container text-sm opacity-90 mb-6 relative z-10">Follow your order from farm to warehouse. Real-time status updates — from pickup through to final delivery.</p>
        <div class="flex items-center gap-3 relative z-10">
          <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2 text-xs"><span class="w-2 h-2 rounded-full bg-primary-container"></span> Pending</div>
            <div class="flex items-center gap-2 text-xs"><span class="w-2 h-2 rounded-full bg-on-primary"></span> In Transit</div>
            <div class="flex items-center gap-2 text-xs"><span class="w-2 h-2 rounded-full bg-tertiary-container"></span> Delivered</div>
          </div>
        </div>
      </div>
      <!-- Feature: Bidding -->
      <div class="bg-surface-container-low p-8 rounded-[2rem] flex flex-col gap-4 border border-outline-variant/10">
        <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings:'FILL' 1">gavel</span>
        <h3 class="text-lg font-bold">Competitive Bidding</h3>
        <p class="text-outline text-sm leading-relaxed">Buyers can submit bids on bulk produce. Farmers choose the best offer.</p>
      </div>
      <!-- Feature: Analytics -->
      <div class="bg-surface-container-low p-8 rounded-[2rem] flex flex-col gap-4 border border-outline-variant/10">
        <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings:'FILL' 1">query_stats</span>
        <h3 class="text-lg font-bold">Supply Analytics</h3>
        <p class="text-outline text-sm leading-relaxed">Dashboards for every user role — revenue, order trends, regional demand maps.</p>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section id="about" class="px-6 py-24">
    <div class="max-w-7xl mx-auto rounded-[2.5rem] bg-[#1B4332] p-12 md:p-20 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-64 h-full opacity-10 pointer-events-none flex items-center justify-end pr-8">
        <span class="material-symbols-outlined text-white" style="font-size:250px;font-variation-settings:'FILL' 1">agriculture</span>
      </div>
      <div class="relative z-10 max-w-2xl">
        <p class="text-primary-container text-sm font-semibold uppercase tracking-widest mb-4">Join the Movement</p>
        <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6">Ready to Grow with AgriLink?</h2>
        <p class="text-slate-300 text-lg mb-10 leading-relaxed">Whether you're a farmer in Ashanti, a buyer in Accra, or a transporter in Takoradi — AgriLink gives you the tools to trade smarter and earn more.</p>
        <div class="flex flex-wrap gap-4">
          <a href="<?= APP_URL ?>/register" class="px-10 py-4 rounded-full bg-primary-container text-on-primary-container font-extrabold hover:scale-105 transition-transform inline-flex items-center gap-2 shadow-lg">
            <span class="material-symbols-outlined">agriculture</span> I'm a Farmer
          </a>
          <a href="<?= APP_URL ?>/register" class="px-10 py-4 rounded-full bg-white/10 text-white font-extrabold backdrop-blur-md hover:bg-white/20 transition-colors inline-flex items-center gap-2">
            <span class="material-symbols-outlined">shopping_bag</span> I'm a Buyer
          </a>
          <a href="<?= APP_URL ?>/register" class="px-10 py-4 rounded-full bg-white/10 text-white font-extrabold backdrop-blur-md hover:bg-white/20 transition-colors inline-flex items-center gap-2">
            <span class="material-symbols-outlined">local_shipping</span> I'm a Transporter
          </a>
        </div>
      </div>
    </div>
  </section>

</main>

<!-- Footer -->
<footer class="w-full py-12 bg-surface-container-low border-t border-outline-variant/10">
  <div class="flex flex-col md:flex-row justify-between items-center px-8 max-w-7xl mx-auto gap-6">
    <div>
      <div class="flex items-center gap-2 mb-2">
        <span class="material-symbols-outlined text-primary">agriculture</span>
        <span class="font-extrabold text-lg text-[#1B4332]">AgriLink</span>
      </div>
      <p class="text-sm text-outline">© <?= date('Y') ?> AgriLink Ghana. Connecting farms, markets, and communities.</p>
    </div>
    <nav class="flex gap-8">
      <a href="<?= APP_URL ?>/login"    class="text-outline text-sm hover:text-primary transition-colors">Login</a>
      <a href="<?= APP_URL ?>/register" class="text-outline text-sm hover:text-primary transition-colors">Register</a>
    </nav>
  </div>
</footer>

</body>
</html>
